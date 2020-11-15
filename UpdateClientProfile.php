<?php

//    Copyright (c) 2020 by David Kariuki (dk).
//    All Rights Reserved.


// Update Client Profile Details

// Enable Error Reporting
error_reporting(1);

// Call required functions classes
require_once 'classes/ClientAccountFunctions.php';
require_once 'classes/FieldKeys.php';


// Create classes objects
$clientAccountFunctions = new ClientAccountFunctions();
$fieldKeys		        = new FieldKeys();

// Create Json response array and initialize error to false
$response = array($fieldKeys->keyError => false);

// Required fields
$firstName = "";
$lastName = "";
$businessName = "";
$phoneNumber = "";
$emailAddress = "";
$countryCode = "";
$countryAlpha2 = "";
$cityName = "";
$gender = "";

// Update details associative array
$updateDetails  = array(
    $fieldKeys->keyFirstName => "",
    $fieldKeys->keyLastName => "",
    $fieldKeys->keyGender => "",
    $fieldKeys->keyBusinessName => "",
    $fieldKeys->keyCityName => "",
    $fieldKeys->keyPhoneNumber => "",
    $fieldKeys->keyEmailAddress => "",
    $fieldKeys->keyCountryCode => "",
    $fieldKeys->keyCountryAlpha2 => "",
    $fieldKeys->keyPassword => "",
    $fieldKeys->keyAccountType => ""
);

// Check for set POST params
if (isset($_POST[$fieldKeys->keyClientId]) && isset($_POST[$fieldKeys->keyAccountType])) {

    // Get Values From POST
    $clientId       = $_POST[$fieldKeys->keyClientId]       ? $_POST[$fieldKeys->keyClientId]    : '';
    $accountType    = $_POST[$fieldKeys->keyAccountType]    ? $_POST[$fieldKeys->keyAccountType] : '';

    // Check for personal account params
    if ($accountType == $fieldKeys->keyAccountTypePersonal) {

        // Check for and get first name
        if (isset($_POST[$fieldKeys->keyFirstName])) {
            $firstName = $_POST[$fieldKeys->keyFirstName] ? $_POST[$fieldKeys->keyFirstName] : '';
            $updateDetails[$fieldKeys->keyFirstName] = $firstName; // Add firstName to details array
        }

        // Check for and get last name
        if (isset($_POST[$fieldKeys->keyLastName])) {
            $lastName = $_POST[$fieldKeys->keyLastName] ? $_POST[$fieldKeys->keyLastName] : '';
            $updateDetails[$fieldKeys->keyLastName] = $lastName; // Add lastName to details array
        }

        // Check for and get gender
        if (isset($_POST[$fieldKeys->keyGender])) {
            $gender = $_POST[$fieldKeys->keyGender] ? $_POST[$fieldKeys->keyGender] : '';
            $updateDetails[$fieldKeys->keyGender] = $gender; // Add gender to details array
        }

        // Check for busimess account params
    } else if ($accountType == $fieldKeys->keyAccountTypeBusiness) {

        // Check for and get business name
        if (isset($_POST[$fieldKeys->keyBusinessName])) {
            $businessName = $_POST[$fieldKeys->keyBusinessName] ? $_POST[$fieldKeys->keyBusinessName] : '';
            $updateDetails[$fieldKeys->keyBusinessName] = $businessName; // Add businessName to details array
        }

        // Check for and get city name
        if (isset($_POST[$fieldKeys->keyCityName])) {
            $cityName = $_POST[$fieldKeys->keyCityName] ? $_POST[$fieldKeys->keyCityName] : '';
            $updateDetails[$fieldKeys->keyCityName] = $cityName; // Add cityName to details array
        }
    }

    // Check for the shared account params

    // Check for and get phone number
    if (isset($_POST[$fieldKeys->keyPhoneNumber])) {
        $phoneNumber = $_POST[$fieldKeys->keyPhoneNumber] ? $_POST[$fieldKeys->keyPhoneNumber] : '';
        $updateDetails[$fieldKeys->keyPhoneNumber] = $phoneNumber; // Add phoneNumber to details array
    }

    // Check for and get email address
    if (isset($_POST[$fieldKeys->keyEmailAddress])) {
        $emailAddress = $_POST[$fieldKeys->keyEmailAddress] ? $_POST[$fieldKeys->keyEmailAddress] : '';
        $updateDetails[$fieldKeys->keyEmailAddress] = $emailAddress; // Add emailAddress to details array
    }

    // Check for and get country code and country alpha2
    if (isset($_POST[$fieldKeys->keyCountryCode]) && isset($_POST[$fieldKeys->keyCountryAlpha2])) {

        $countryCode = $_POST[$fieldKeys->keyCountryCode] ? $_POST[$fieldKeys->keyCountryCode] : '';
        $updateDetails[$fieldKeys->keyCountryCode] = $countryCode; // Add to details array

        $countryAlpha2 = $_POST[$fieldKeys->keyCountryAlpha2] ? $_POST[$fieldKeys->keyCountryAlpha2] : '';
        $updateDetails[$fieldKeys->keyCountryAlpha2] = $countryAlpha2; // Add to details array
    }


    // Loop through array removing null key value pairs
    foreach($updateDetails as $key => $value) {

        // Check for null
        if (is_null($value) || $value == '') {

            // Unset key and value
            unset($updateDetails[$key]);
        }
    }

    // Update profile details
    $update = $clientAccountFunctions->updateClientProfile($clientId, $accountType, $updateDetails);

    // Check if update was successful
    if ($update != false){
        // Update successful

        // Set error to false
        $response[$fieldKeys->keyError]             = false;
        $response[$fieldKeys->keySuccessMessage]    = "Update successful!";

        // Encode and echo Json response
        echo json_encode($response);

    } else {
        // Update unsuccessful

            // Set error to true
            $response[$fieldKeys->keyError]			= true;
            $response[$fieldKeys->keyErrorMessage] 	= "Something went terribly wrong!";

            // Encode and echo Json response
            echo json_encode($response);
    }

} else {
    // Missing clientId

    // Set error to true
    $response[$fieldKeys->keyError]			= true;
    $response[$fieldKeys->keyErrorMessage] 	= "Something went terribly wrong!";

    // Encode and echo Json response
    echo json_encode($response);
}

?>
