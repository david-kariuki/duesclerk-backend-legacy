<?php

/**
* Send verify EmailAddress file
* This file verifies EmailAddress by comparing stored verification code to that sent on
* mail and returns response in json
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/


// Enable error reporting
ini_set('display_errors', 1); // Enable displaying of errors
ini_set('display_startup_errors', 1); // Enable displaying of startup errors
ini_set('log_errors', 1); // Enabke error logging
error_reporting(E_ALL | E_NOTICE | E_STRICT); // eNable all error reporting


// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\user\UserAccountFunctions;
use duesclerk\src\DateTimeFunctions;
use duesclerk\mail\MailFunctions;

// Create classes objects
$userAccountFunctions   = new UserAccountFunctions();
$dateTimeFunctions      = new DateTimeFunctions();
$mailFunctions          = new MailFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Associative array to update email verification status
$updateDetails = array(FIELD_EMAIL_VERIFIED => "false");

// Check for set POST params
if ((isset($_POST[FIELD_VERIFICATION_CODE]) && isset($_POST[FIELD_VERIFICATION_TYPE]))
|| isset($_POST[FIELD_USER_ID]) || isset($_POST[FIELD_EMAIL_ADDRESS])) {

    // Get values from POST params
    $userId             = "";
    $verificationType   = $_POST[FIELD_VERIFICATION_TYPE]   ? $_POST[FIELD_VERIFICATION_TYPE] : '';
    $verificationCode   = $_POST[FIELD_VERIFICATION_CODE]   ? $_POST[FIELD_VERIFICATION_CODE] : '';
    $user               = array(); // User details array
    $check              = array(); // Array to store verification request record response

    // Check verification code type
    if ($verificationType == KEY_VERIFICATION_TYPE_PASSWORD_RESET) {
        // Password reset verification code

        // Check for EmailAddress in POST params
        if (isset($_POST[FIELD_EMAIL_ADDRESS])) {

            // Get EmailAddress from POST params
            $emailAddress = $_POST[FIELD_EMAIL_ADDRESS] ? $_POST[FIELD_EMAIL_ADDRESS] : '';

            // Get user details
            $user = $userAccountFunctions->getUserByEmailAddress($emailAddress);

            $userId = $user[FIELD_USER_ID]; // Get UserId from array
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
        $requestTime = $check[FIELD_VERIFICATION_CODE_REQUEST_TIME];

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

                    // Echo encoded JSON response
                    echo json_encode($response);

                    exit; // Exit script

                } else {
                    // Verification code expired

                    // Set response error to true and add error message
                    $response[KEY_ERROR]            = true;
                    $response[KEY_ERROR_MESSAGE]    = "Expired code. Click resend to get a new one!";

                    // Echo encoded JSON response
                    echo json_encode($response);

                    exit; // Exit script
                }
            }
        }
    } else {
        // Verification code record not found

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_ERROR_MESSAGE]    = "You have not requested an email verification!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script
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

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script

        } else if ($verificationType == KEY_VERIFICATION_TYPE_EMAIL_ACCOUNT) {
            // Email account verification code

            // Delete email verification record
            if ($mailFunctions->deleteEmailVerificationDetails(
                $userId,
                $verificationType
            )) {
                // Email verification details deleted successfully

                $updateDetails[FIELD_EMAIL_VERIFIED] = "true"; // Set EmailVerified value

                // Update EmailVerified field in users table
                $update = $userAccountFunctions->updateUserProfile(
                    $userId,
                    $updateDetails
                );

                // Check if update was successful
                if ($update !== false) {
                    // EmailVerified field update successful

                    // Get user details
                    $user = $userAccountFunctions->getUserByUserId($userId);

                    // Get EmailVerified field value
                    $emailVerified = $user[FIELD_EMAIL_VERIFIED];

                    // Check if EmailVerified value is true
                    if ($emailVerified == "true") {
                        // EmailVerified field updated successfully

                        // Set success message
                        $response[KEY_EMAIL_VERIFICATION][KEY_SUCCESS_MESSAGE]  = "Your email address has been verified!";

                        // Echo encoded JSON response
                        echo json_encode($response);

                        exit; // Exit script
                    }
                } else {
                    // EmailVerified field update failed

                    // Set response error to true and add error message
                    $response[KEY_ERROR]            = true;
                    $response[KEY_ERROR_MESSAGE]    = "Email address verification failed!";

                    // Echo encoded JSON response
                    echo json_encode($response);

                    exit; // Exit script
                }
            } else {
                // Email verification details deletion failed

                // Set response error to true and add error message
                $response[KEY_ERROR]            = true;
                $response[KEY_ERROR_MESSAGE]    = "Email verification details not deleted!";

                // Echo encoded JSON response
                echo json_encode($response);

                exit; // Exit script
            }
        }
    } else {
        //  Wrong verification code passed

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_ERROR_MESSAGE]    = "Your verification code does not exist or has already been used!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script
    }
} else {
    // Missing params

    // Set response error to true and add error message
    $response[KEY_ERROR]            = true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);

    exit; // Exit script
}

// EOF: verifyEmailAddress.php
