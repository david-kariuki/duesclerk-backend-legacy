<?php

/**
* Send verify email address file
* This file verifies email address by comparing stored verification code to that sent on
* mail and returns response in json
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/


// Enable Error Reporting
error_reporting(1);

// Call Required Functions Classes
require_once 'classes/UserAccountFunctions.php';  // User account functions php file
require_once 'classes/DateTimeFunctions.php';       // DateTimeFunctions php class
require_once 'classes/MailFunctions.php';           // MailFunctions php file
require_once 'classes/Keys.php';

// Create Classes Objects
$userAccountFunctions = new UserAccountFunctions();
$dateTimeFunctions      = new DateTimeFunctions();
$mailFunctions          = new MailFunctions();

// Create Json Response Array And Initialize Error o FALSE
$response = array(KEY_ERROR => false);

// Associative array to update email verification status
$updateDetails = array(FIELD_EMAIL_VERIFIED => "false");

// Check for set POST params
if ((isset($_POST[FIELD_VERIFICATION_CODE]) && isset($_POST[FIELD_VERIFICATION_TYPE]))
|| isset($_POST[FIELD_USER_ID]) || isset($_POST[FIELD_EMAIL_ADDRESS])) {

    // Get values from POST params
    $userId           = "";
    $verificationType   = $_POST[FIELD_VERIFICATION_TYPE]   ? $_POST[FIELD_VERIFICATION_TYPE] : '';
    $verificationCode   = $_POST[FIELD_VERIFICATION_CODE]   ? $_POST[FIELD_VERIFICATION_CODE] : '';
    $user             = array(); // User details array
    $check              = array(); // Array to store verification request record response

    // Check verification code type
    if ($verificationType == KEY_VERIFICATION_TYPE_PASSWORD_RESET) {
        // Password reset verification code

        // Check for email address in POST params
        if (isset($_POST[FIELD_EMAIL_ADDRESS])) {

            // Get email address from POST params
            $emailAddress = $_POST[FIELD_EMAIL_ADDRESS] ? $_POST[FIELD_EMAIL_ADDRESS] : '';

            // Get user details
            $user = $userAccountFunctions->getUserByEmailAddress($emailAddress);

            $userId = $user[FIELD_USER_ID]; // Get userId from array
        }

    } else if ($verificationType == KEY_VERIFICATION_TYPE_EMAIL_ACCOUNT) {
        // Email account verification code

        // Check for UserId in POST params
        if (isset($_POST[FIELD_USER_ID])) {

            // Get UserId from POST params
            $userId = $_POST[FIELD_USER_ID] ? $_POST[FIELD_USER_ID] : '';

            // Get user details
            $user = $userAccountFunctions->getUserByUserId($userId);
        }
    }


    // Check if user had requested for email account verification code earlier
    $check = $mailFunctions->checkForVerificationRequestRecord(
        $userId,
        $verificationType
    );

    // Check for user verification details
    if ($check !== false) {
        // Email verification code exist for Id

        // Get current date and time
        $numericalTimeStamp = $dateTimeFunctions->getDefaultTimeZoneNumericalDateTime();

        // Get old code request time from email verification table
        $requestTime = $check[FIELD_CODE_REQUEST_TIME];

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
                    // Code deleted faied

                    // Set response error to true and add error message
                    $response[KEY_ERROR]            = true;
                    $response[KEY_ERROR_MESSAGE]    = "Old verification code deletion failed!";

                    // Encode and echo Json response
                    echo json_encode($response);

                } else {
                    // Verification code expired

                    // Set response error to true and add error message
                    $response[KEY_ERROR]            = true;
                    $response[KEY_ERROR_MESSAGE]    = "Expired code. Click resend to get a new one!";

                    // Encode and echo Json response
                    echo json_encode($response);
                }
            }
        }
    } else {
        // Verification code record not found

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_ERROR_MESSAGE]    = "You have not requested an email verification!";

        // Encode and echo json response
        echo json_encode($response);
    }


    // Verify users verification code
    if ($mailFunctions->verifyEmaiVerificationCode(
        $userId,
        $verificationType,
        $verificationCode
    )) {
        // Verification code matched UserId

        // Check verification code type
        if ($verificationType == KEY_VERIFICATION_TYPE_PASSWORD_RESET) {
            // Password reset verification code verified

            // Set success message
            $response[KEY_EMAIL_VERIFICATION][KEY_SUCCESS_MESSAGE] = "Email address verified!";

            // Encode and echo json response
            echo json_encode($response);

        } else if ($verificationType == KEY_VERIFICATION_TYPE_EMAIL_ACCOUNT) {
            // Email account verification code

            // Delete email verification record
            if ($mailFunctions->deleteEmailVerificationDetails(
                $userId,
                $verificationType
            )) {
                // Email verification details deleted successfully

                $updateDetails[FIELD_EMAIL_VERIFIED] = "true"; // Set email verified value

                // Update email verified field in users table
                $update = $userAccountFunctions->updateUserProfile(
                    $userId,
                    "",
                    $updateDetails
                );

                // Check if update was successful
                if ($update !== false) {
                    // Email verified field update successful

                    // Get user details
                    $user = $userAccountFunctions->getUserByUserId($userId);

                    // Get email verified field value
                    $emailVerified = $user[FIELD_EMAIL_VERIFIED];

                    // Check if email verified value is true
                    if ($emailVerified == "true") {
                        // Email verified field updated successfully

                        // Set success message
                        $response[KEY_EMAIL_VERIFICATION][KEY_SUCCESS_MESSAGE]  = "Your email address has been verified!";

                        // Encode and echo json response
                        echo json_encode($response);
                    }
                } else {
                    // Email verified field update failed

                    // Set response error to true and add error message
                    $response[KEY_ERROR]            = true;
                    $response[KEY_ERROR_MESSAGE]    = "Email address verification failed!";

                    // Encode and echo json response
                    echo json_encode($response);
                }
            } else {
                // Email verification details deletion failed

                // Set response error to true and add error message
                $response[KEY_ERROR]            = true;
                $response[KEY_ERROR_MESSAGE]    = "Email verification details not deleted!";

                // Encode and echo json response
                echo json_encode($response);
            }
        }
    } else {
        //  Wrong verification code passed

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_ERROR_MESSAGE]    = "Your verification code does not exist or has already been used!";

        // Encode and echo json response
        echo json_encode($response);
    }
} else {
    // Missing params

    // Set response error to true and add error message
    $response[KEY_ERROR]            = true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Encode and echo json response
    echo json_encode($response);
}

// EOF: VerifyEmailAddress.php
