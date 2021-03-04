<?php

/**
* Add contact file
* This file adds user contacts to database and returns response in json
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

// Enable Error Reporting
error_reporting(1);

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\contact\ContactFunctions;

// Create Classes Objects
$contactFunctions = new ContactFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Check for set POST params
if (
    isset($_POST[FIELD_CONTACT_FULL_NAME]) && isset($_POST[FIELD_CONTACT_PHONE_NUMBER])
    && isset($_POST[FIELD_CONTACT_TYPE]) && isset($_POST[FIELD_USER_ID])
) {


    // Contact details array
    $contactDetails = array(
        FIELD_CONTACT_FULL_NAME     => "",
        FIELD_CONTACT_PHONE_NUMBER  => "",
        FIELD_CONTACT_EMAIL_ADDRESS => "",
        FIELD_CONTACT_ADDRESS       => "",
        FIELD_CONTACT_TYPE          => "",
    );

    // Get Values From POST
    $userId                 = $_POST[FIELD_USER_ID] ? $_POST[FIELD_USER_ID] : '';
    $contactFullName       = $_POST[FIELD_CONTACT_FULL_NAME]
    ? $_POST[FIELD_CONTACT_FULL_NAME] : '';
    $contactPhoneNumber    = $_POST[FIELD_CONTACT_PHONE_NUMBER]
    ? $_POST[FIELD_CONTACT_PHONE_NUMBER] : '';
    $contactType           = $_POST[FIELD_CONTACT_TYPE] ? $_POST[FIELD_CONTACT_TYPE] : '';

    // Add full name, phone number and contact type to contact details array
    $contactDetails[FIELD_CONTACT_FULL_NAME]    = $contactFullName;
    $contactDetails[FIELD_CONTACT_PHONE_NUMBER] = $contactPhoneNumber;
    $contactDetails[FIELD_CONTACT_TYPE]         = $contactType;

    // Check for phone number`
    if (isset($_POST[FIELD_CONTACT_ADDRESS])) {

        $contactAddress = $_POST[FIELD_CONTACT_ADDRESS] ?
        $_POST[FIELD_CONTACT_ADDRESS] : '';

        // Add contact address to contact details array
        $contactDetails[FIELD_CONTACT_ADDRESS] = $contactAddress;
    }

    // Check for EmailAddress
    if (isset($_POST[FIELD_CONTACT_EMAIL_ADDRESS])) {

        $contactEmailAddress = $_POST[FIELD_CONTACT_EMAIL_ADDRESS] ?
        $_POST[FIELD_CONTACT_EMAIL_ADDRESS] : '';

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
            $response[KEY_SIGN_UP]          = FIELD_EMAIL_ADDRESS;
            $response[KEY_ERROR_MESSAGE]    = "The email address you entered is invalid!";

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script

        } else {

            // Add EmailAddress to contact details array
            $contactDetails[FIELD_CONTACT_EMAIL_ADDRESS] = $contactEmailAddress;

            // Check for EmailAddress in contact table
            if ($contactFunctions->isemailAddressInContactsTable(
                $contactEmailAddress,
                $contactType
                )
            ) {
                // Contact EmailAddress is in contact table

                // Get contact details
                $contact = $contactFunctions->getContactDetailsByContactEmailAddress(
                    $contactEmailAddress
                );

                // Get contact full name
                $fullName = $contactDetails[FIELD_CONTACT_FULL_NAME];

                // Set response error to true and add error message
                $response[KEY_ERROR]           = true;
                $response[KEY_ERROR_MESSAGE]   = "The email address you entered exists as " . $fullName . "!";

                // Echo encoded JSON response
                echo json_encode($response);

                exit; // Exit script
            }
        }
    }

    // Check for phone number in contact table
    if ($contactFunctions->isPhoneNumberInContactsTable($contactPhoneNumber, $contactType)) {
        // Contact phone number is in contact table

        // Get contact details
        $contact = $contactFunctions->getContactDetailsByContactPhoneNumber($contactPhoneNumber);

        // Get contact full name
        $fullName = $contactDetails[FIELD_CONTACT_FULL_NAME];

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "The phone number you entered exists as " . $fullName . "!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script
    }

    // Add contact to database
    $addContact = $contactFunctions->addUsersContact($userId, $contactDetails);

    // Check if contact was added
    if ($addContact !== false) {
        // Contact added

        // Set success message
        $response[KEY_SUCCESS_MESSAGE] = "Contact added successfully!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

    } else {
        // Contact addition failed

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Contact not added!" . $addContact;

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script
    }
} else {
    // Mising fields

    // Set response error to true and add error message
    $response[KEY_ERROR]           = true;
    $response[KEY_ERROR_MESSAGE]   = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);

    exit; // Exit script
}

// EOF: addUserContact.php
