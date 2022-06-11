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
require_once $_SERVER["DOCUMENT_ROOT"] . "duesclerk_php/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\user\UserAccountFunctions;
use duesclerk\mail\MailFunctions;

// Create classes objects
$userAccountFunctions   = new UserAccountFunctions();
$mailFunctions          = new MailFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Check for set POST params
if (isset($_POST[FIELD_EMAIL_ADDRESS]) && isset($_POST[FIELD_NEW_PASSWORD])
&& isset($_POST[FIELD_VERIFICATION_TYPE])) {

    // Get Values From POST
    $userId             = "";
    $emailAddress       = $_POST[FIELD_EMAIL_ADDRESS]     ? $_POST[FIELD_EMAIL_ADDRESS]      : '';
    $newPassword        = $_POST[FIELD_NEW_PASSWORD]      ? $_POST[FIELD_NEW_PASSWORD]       : '';
    $verificationType   = $_POST[FIELD_VERIFICATION_TYPE] ? $_POST[FIELD_VERIFICATION_TYPE]  : '';

    // Get user details
    $user = $userAccountFunctions->getUserByEmailAddress($emailAddress);

    // Check for user details
    if ($user !== false) {
        // User details fetched

        // Get FullNameOrBusinessName and EmailAddress for mail notification
        $userId = $user[FIELD_USER_ID]; // Get UserId from array

        // Get FullNameOrBusinessName from array
        $fullNameOrBusinessName = $user[FIELD_FULL_NAME_OR_BUSINESS_NAME];
        $emailAddress   = $user[FIELD_EMAIL_ADDRESS];   // Get EmailAddress from array

        // Update password
        $update = $userAccountFunctions->updateUserPassword(
            $userId,
            "",
            $newPassword
        );

        // Check if password update was successful
        if ($update !== false) {
            // Password update successful

            // Delete password update email verification code
            if ($mailFunctions->deleteEmailVerificationDetails(
                $userId,
                $verificationType
            )) {
                // Verification code deleted

                // Set UserId and success message
                $response[KEY_PASSWORD_RESET][FIELD_USER_ID]        = $userId;
                $response[KEY_PASSWORD_RESET][KEY_SUCCESS_MESSAGE]  = "Password reset successful!";

                // Echo encoded JSON response
                echo json_encode($response);

                exit; // Exit script

            } else {
                // Code deletion failed

                // Set response error to true and add error message
                $response[KEY_ERROR]            = true;
                $response[KEY_ERROR_MESSAGE]    = "Verification code deletion failed!";

                // Echo encoded JSON response
                echo json_encode($response);

                exit; // Exit script
            }
        } else {
            // Password update failed

            // Set response error to true and add error message
            $response[KEY_ERROR]            = true;
            $response[KEY_ERROR_MESSAGE]    = "Password reset failed!";

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script
        }
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

// EOF: resetPassword.php
