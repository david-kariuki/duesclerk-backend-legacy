<?php

/**
* Fetch user profile details file
* This file fetches users profile and returns response in json
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

// Create classes objects
$userAccountFunctions = new UserAccountFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Check for set POST params
if (isset($_POST[FIELD_EMAIL_ADDRESS]) && isset($_POST[FIELD_PASSWORD])) {

    // Get values from Post
    $emailAddress   = $_POST[FIELD_EMAIL_ADDRESS]   ? $_POST[FIELD_EMAIL_ADDRESS]	: '';
    $password 		= $_POST[FIELD_PASSWORD]        ? $_POST[FIELD_PASSWORD]        : '';


    // Get user by EmailAddress and password
    $user = $userAccountFunctions->getUserByEmailAddressAndPassword(
        $emailAddress,
        $password
    );

    // Check if user was found
    if ($user != false) {
        // User details found

        // Add user account details to response array
        $response[KEY_USER][FIELD_FULL_NAME_OR_BUSINESS_NAME] = $user[FIELD_FULL_NAME_OR_BUSINESS_NAME];

        // Add user details to response array
        $response[KEY_USER][FIELD_USER_ID]              = $user[FIELD_USER_ID];
        $response[KEY_USER][FIELD_EMAIL_ADDRESS]        = $user[FIELD_EMAIL_ADDRESS];

        // Get country flag and strip out extension type to get flag name
        $countryFlagName = str_replace(FILE_TYPE_PNG, "", $user[FIELD_COUNTRY_FLAG]);

        $response[KEY_USER][FIELD_COUNTRY_FLAG]         = $countryFlagName;
        $response[KEY_USER][FIELD_COUNTRY_NAME]         = $user[FIELD_COUNTRY_NAME];
        $response[KEY_USER][FIELD_COUNTRY_CODE]         = $user[FIELD_COUNTRY_CODE];
        $response[KEY_USER][FIELD_COUNTRY_ALPHA2]       = $user[FIELD_COUNTRY_ALPHA2];
        $response[KEY_USER][FIELD_EMAIL_VERIFIED]       = $user[FIELD_EMAIL_VERIFIED];
        $response[KEY_USER][FIELD_SIGN_UP_DATE_TIME]    = $user[FIELD_SIGN_UP_DATE_TIME];
        $response[KEY_USER][FIELD_ACCOUNT_TYPE]         = $user[FIELD_ACCOUNT_TYPE];

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

    } else {
        // User not found

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_ERROR_MESSAGE]    = "Please sign in to continue!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script
    }

} else {
    // EmailAddress or password or both missing

    // Set error to true
    $response[KEY_ERROR]			= true;
    $response[KEY_ERROR_MESSAGE] 	= "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);

    exit; // Exit script
}

// EOF: fetchUserDetails.php
