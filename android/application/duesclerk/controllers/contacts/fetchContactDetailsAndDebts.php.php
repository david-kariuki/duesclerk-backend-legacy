<?php

/**
* Fetch user contacts file
* This file fetches contacts debts and returns response in json
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
if (isset($_POST[FIELD_USER_ID]) && isset($_POST[FIELD_CONTACT_ID])) {

    // Get Values From POST
    $userId     = $_POST[FIELD_USER_ID]     ? $_POST[FIELD_USER_ID]     : ''; // Get UserId
    $contactId  = $_POST[FIELD_CONTACT_ID]  ? $_POST[FIELD_CONTACT_ID]  : ''; // Get ContactId

    // Get contact by contact id
    $getContactDetails = $contactFunctions->getContactDetailsByContactId($contactId);

    // Check for contact details
    if ($getContactDetails !== false) {
        // Contacts info fetched

        $contactType = $getContactDetails[FIELD_CONTACT_TYPE]; // Get contact type

        // Get contact debts
        $getContactsDebts = $contactFunctions->getContactsDebts($contactId, $contactType, $userId);

        // Check for debts
        if ($getContactsDebts !== false) {

            if ($getContactsDebts !== null) {
                // Contacts debts found

                // Add debts total amount to contact details
                $getContactDetails[FIELD_DEBTS_TOTAL_AMOUNT] = $getContactsDebts[FIELD_DEBTS_TOTAL_AMOUNT];

                // Add contact info to response array
                $response[KEY_CONTACT_DETAILS] = $getContactDetails;

                // Add debts to response array
                $response[KEY_DEBTS] = $getContactsDebts[KEY_DEBTS];

            } else {

                // Add contact info to response array
                $response[KEY_CONTACT_DETAILS] = $getContactDetails;

                $response[KEY_DEBTS] = array(); // Set debts array to null
            }

            // Echo encoded JSON response
            echo json_encode($response);

        } else {
            // Get contacts debts failed

            // Set response error to true and add error message
            $response[KEY_ERROR]           = true;
            $response[KEY_ERROR_MESSAGE]   = "Contact debts not fetched!";

            // Echo encoded JSON response
            echo json_encode($response);
        }
    } else {
        // Contact info not fetched

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Contact not found!";

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

// EOF: fetchContactsDebts.php
