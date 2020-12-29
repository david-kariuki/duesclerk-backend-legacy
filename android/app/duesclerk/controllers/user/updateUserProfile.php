<?php

/**
* Update user profile details file
*
* This file updates users profile, password and returns response in json
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/


error_reporting(1); // Enable Error Reporting

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\user\UserAccountFunctions;
use duesclerk\configs\Constants;

// Create classes objects
$userAccountFunctions = new UserAccountFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Required fields
$firstName      = "";
$lastName       = "";
$businessName   = "";
$emailAddress   = "";
$countryCode    = "";
$countryAlpha2  = "";

// Update details associative array
$updateDetails  = array(
    FIELD_FIRST_NAME        => "",
    FIELD_LAST_NAME         => "",
    FIELD_BUSINESS_NAME     => "",
    FIELD_EMAIL_ADDRESS     => "",
    FIELD_COUNTRY_CODE      => "",
    FIELD_COUNTRY_ALPHA2    => "",
    FIELD_ACCOUNT_TYPE      => ""
);


// Check for set POST params
if (
    isset($_POST[FIELD_USER_ID]) &&
    (isset($_POST[FIELD_ACCOUNT_TYPE]) || (isset($_POST[FIELD_PASSWORD]) && isset($_POST[FIELD_NEW_PASSWORD])))
){

    // Get Values From POST
    $userId   = $_POST[FIELD_USER_ID] ? $_POST[FIELD_USER_ID]    : '';
    $update     = "";

    // Check for password
    if (isset($_POST[FIELD_PASSWORD]) && isset($_POST[FIELD_NEW_PASSWORD])) {
        // Updating password

        // Get values from POST
        $currentPassword   = $_POST[FIELD_PASSWORD]     ? $_POST[FIELD_PASSWORD]	    : '';
        $newPassword       = $_POST[FIELD_NEW_PASSWORD] ? $_POST[FIELD_NEW_PASSWORD]    : '';

        // Check for password change
        if ($newPassword == $currentPassword) {
            // Password did not change

            // Set response error to true and add error message
            $response[KEY_ERROR]         = true;
            $response[KEY_ERROR_MESSAGE] = "Please choose a different password other than the current one!";

            // Echo encoded Json response
            echo json_encode($response);

        } else {
            // New passwprd

            // Update password
            $update = $userAccountFunctions->updateUserPassword(
                $userId,
                $currentPassword,
                $newPassword
            );
        }
    } else if (isset($_POST[FIELD_ACCOUNT_TYPE])) {
        // Updating profile details

        // Get values from POST
        $accountType    = $_POST[FIELD_ACCOUNT_TYPE]    ? $_POST[FIELD_ACCOUNT_TYPE] : '';

        // Check for personal account params
        if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {

            // Check for and get first name
            if (isset($_POST[FIELD_FIRST_NAME])) {
                $firstName = $_POST[FIELD_FIRST_NAME] ? $_POST[FIELD_FIRST_NAME] : '';
                $updateDetails[FIELD_FIRST_NAME] = $firstName; // Add firstName to details array
            }

            // Check for and get last name
            if (isset($_POST[FIELD_LAST_NAME])) {
                $lastName = $_POST[FIELD_LAST_NAME] ? $_POST[FIELD_LAST_NAME] : '';
                $updateDetails[FIELD_LAST_NAME] = $lastName; // Add lastName to details array
            }

            // Check for busimess account params
        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {

            // Check for and get business name
            if (isset($_POST[FIELD_BUSINESS_NAME])) {

                $businessName = $_POST[FIELD_BUSINESS_NAME] ? $_POST[FIELD_BUSINESS_NAME] : '';

                // Add businessName to details array
                $updateDetails[FIELD_BUSINESS_NAME] = $businessName;
            }
        }

        // Check for the other account params

        // Check for and get email address
        if (isset($_POST[FIELD_EMAIL_ADDRESS])) {
            $emailAddress = $_POST[FIELD_EMAIL_ADDRESS] ? $_POST[FIELD_EMAIL_ADDRESS] : '';

            // Add emailAddress to details array
            $updateDetails[FIELD_EMAIL_ADDRESS] = $emailAddress;
        }

        // Check for and get country code and country alpha2
        if (isset($_POST[FIELD_COUNTRY_CODE]) && isset($_POST[FIELD_COUNTRY_ALPHA2])) {

            $countryCode = $_POST[FIELD_COUNTRY_CODE] ? $_POST[FIELD_COUNTRY_CODE] : '';
            $updateDetails[FIELD_COUNTRY_CODE] = $countryCode; // Add to details array

            $countryAlpha2 = $_POST[FIELD_COUNTRY_ALPHA2] ? $_POST[FIELD_COUNTRY_ALPHA2] : '';
            $updateDetails[FIELD_COUNTRY_ALPHA2] = $countryAlpha2; // Add to details array
        }


        // Loop through array removing null key value pairs
        foreach ($updateDetails as $key => $value) {

            // Check for null
            if (is_null($value) || $value == '') {

                // Unset key and value
                unset($updateDetails[$key]);
            }
        }

        // Update profile details
        $update = $userAccountFunctions->updateUserProfile(
            $userId,
            $accountType,
            $updateDetails
        );
    }

    // Check if update was successful
    if ($update != false) {
        // Update successful

        // Set success message
        $response[KEY_UPDATE_PROFILE][KEY_SUCCESS_MESSAGE] = "Update successful!";

        // Echo encoded Json response
        echo json_encode($response);

    } else {
        // Update unsuccessful

        // Set error to true
        $response[KEY_ERROR]			= true;
        $response[KEY_ERROR_MESSAGE]    = "Update failed!";

        // Echo encoded Json response
        echo json_encode($response);
    }

} else {
    // Missing userId

    // Set error to true
    $response[KEY_ERROR]			= true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Echo encoded Json response
    echo json_encode($response);
}

// EOF: updateUserProfile.php
