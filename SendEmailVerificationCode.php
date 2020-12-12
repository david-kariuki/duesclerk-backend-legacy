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

// Call Required Functions Classes
require_once 'classes/ClientAccountFunctions.php';  // Client account functions php file
require_once 'classes/DateTimeFunctions.php';       // DateTimeFunctions php class
require_once 'classes/MailFunctions.php';           // MailFunctions php file
require_once 'classes/Keys.php';                    // Keys php file


// Create Classes Objects
$clientAccountFunctions = new ClientAccountFunctions();
$dateTimeFunctions      = new DateTimeFunctions();
$mailFunctions          = new MailFunctions();

// Create Json Response Array And Initialize Error To FALSE
$response = array(KEY_ERROR => false);

// Check for set POST params
if (isset($_POST[FIELD_VERIFICATION_TYPE])
&& (isset($_POST[FIELD_CLIENT_ID]) || isset($_POST[FIELD_EMAIL_ADDRESS]))) {

    // Get Values From POST
    $clientId       = ""; // Client id
    $emailAddress   = ""; // Clients email address
    $client         = array(); // Client details array

    // Get verification code from POST params
    $verificationType   = $_POST[FIELD_VERIFICATION_TYPE]   ? $_POST[FIELD_VERIFICATION_TYPE] : '';

    // Check verification code type
    if ($verificationType == KEY_VERIFICATION_TYPE_EMAIL_ACCOUNT) {
        // Verifying email account

        // Check for client id in POST params
        if (isset($_POST[FIELD_CLIENT_ID])) {

            // Get client id from POST params
            $clientId = $_POST[FIELD_CLIENT_ID] ? $_POST[FIELD_CLIENT_ID] : '';

            // Get client details
            $client = $clientAccountFunctions->getClientByClientId($clientId);
        }

    } else if ($verificationType == KEY_VERIFICATION_TYPE_PASSWORD_RESET) {
        // Email verification for password reset

        // Check for email address in POST params
        if (isset($_POST[FIELD_EMAIL_ADDRESS])) {

            // Get email address from POST params
            $emailAddress = $_POST[FIELD_EMAIL_ADDRESS] ? $_POST[FIELD_EMAIL_ADDRESS] : '';

            // Get client details
            $client = $clientAccountFunctions->getClientByEmailAddress($emailAddress);
        }
    }


    // Check for client details
    if ($client !== false) {
        // Client details fetched

        // Check for email address
        if (empty($emailAddress)) {

            $emailAddress   = $client[FIELD_EMAIL_ADDRESS]; // Get email address from array
        }

        // Check for client id
        if (empty($clientId)) {

            $clientId = $client[FIELD_CLIENT_ID]; // Get client id from array
        }

        $accountType    = $client[FIELD_ACCOUNT_TYPE];  // Get account type from array
        $name           = ""; // Variable to hold business or persons first name

        // Check account type to get first name or business name
        if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
            // Personal account

            $name = $client[FIELD_FIRST_NAME]; // Get first name from array

        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
            // Business account

            $name = $client[FIELD_BUSINESS_NAME]; // Get business name from array
        }

        // Check if email address and first name are empty
        if ((!empty($emailAddress)) && (!empty($name))) {
            // Email address and first name not empty

            $verificationCode   = ""; // Verification code

            // Check if client had requested for email account verification code earlier
            $checkForOldCode = $mailFunctions->checkForVerificationRequestRecord(
                $clientId,
                $verificationType
            );

            // Check for client verification details
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
                            $clientId,
                            $verificationType
                        )) {
                            // Code deletion failed

                            // Set response error to true
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
                    $clientId,
                    $emailAddress,
                    $verificationType
                );

                if ($generateCode !== false) {
                    // Error false

                    // Get Verification Code
                    $verificationCode = $generateCode[FIELD_VERIFICATION_CODE];

                } else {
                    // Verification code generation failed

                    // Set response error to true
                    $response[KEY_ERROR]            = true;
                    $response[KEY_ERROR_MESSAGE]    = "Verification code generation failed!";

                    // Encode and echo Json response
                    echo json_encode($response);
                }
            }

            // Check verification type
            if ($verificationType == KEY_VERIFICATION_TYPE_EMAIL_ACCOUNT) {
                // Client account email verification

                // Send email account verification mail
                $sendMail = $mailFunctions->sendClientEmailAccountVerificationCodeMail(
                    $name,
                    $emailAddress,
                    $verificationCode
                );

            } else if ($verificationType == KEY_VERIFICATION_TYPE_PASSWORD_RESET) {
                // Client password email verification

                // Send password reset email verification
                $sendMail = $mailFunctions->sendClientPasswordResetEmailVerificationCodeMail(
                    $name,
                    $emailAddress,
                    $verificationCode
                );
            }

            // Check If Mail Was Sent
            if ($sendMail !== false) {

                // Set Response Error To False
                $response[KEY_ERROR] = false;
                $response[KEY_EMAIL_VERIFICATION][FIELD_VERIFICATION_CODE] = $verificationCode;

                // Encode and echo Json response
                echo json_encode($response);

            } else {

                // Set Response Error To True
                $response[KEY_ERROR]          = true;
                $response[KEY_ERROR_MESSAGE]  = "Verification code not sent";

                // Encode and echo Json response
                echo json_encode($response);
            }
        }
    }
} else {
    // Missing params

    // Set response error to true
    $response[KEY_ERROR]            = true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Encode and echo json response
    echo json_encode($response);
}

// EOF: SendEmailVerificationCode.php
