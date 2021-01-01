<?php

/**
* Update user profile details file
*
* This file updates users profile, password and returns response in json
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/


error_reporting(1); // Enable Error Reporting

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\user\UserAccountFunctions;

// Create classes objects
$userAccountFunctions = new UserAccountFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Update details associative array
$switchAccountDetails  = array(FIELD_ACCOUNT_TYPE => "");

// Check for set POST params
if (isset($_POST[FIELD_USER_ID]) && isset($_POST[FIELD_PASSWORD])
&& isset($_POST[FIELD_NEW_ACCOUNT_TYPE])
&& ((isset($_POST[FIELD_FIRST_NAME]) && isset($_POST[FIELD_LAST_NAME]))
|| (isset($_POST[FIELD_BUSINESS_NAME])))
) {

    // Get Values From POST
    $userId         = $_POST[FIELD_USER_ID]     ? $_POST[FIELD_USER_ID]     : '';
    $password       = $_POST[FIELD_PASSWORD]    ? $_POST[FIELD_PASSWORD]    : '';
    $newAccountType = $_POST[FIELD_NEW_ACCOUNT_TYPE] ? $_POST[FIELD_NEW_ACCOUNT_TYPE] : '';

    $firstName      = ""; // First name for convertion to personal account
    $lastName       = ""; // Last name for convertion to personal account
    $businessName   = ""; // Business name for convertion to business account

    // Get user details
    $user = $userAccountFunctions->getUserByUserId($userId);

    // Check for user details
    if ($user !== false) {
        // User details fetched

        $userHash = $user[FIELD_HASH]; // Get users password hash

        // Verify users password
        if (!$userAccountFunctions->verifyPassword($password, $userHash)) {
            // Password verified

            if (isset($_POST[FIELD_FIRST_NAME]) && isset($_POST[FIELD_LAST_NAME])) {
                // Converting to personal account

                // Get firstName and lastName from POST
                $firstName  = $_POST[FIELD_FIRST_NAME]  ? $_POST[FIELD_FIRST_NAME]  : '';
                $lastName   = $_POST[FIELD_LAST_NAME]   ? $_POST[FIELD_LAST_NAME]   : '';

                // Add first and last names to associative array
                $switchAccountDetails[FIELD_FIRST_NAME] = $firstName;
                $switchAccountDetails[FIELD_LAST_NAME]  = $lastName;

            } else if (isset($_POST[FIELD_BUSINESS_NAME])) {
                // Converting to business account

                // Get businessName from POST
                $businessName = $_POST[FIELD_BUSINESS_NAME] ? $_POST[FIELD_BUSINESS_NAME] : '';

                // Add business name to associative array
                $switchAccountDetails[FIELD_BUSINESS_NAME] = $businessName;
            }

            // Add user id and new account type to associative array
            $switchAccountDetails[FIELD_USER_ID]            = $userId;
            $switchAccountDetails[FIELD_NEW_ACCOUNT_TYPE]   = $newAccountType;

            // Switch account type
            $switchAccountType = $userAccountFunctions->switchAccountType($switchAccountDetails);

            // Check for successful switching
            if (($switchAccountType !== null) && ($switchAccountType !== false)) {
                // Account switching successful

                // Set error to true
                $response[KEY_SUCCESS_MESSAGE] = "Account switching successful!";

                // Echo encoded JSON response
                echo json_encode($response);

            } else {
                // Account switching failed

                // Set error to true
                $response[KEY_ERROR]			= true;
                $response[KEY_ERROR_MESSAGE]    = "Account switching failed!!";

                // Echo encoded JSON response
                echo json_encode($response);
            }
        } else {
            // Pasword verification failed

            // Set error to true
            $response[KEY_ERROR]			= true;
            $response[KEY_ERROR_MESSAGE]    = "Incorrect password!";

            // Echo encoded JSON response
            echo json_encode($response);
        }
    }
} else {
    // Missing userId

    // Set error to true
    $response[KEY_ERROR]			= true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);
}

// EOF: switchAccountType.php
