<?php

/**
* Delete contact file
* This file deletes user contacts from database and returns response in json
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
use duesclerk\contact\ContactFunctions;

// Create Classes Objects
$contactFunctions = new ContactFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Check for set POST params
if (isset($_POST[KEY_CONTACTS_IDS]) && isset($_POST[FIELD_USER_ID])) {

    // Get Values From POST
    $contactsIds = $_POST[KEY_CONTACTS_IDS]  ? $_POST[KEY_CONTACTS_IDS] : ''; // Get ContactIds
    $userId     = $_POST[FIELD_USER_ID]     ? $_POST[FIELD_USER_ID]     : ''; // Get UserId

    // Decode JSON array with contacts ids into PHP array
    $contactsIds = json_decode($contactsIds, true);

    // Delete contact(s)
    $deleteContacts = $contactFunctions->deleteUserContacts($contactsIds, $userId);

    // Check for delete contacts response
    if ($deleteContacts == null) {
        // Contact not found

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Contact not found!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

    } else if ($deleteContacts == 0) {
        // Deleting debts failed

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Debts deleting failed!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

    } else if ($deleteContacts == false) {
        // Contact not deleted

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Contact not deleted!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

    } else {
        // Contact deleted

        // Set success message
        $response[KEY_DELETE_CONTACTS][KEY_SUCCESS_MESSAGE] = "Contact deleted!";

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

// EOF: deleteUserContacts.php
