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

// Required fields
$fullNameOrBusinessName = "";
$emailAddress           = "";
$countryCode            = "";
$countryAlpha2          = "";

// Update details associative array
$updateDetails  = array(
    FIELD_FULL_NAME_OR_BUSINESS_NAME    => "",
    FIELD_EMAIL_ADDRESS                 => "",
    FIELD_COUNTRY_CODE                  => "",
    FIELD_COUNTRY_ALPHA2                => ""
);

// Check for set POST params
if (isset($_POST[FIELD_USER_ID])){

    // Get Values From POST
    $userId = $_POST[FIELD_USER_ID] ? $_POST[FIELD_USER_ID]    : '';
    $update = "";

    // Check for password
    if (isset($_POST[FIELD_PASSWORD]) && isset($_POST[FIELD_NEW_PASSWORD])) {
        // Updating password

        // Get values from POST
        $currentPassword    = $_POST[FIELD_PASSWORD]     ? $_POST[FIELD_PASSWORD]	    : '';
        $newPassword        = $_POST[FIELD_NEW_PASSWORD] ? $_POST[FIELD_NEW_PASSWORD]   : '';

        // Check for password change
        if ($newPassword == $currentPassword) {
            // Password did not change

            // Set response error to true and add error message
            $response[KEY_ERROR]         = true;
            $response[KEY_ERROR_MESSAGE] = "Please choose a different password other than the current one!";

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script

        } else {
            // New passwprd

            // Update password
            $update = $userAccountFunctions->updateUserPassword(
                $userId,
                $currentPassword,
                $newPassword
            );
        }
    } else {
        // Updating profile details

        // Check for and get FullNameOrBusinessName
        if (isset($_POST[FIELD_FULL_NAME_OR_BUSINESS_NAME])) {

            $fullNameOrBusinessName = $_POST[FIELD_FULL_NAME_OR_BUSINESS_NAME] ? $_POST[FIELD_FULL_NAME_OR_BUSINESS_NAME] : '';

            // Add FullNameOrBusinessName to details array
            $updateDetails[FIELD_FULL_NAME_OR_BUSINESS_NAME] = $fullNameOrBusinessName;
        }

        // Check for the other account params

        // Check for and get EmailAddress
        if (isset($_POST[FIELD_EMAIL_ADDRESS])) {
            $emailAddress = $_POST[FIELD_EMAIL_ADDRESS] ? $_POST[FIELD_EMAIL_ADDRESS] : '';

            // Add EmailAddress to details array
            $updateDetails[FIELD_EMAIL_ADDRESS] = $emailAddress;
        }

        // Check for and get CountryCode and CountryAlpha2
        if (isset($_POST[FIELD_COUNTRY_CODE]) && isset($_POST[FIELD_COUNTRY_ALPHA2])) {

            $countryCode = $_POST[FIELD_COUNTRY_CODE] ? $_POST[FIELD_COUNTRY_CODE] : '';

            // Add to details array
            $updateDetails[FIELD_COUNTRY_CODE] = $countryCode;

            $countryAlpha2 = $_POST[FIELD_COUNTRY_ALPHA2] ? $_POST[FIELD_COUNTRY_ALPHA2] : '';

            // Add to details array
            $updateDetails[FIELD_COUNTRY_ALPHA2] = $countryAlpha2;
        }


        // Loop through array removing null key value pairs
        foreach ($updateDetails as $key => $value) {

            // Check for null
            if (is_null($value) || $value == '') {

                unset($updateDetails[$key]); // Unset key and value
            }
        }

        // Update profile details
        $update = $userAccountFunctions->updateUserProfile(
            $userId,
            $updateDetails
        );
    }

    // Check if update was successful
    if ($update != false) {
        // Update successful

        // Set success message
        $response[KEY_UPDATE_PROFILE][KEY_SUCCESS_MESSAGE] = "Profile updated successfully!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

    } else {
        // Update unsuccessful

        // Set error to true
        $response[KEY_ERROR]			= true;
        $response[KEY_ERROR_MESSAGE]    = "Update failed!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script
    }

} else {
    // Missing UserId

    // Set error to true
    $response[KEY_ERROR]			= true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);

    exit; // Exit script
}

// EOF: updateUserProfile.php
