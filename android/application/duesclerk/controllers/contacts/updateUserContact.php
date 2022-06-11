<?php

/**
* Update user contact details file
* This file updates users contact details and returns response in json
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
use duesclerk\contact\ContactFunctions;

// Create classes objects
$contactFunctions = new ContactFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Update details associative array
$updateDetails  = array(
    FIELD_CONTACT_ID            => "",
    FIELD_CONTACT_FULL_NAME     => "",
    FIELD_CONTACT_PHONE_NUMBER  => "",
    FIELD_CONTACT_EMAIL_ADDRESS => "",
    FIELD_CONTACT_ADDRESS       => "",
    FIELD_USER_ID               => ""
);

// Check for set POST params
if (isset($_POST[FIELD_USER_ID]) && isset($_POST[FIELD_CONTACT_ID])) {

    // Get Values From POST
    $contactId          = $_POST[FIELD_CONTACT_ID] ? $_POST[FIELD_CONTACT_ID] : '';
    $contactFullName    = $_POST[FIELD_CONTACT_FULL_NAME] ? $_POST[FIELD_CONTACT_FULL_NAME] : '';
    $contactPhoneNumber = $_POST[FIELD_CONTACT_PHONE_NUMBER] ? $_POST[FIELD_CONTACT_PHONE_NUMBER] : '';
    $userId             = $_POST[FIELD_USER_ID] ? $_POST[FIELD_USER_ID]    : '';

    // Check for contact address
    if (isset($_POST[FIELD_CONTACT_EMAIL_ADDRESS])) {

        $updateDetails[FIELD_CONTACT_ADDRESS] =
        $_POST[FIELD_CONTACT_ADDRESS] ? $_POST[FIELD_CONTACT_ADDRESS] : '';
    }

    // Check for contact EmailAddress
    if (isset($_POST[FIELD_CONTACT_EMAIL_ADDRESS])) {

        $contactEmailAddress = $_POST[FIELD_CONTACT_EMAIL_ADDRESS] ? $_POST[FIELD_CONTACT_EMAIL_ADDRESS] : '';

        /**
        * Check EmailAddress validity
        * Check the maximum allowed length of the EmailAddress
        * Total length in RFC_3696 is 320 characters
        * The local part of the EmailAddress—your username—must not exceed 64 characters.
        * The domain name is limited to 255 characters.
        */
        if ((!filter_var($contactEmailAddress, FILTER_VALIDATE_EMAIL))
        || (strlen($contactEmailAddress) > LENGTH_MAX_EMAIL_ADDRESS)) {
            // Invalid email

            // Set response error to true and add error message
            $response[KEY_ERROR]            = true;
            $response[KEY_ERROR_MESSAGE]    = "The email address you entered is invalid!";

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script

        } else {
            // Contact email is valid

            // Add contact EmailAddress to contact details associative array
            $updateDetails[FIELD_CONTACT_EMAIL_ADDRESS] = $contactEmailAddress;
        }
    }

    // Add other contact details to details associative array
    $updateDetails[FIELD_CONTACT_ID]            = $contactId;
    $updateDetails[FIELD_CONTACT_FULL_NAME]     = $contactFullName;
    $updateDetails[FIELD_CONTACT_PHONE_NUMBER]  = $contactPhoneNumber;
    $updateDetails[FIELD_USER_ID]               = $userId;

    // Update contact
    $updateContact = $contactFunctions->updateContactDetails($updateDetails);

    // Check for successful contact update
    if ($updateContact !== false) {
        // Contact updated

        // Set success message
        $response[KEY_UPDATE_CONTACT][KEY_SUCCESS_MESSAGE] = "Contact updated successfully!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

    } else {
        // Contact update failed

        // Set error to true
        $response[KEY_ERROR]			= true;
        $response[KEY_ERROR_MESSAGE]    = "Contact update failed!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script
    }
} else {
    // Missing userId

    // Set error to true
    $response[KEY_ERROR]			= true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);

    exit; // Exit script
}

// EOF: updateUserContact.php
