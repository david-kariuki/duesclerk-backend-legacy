<?php

/**
* Send email verification file
* This file generates, sends email verification codes and returns response in json
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/


// Enable Error Reporting
error_reporting(1);

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\user\UserAccountFunctions;
use duesclerk\src\DateTimeFunctions;
use duesclerk\mail\MailFunctions;
use duesclerk\configs\Constants;

// Create Classes Objects
$userAccountFunctions   = new UserAccountFunctions();
$dateTimeFunctions      = new DateTimeFunctions();
$mailFunctions          = new MailFunctions();

// Create Json Response Array And Initialize Error To FALSE
$response = array(KEY_ERROR => false);

// Check for set POST params
if (isset($_POST[FIELD_VERIFICATION_TYPE])
&& (isset($_POST[FIELD_USER_ID]) || isset($_POST[FIELD_EMAIL_ADDRESS]))) {

    // Get Values From POST
    $userId       = ""; // User id
    $emailAddress   = ""; // Users email address
    $user         = array(); // User details array

    // Get verification code from POST params
    $verificationType   = $_POST[FIELD_VERIFICATION_TYPE]   ? $_POST[FIELD_VERIFICATION_TYPE] : '';

    // Check verification code type
    if ($verificationType == KEY_VERIFICATION_TYPE_EMAIL_ACCOUNT) {
        // Verifying email account

        // Check for user id in POST params
        if (isset($_POST[FIELD_USER_ID])) {

            // Get user id from POST params
            $userId = $_POST[FIELD_USER_ID] ? $_POST[FIELD_USER_ID] : '';

            // Get user details
            $user = $userAccountFunctions->getUserByUserId($userId);
        }

    } else if ($verificationType == KEY_VERIFICATION_TYPE_PASSWORD_RESET) {
        // Email verification for password reset

        // Check for email address in POST params
        if (isset($_POST[FIELD_EMAIL_ADDRESS])) {

            // Get email address from POST params
            $emailAddress = $_POST[FIELD_EMAIL_ADDRESS] ? $_POST[FIELD_EMAIL_ADDRESS] : '';

            // Get user details
            $user = $userAccountFunctions->getUserByEmailAddress($emailAddress);
        }
    }


    // Check for user details
    if ($user !== false) {
        // User details fetched

        // Check for email address
        if (empty($emailAddress)) {

            $emailAddress   = $user[FIELD_EMAIL_ADDRESS]; // Get email address from array
        }

        // Check for user id
        if (empty($userId)) {

            $userId = $user[FIELD_USER_ID]; // Get user id from array
        }

        $accountType    = $user[FIELD_ACCOUNT_TYPE];  // Get account type from array
        $name           = ""; // Variable to hold business or persons first name

        // Check account type to get first name or business name
        if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
            // Personal account

            $name = $user[FIELD_FIRST_NAME]; // Get first name from array

        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
            // Business account

            $name = $user[FIELD_BUSINESS_NAME]; // Get business name from array
        }

        // Check if email address and first name are empty
        if ((!empty($emailAddress)) && (!empty($name))) {
            // Email address and first name not empty

            $verificationCode   = ""; // Verification code

            // Check if user had requested for email account verification code earlier
            $checkForOldCode = $mailFunctions->checkForVerificationRequestRecord(
                $userId,
                $verificationType
            );

            // Check for user verification details
            if ($checkForOldCode !== false) {
                // Email verification code exist for Id

                // Get current date and time
                $numericalTimeStamp = $dateTimeFunctions->getDefaultTimeZoneNumericalDateTime();

                // Get old code request time from email verification table
                $requestTime = $checkForOldCode[FIELD_CODE_REQUEST_TIME];

                // Check if request time is empty
                if (!empty($requestTime)) {
                    // Request time not empty

                    // Get absolute time difference from code request time to current time
                    $timeDifference = $dateTimeFunctions->getNumericalTimeDifferenceInHours(
                        $numericalTimeStamp,
                        $requestTime
                    );

                    // Check if code request time exceeds the 1 Hour to expiry time
                    if ($timeDifference > KEY_VERIFICATION_CODE_EXPIRY_TIME) {
                        // Verification code time exceeds 1 hour

                        // Delete old verification code
                        if (!$mailFunctions->deleteEmailVerificationDetails(
                            $userId,
                            $verificationType
                        )) {
                            // Code deletion failed

                            // Set response error to true and add error message
                            $response[KEY_ERROR]            = true;
                            $response[KEY_ERROR_MESSAGE]    = "Old verification code deletion failed!";

                            // Encode and echo Json response
                            echo json_encode($response);
                        }
                    } else {
                        // Old verification code is still valid

                        // Get old verification code
                        $verificationCode = $checkForOldCode[FIELD_VERIFICATION_CODE];
                    }
                }
            }

            // Check if verification code is empty
            if (empty($verificationCode)) {
                // Old verification code not found or expired

                // Generate new verification code
                $generateCode = $mailFunctions->generateNewEmailVerificationCode(
                    $userId,
                    $emailAddress,
                    $verificationType
                );

                if ($generateCode !== false) {
                    // Error false

                    // Get Verification Code
                    $verificationCode = $generateCode[FIELD_VERIFICATION_CODE];

                } else {
                    // Verification code generation failed

                    // Set response error to true and add error message
                    $response[KEY_ERROR]            = true;
                    $response[KEY_ERROR_MESSAGE]    = "Verification code generation failed!";

                    // Encode and echo Json response
                    echo json_encode($response);
                }
            }

            // Check verification type
            if ($verificationType == KEY_VERIFICATION_TYPE_EMAIL_ACCOUNT) {
                // User account email verification

                // Send email account verification mail
                $sendMail = $mailFunctions->sendUserEmailAccountVerificationCodeMail(
                    $name,
                    $emailAddress,
                    $verificationCode
                );

            } else if ($verificationType == KEY_VERIFICATION_TYPE_PASSWORD_RESET) {
                // User password email verification

                // Send password reset email verification
                $sendMail = $mailFunctions->sendUserPasswordResetEmailVerificationCodeMail(
                    $name,
                    $emailAddress,
                    $verificationCode
                );
            }

            // Check If Mail Was Sent
            if ($sendMail !== false) {

                // Set verification code
                $response[KEY_SEND_VERIFICATION_CODE][FIELD_VERIFICATION_CODE] = $verificationCode;

                // Encode and echo Json response
                echo json_encode($response);

            } else {

                // Set response error to true and add error message
                $response[KEY_ERROR]          = true;
                $response[KEY_ERROR_MESSAGE]  = "Verification code not sent";

                // Encode and echo Json response
                echo json_encode($response);
            }
        }
    }
} else {
    // Missing params

    // Set response error to true and add error message
    $response[KEY_ERROR]            = true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Encode and echo json response
    echo json_encode($response);
}

// EOF: SendEmailVerificationCode.php
