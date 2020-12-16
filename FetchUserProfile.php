<?php

/**
* Fetch user profile details file
* This file fetches users profile and returns response in json
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

// Check for set POST params
if (isset($_POST[FIELD_EMAIL_ADDRESS]) && isset($_POST[FIELD_PASSWORD])) {

    // Get values from Post
    $emailAddress   = $_POST[FIELD_EMAIL_ADDRESS]   ? $_POST[FIELD_EMAIL_ADDRESS]	: '';
    $password 		= $_POST[FIELD_PASSWORD]        ? $_POST[FIELD_PASSWORD]        : '';


    // Get user by emailAddress and password
    $user = $userAccountFunctions->getUserByEmailAddressAndPassword(
        $emailAddress,
        $password
    );

    // Check if user Was Found
    if ($user != false) {
        // User details found

        // Get account type
        $accountType = $user[FIELD_ACCOUNT_TYPE];

        // Add user business and personal account details to response array
        if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
            // Personal account

            $response[KEY_USER][FIELD_FIRST_NAME]   = $user[FIELD_FIRST_NAME];
            $response[KEY_USER][FIELD_LAST_NAME]    = $user[FIELD_LAST_NAME];
            $response[KEY_USER][FIELD_GENDER]       = $user[FIELD_GENDER];

        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
            // Business account

            $response[KEY_USER][FIELD_BUSINESS_NAME] = $user[FIELD_BUSINESS_NAME];
        }

        // Add user Details To Response Array
        $response[KEY_USER][FIELD_USER_ID]        = $user[FIELD_USER_ID];
        $response[KEY_USER][FIELD_EMAIL_ADDRESS]    = $user[FIELD_EMAIL_ADDRESS];

        // Get country flag and strip out extension type to get flag name
        $countryFlagName = str_replace(FILE_TYPE_PNG, "", $user[FIELD_COUNTRY_FLAG]);
        $response[KEY_USER][FIELD_COUNTRY_FLAG]     = $countryFlagName;

        $response[KEY_USER][FIELD_COUNTRY_NAME]     = $user[FIELD_COUNTRY_NAME];
        $response[KEY_USER][FIELD_COUNTRY_CODE]     = $user[FIELD_COUNTRY_CODE];
        $response[KEY_USER][FIELD_COUNTRY_ALPHA2]   = $user[FIELD_COUNTRY_ALPHA2];
        $response[KEY_USER][FIELD_EMAIL_VERIFIED]   = $user[FIELD_EMAIL_VERIFIED];
        $response[KEY_USER][FIELD_SIGN_UP_DATE_TIME] = $user[FIELD_SIGN_UP_DATE_TIME];

        // Encode and echo json response
        echo json_encode($response);

    } else {
        // User not found

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_ERROR_MESSAGE]    = "Please sign in to continue!";

        // Encode and echo json response
        echo json_encode($response);
    }

} else {
    // EmailAddress Or password Or both missing

    // Set error to true
    $response[KEY_ERROR]			= true;
    $response[KEY_ERROR_MESSAGE] 	= "Something went terribly wrong!";

    // Encode and echo Json response
    echo json_encode($response);
}

// EOF: FetchUserDetails.php
