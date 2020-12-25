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

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\user\UserAccountFunctions;
use duesclerk\mail\MailFunctions;
use duesclerk\configs\Constants;

// Create Classes Objects
$userAccountFunctions   = new UserAccountFunctions();
$mailFunctions          = new MailFunctions();

// Create Json Response Array And Initialize Error to FALSE
$response = array(KEY_ERROR => false);

// Check for set POST params
if (isset($_POST[FIELD_EMAIL_ADDRESS]) && isset($_POST[FIELD_NEW_PASSWORD])
&& isset($_POST[FIELD_VERIFICATION_TYPE])) {

    // Get Values From POST
    $userId           = "";
    $emailAddress       = $_POST[FIELD_EMAIL_ADDRESS]     ? $_POST[FIELD_EMAIL_ADDRESS]      : '';
    $newPassword        = $_POST[FIELD_NEW_PASSWORD]      ? $_POST[FIELD_NEW_PASSWORD]       : '';
    $verificationType   = $_POST[FIELD_VERIFICATION_TYPE] ? $_POST[FIELD_VERIFICATION_TYPE]  : '';

    // Get user details
    $user = $userAccountFunctions->getUserByEmailAddress($emailAddress);

    // Check for user details
    if ($user !== false) {
        // User details fetched

        // Get first name and email address for mail notification
        $userId       = $user[FIELD_USER_ID]; // Get UserId from array
        $firstName      = $user[FIELD_FIRST_NAME]; // Get FirstName from array
        $emailAddress   = $user[FIELD_EMAIL_ADDRESS]; // Get EmailAddress from array

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

                // Set user id and success message
                $response[KEY_PASSWORD_RESET][FIELD_USER_ID] = $userId;
                $response[KEY_PASSWORD_RESET][KEY_SUCCESS_MESSAGE]  = "Password reset successful!";

                // Encode and echo Json response
                echo json_encode($response);

            } else {
                // Code deletion failed

                // Set response error to true and add error message
                $response[KEY_ERROR]            = true;
                $response[KEY_ERROR_MESSAGE]    = "Verification code deletion failed!";

                // Encode and echo Json response
                echo json_encode($response);
            }
        } else {
            // Password update failed

            // Set response error to true and add error message
            $response[KEY_ERROR]            = true;
            $response[KEY_ERROR_MESSAGE]    = "Password reset failed!";

            // Encode and echo Json response
            echo json_encode($response);
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

// EOF: ResetPassword.php
