<?php

/**
* Add contacts file
* This file Signs In / logs in users and returns response in json
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
    isset($_POST[FIELD_CONTACTS_FULL_NAME]) && isset($_POST[FIELD_CONTACTS_PHONE_NUMBER])
    && isset($_POST[FIELD_CONTACTS_TYPE]) && isset($_POST[FIELD_USER_ID])
) {


    // Contact details array
    $contactDetails = array(
        FIELD_CONTACTS_FULL_NAME     => "",
        FIELD_CONTACTS_PHONE_NUMBER  => "",
        FIELD_CONTACTS_EMAIL_ADDRESS => "",
        FIELD_CONTACTS_ADDRESS       => "",
        FIELD_CONTACTS_TYPE          => "",
    );

    // Get Values From POST
    $userId                 = $_POST[FIELD_USER_ID] ? $_POST[FIELD_USER_ID] : '';
    $contactsFullName       = $_POST[FIELD_CONTACTS_FULL_NAME]
    ? $_POST[FIELD_CONTACTS_FULL_NAME] : '';
    $contactsPhoneNumber    = $_POST[FIELD_CONTACTS_PHONE_NUMBER]
    ? $_POST[FIELD_CONTACTS_PHONE_NUMBER] : '';
    $contactsType           = $_POST[FIELD_CONTACTS_TYPE] ? $_POST[FIELD_CONTACTS_TYPE] : '';

    // Add full name, phone number and contact type to contact details array
    $contactDetails[FIELD_CONTACTS_FULL_NAME]    = $contactsFullName;
    $contactDetails[FIELD_CONTACTS_PHONE_NUMBER] = $contactsPhoneNumber;
    $contactDetails[FIELD_CONTACTS_TYPE]         = $contactsType;

    // Check for phone number`
    if (isset($_POST[FIELD_CONTACTS_ADDRESS])) {

        $contactAddress = $_POST[FIELD_CONTACTS_ADDRESS] ?
        $_POST[FIELD_CONTACTS_ADDRESS] : '';

        // Add contact address to contact details array
        $contactDetails[FIELD_CONTACTS_ADDRESS] = $contactAddress;
    }

    // Check for email address
    if (isset($_POST[FIELD_CONTACTS_EMAIL_ADDRESS])) {

        $contactEmailAddress = $_POST[FIELD_CONTACTS_EMAIL_ADDRESS] ?
        $_POST[FIELD_CONTACTS_EMAIL_ADDRESS] : '';

        /**
        * Check email address validity
        * Check the maximum allowed length of the email address
        * Total length in RFC_3696 is 320 characters
        * The local part of the email address—your username—must not exceed 64 characters.
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

        } else {

            // Add email address to contact details array
            $contactDetails[FIELD_CONTACTS_EMAIL_ADDRESS] = $contactEmailAddress;

            // Check for email address in contacts table
            if ($contactFunctions->isemailAddressInContactsTable(
                $contactEmailAddress,
                $contactsType
                )
            ) {
                // Contact email address is in contacts table

                // Get contact details
                $contact = $contactFunctions->getContactByEmailAddress($contactEmailAddress);

                // Get contacts full name
                $fullName = $contactDetails[FIELD_CONTACTS_FULL_NAME];

                // Set response error to true and add error message
                $response[KEY_ERROR]           = true;
                $response[KEY_ERROR_MESSAGE]   = "The email address you entered exists as " . $fullName . "!";

                // Echo encoded JSON response
                echo json_encode($response);
            }
        }
    }

    // Check for phone number in contacts table
    if ($contactFunctions->isPhoneNumberInContactsTable($contactsPhoneNumber, $contactsType)) {
        // Contact phone number is in contacts table

        // Get contact details
        $contact = $contactFunctions->getContactByPhoneNumber($contactsPhoneNumber);

        // Get contacts full name
        $fullName = $contactDetails[FIELD_CONTACTS_FULL_NAME];

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "The phone number you entered exists as " . $fullName . "!";

        // Echo encoded JSON response
        echo json_encode($response);
    }

    // Add contact to database
    $addContact = $contactFunctions->addContact($userId, $contactDetails);

    // Check if contact was added
    if ($addContact !== false) {
        // Contact added

        // Set success message
        $response[KEY_SUCCESS_MESSAGE] = "Contact added successfully!";

        // Echo encoded JSON response
        echo json_encode($response);

    } else {
        // Contact addition failed

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Contact not added!" . $addContact . "-";

        // Echo encoded JSON response
        echo json_encode($response);
    }
} else {
    // Mising fields

    // Set response error to true and add error message
    $response[KEY_ERROR]           = true;
    $response[KEY_ERROR_MESSAGE]   = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);
}

// EOF: addUserContact.php
