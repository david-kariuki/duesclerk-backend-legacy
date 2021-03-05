<?php

/**
* Fetch user contact file
* This file fetches user contacts and returns response in json
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
use duesclerk\debt\DebtFunctions;

// Create Classes Objects
$contactFunctions = new ContactFunctions();
$debtFunctions    = new DebtFunctions();

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

        // Get total debts for all contacts
        $getAllContactsTotals = $debtFunctions->getContactsDebtsTotalSumForAllUserContacts(
            $getContacts
        );

        // Check for contacts debts total
        if ($getAllContactsTotals !== false) {
            // Contacts totals found

            // Add all contacts debts total amount to response
            $response[KEY_ALL_CONTACTS_DEBTS_TOTAL_AMOUNT] = $getAllContactsTotals;

            $response[KEY_CONTACTS] = $getContacts; // Add contact array to JSON response

        } else {
            // Contacts debts total fetching failed or null

            // Add all PeopleOwingMe contacts debts total amount to response
            $response[KEY_ALL_CONTACTS_DEBTS_TOTAL_AMOUNT][KEY_CONTACTS_DEBTS_TOTAL_PEOPLE_OWING_ME] = "0.00";

            // Add all PeopleOwingMe contacts debts total amount to response
            $response[KEY_ALL_CONTACTS_DEBTS_TOTAL_AMOUNT][KEY_CONTACTS_DEBTS_TOTAL_PEOPLE_I_OWE] = "0.00";
        }

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

    } else {
        // User contact fetching failed or null

        if ($getContacts == null) {
            // Contacts not found

            // Add all PeopleOwingMe contacts debts total amount to response
            $response[KEY_ALL_CONTACTS_DEBTS_TOTAL_AMOUNT][KEY_CONTACTS_DEBTS_TOTAL_PEOPLE_OWING_ME] = "0.00";

            // Add all PeopleOwingMe contacts debts total amount to response
            $response[KEY_ALL_CONTACTS_DEBTS_TOTAL_AMOUNT][KEY_CONTACTS_DEBTS_TOTAL_PEOPLE_I_OWE] = "0.00";

            $response[KEY_CONTACTS] = array(); // Set empty array as contacts

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script

        } else {

            // Set response error to true and add error message
            $response[KEY_ERROR]           = true;
            $response[KEY_ERROR_MESSAGE]   = "Something went terribly wrong!";

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script
        }
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

// EOF: fetchUserContacts.php
