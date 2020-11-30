<?php

//    Copyright (c) 2020 by David Kariuki (dk).
//    All Rights Reserved.


// Update Client Profile Details

// Enable Error Reporting
error_reporting(1);

// Call required functions classes
require_once 'classes/ClientAccountFunctions.php';
require_once 'classes/Keys.php';


// Create classes objects
$clientAccountFunctions = new ClientAccountFunctions();

// Create Json response array and initialize error to false
$response = array(KEY_ERROR => false);

// Required fields
$firstName      = "";
$lastName       = "";
$businessName   = "";
$phoneNumber    = "";
$emailAddress   = "";
$countryCode    = "";
$countryAlpha2  = "";
$cityName       = "";
$gender         = "";

// Update details associative array
$updateDetails  = array(
    FIELD_FIRST_NAME        => "",
    FIELD_LAST_NAME         => "",
    FIELD_GENDER            => "",
    FIELD_BUSINESS_NAME     => "",
    FIELD_CITY_NAME         => "",
    FIELD_PHONE_NUMBER      => "",
    FIELD_EMAIL_ADDRESS     => "",
    FIELD_COUNTRY_CODE      => "",
    FIELD_COUNTRY_ALPHA2    => "",
    FIELD_ACCOUNT_TYPE      => ""
);

// Check for set POST params
if (
    isset($_POST[FIELD_CLIENT_ID]) &&
    (isset($_POST[FIELD_ACCOUNT_TYPE]) || (isset($_POST[FIELD_PASSWORD]) && isset($_POST[FIELD_NEW_PASSWORD])))
){

    // Get Values From POST
    $clientId   = $_POST[FIELD_CLIENT_ID]       ? $_POST[FIELD_CLIENT_ID]    : '';
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

            // Set response error to true
            $response[KEY_ERROR]         = true;
            $response[KEY_ERROR_MESSAGE] = "Please choose a different password other than the current one!";

            // Encode and echo Json response
            echo json_encode($response);

        } else {
            // New passwprd

            // Update password
            $update = $clientAccountFunctions->updateClientPassword(
                $clientId,
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

            // Check for and get gender
            if (isset($_POST[FIELD_GENDER])) {
                $gender = $_POST[FIELD_GENDER] ? $_POST[FIELD_GENDER] : '';
                $updateDetails[FIELD_GENDER] = $gender; // Add gender to details array
            }

            // Check for busimess account params
        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {

            // Check for and get business name
            if (isset($_POST[FIELD_BUSINESS_NAME])) {
                $businessName = $_POST[FIELD_BUSINESS_NAME] ? $_POST[FIELD_BUSINESS_NAME] : '';
                $updateDetails[FIELD_BUSINESS_NAME] = $businessName; // Add businessName to details array
            }

            // Check for and get city name
            if (isset($_POST[FIELD_CITY_NAME])) {
                $cityName = $_POST[FIELD_CITY_NAME] ? $_POST[FIELD_CITY_NAME] : '';
                $updateDetails[FIELD_CITY_NAME] = $cityName; // Add cityName to details array
            }
        }

        // Check for the shared account params

        // Check for and get phone number
        if (isset($_POST[FIELD_PHONE_NUMBER])) {
            $phoneNumber = $_POST[FIELD_PHONE_NUMBER] ? $_POST[FIELD_PHONE_NUMBER] : '';
            $updateDetails[FIELD_PHONE_NUMBER] = $phoneNumber; // Add phoneNumber to details array
        }

        // Check for and get email address
        if (isset($_POST[FIELD_EMAIL_ADDRESS])) {
            $emailAddress = $_POST[FIELD_EMAIL_ADDRESS] ? $_POST[FIELD_EMAIL_ADDRESS] : '';
            $updateDetails[FIELD_EMAIL_ADDRESS] = $emailAddress; // Add emailAddress to details array
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
        $update = $clientAccountFunctions->updateClientProfile(
            $clientId,
            $accountType,
            $updateDetails
        );
    }

    // Check if update was successful
    if ($update != false) {
        // Update successful

        // Set error to false
        $response[KEY_ERROR]            = false;
        $response[KEY_SUCCESS_MESSAGE]  = "Update successful!";

        // Encode and echo Json response
        echo json_encode($response);

    } else {
        // Update unsuccessful

        // Set error to true
        $response[KEY_ERROR]			= true;
        $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

        // Encode and echo Json response
        echo json_encode($response);
    }

} else {
    // Missing clientId

    // Set error to true
    $response[KEY_ERROR]			= true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Encode and echo Json response
    echo json_encode($response);
}

// EOF: UpdateClientProfile.php
