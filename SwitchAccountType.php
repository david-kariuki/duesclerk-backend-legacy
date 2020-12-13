<?php

/**
* Update user profile details file
*
* This file updates users profile, password and returns response in json
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/


// Enable Error Reporting
error_reporting(0);

// Call required functions classes
require_once 'classes/UserAccountFunctions.php';
require_once 'classes/Keys.php';


// Create classes objects
$userAccountFunctions = new UserAccountFunctions();

// Create Json response array and initialize error to false
$response = array(KEY_ERROR => false);

// Update details associative array
$updateDetails  = array(FIELD_ACCOUNT_TYPE => "");

// Check for set POST params
if (isset($_POST[FIELD_USER_ID])
&& isset($_POST[FIELD_PASSWORD])
&& isset($_POST[FIELD_NEW_ACCOUNT_TYPE])
){

        // Get Values From POST
        $userId       = $_POST[FIELD_USER_ID]   ? $_POST[FIELD_USER_ID]   : '';
        $password       = $_POST[FIELD_PASSWORD]    ? $_POST[FIELD_PASSWORD]    : '';
        $newAccountType = $_POST[FIELD_NEW_ACCOUNT_TYPE] ? $_POST[FIELD_NEW_ACCOUNT_TYPE] : '';

        // Get user details
        $user = $userAccountFunctions->getUserByUserId($userId);

        // Check for user details
        if ($user !== false) {
            // User details fetched

            // Get users password hash
            $userHash = $user[FIELD_HASH];

            // Verify users password
            if ($userAccountFunctions->verifyPassword($password, $userHash)) {
                // Password verified

            } else {
                // Pasword verification failed

            }
        }

        // Verify password

} else {
    // Missing userId

    // Set error to true
    $response[KEY_ERROR]			= true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Encode and echo Json response
    echo json_encode($response);
}

// EOF: SwitchAccountType.php
