<?php

/**
* Fetch user contact file
* This file fetches user contacts and returns response in json
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
if (isset($_POST[FIELD_USER_ID])) {

    // Get Values From POST
    $userId = $_POST[FIELD_USER_ID] ? $_POST[FIELD_USER_ID] : ''; // Get UserId

    // Get user contact
    $getContacts = $contactFunctions->getUserContactsByUserId($userId);

    // Check for contact
    if ($getContacts !== null) {
        // Contacts fetched successfully

        $response[KEY_CONTACTS] = $getContacts; // Add contact array to JSON response

        // Echo encoded JSON response
        echo json_encode($response);

    } else {
        // User contact fetching failed

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Something went terribly wrong!";

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

// EOF: fetchUserContact.php
