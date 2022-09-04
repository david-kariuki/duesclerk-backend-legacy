<?php

/**
* Fetch user contact file
* This file adds contacts debts then returns response in json
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 - 2022 David Kariuki (dk) All Rights Reserved.
*/


// Enable error reporting
ini_set('display_errors', 1); // Enable displaying of errors
ini_set('display_startup_errors', 1); // Enable displaying of startup errors
ini_set('log_errors', 1); // Enabke error logging
error_reporting(E_ALL | E_NOTICE | E_STRICT); // eNable all error reporting


// Call autoloader file
require_once $_SERVER["DOCUMENT_ROOT"] . "/duesclerk_php/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\contact\ContactFunctions;
use duesclerk\debt\DebtFunctions;

// Create classes objects
$contactFunctions   = new ContactFunctions();
$debtFunctions      = new DebtFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Debt details array
$debtDetails = array(

    FIELD_DEBT_AMOUNT       => "",
    FIELD_DEBT_DATE_ISSUED  => "",
    FIELD_DEBT_DATE_DUE     => "",
    FIELD_DEBT_DESCRIPTION  => "",
    FIELD_CONTACT_ID        => "",
    FIELD_CONTACT_TYPE      => "",
    FIELD_USER_ID           => ""
);

// Check for set POST params
if (isset($_POST[FIELD_USER_ID]) && isset($_POST[FIELD_CONTACT_ID])
&& isset($_POST[FIELD_DEBT_AMOUNT])) {

    // Get Values From POST

    $userId = $_POST[FIELD_USER_ID] ? $_POST[FIELD_USER_ID] : ''; // Get UserId
    $contactId = $_POST[FIELD_CONTACT_ID] ? $_POST[FIELD_CONTACT_ID] : ''; // Get ContactId
    $debtAmount = $_POST[FIELD_DEBT_AMOUNT] ? $_POST[FIELD_DEBT_AMOUNT] : ''; // Get DebtAmount
    $contactType = ""; // Contact type

    // Get contact by ContactId
    $getContact = $contactFunctions->getContactDetailsByContactId($contactId);

    // Check if contact fetched
    if ($getContact !== false) {
        // Contact fetched

        $contactType = $getContact[FIELD_CONTACT_TYPE]; // Get ContactType

        // Check for DebtDateIssued
        if (isset($_POST[FIELD_DEBT_DATE_ISSUED])) {

            // Get DebtDescription from POST
            $debtDateIssued = $_POST[FIELD_DEBT_DATE_ISSUED] ? $_POST[FIELD_DEBT_DATE_ISSUED] : '';

            // Add DebtDateIssued to update details
            $debtDetails[FIELD_DEBT_DATE_ISSUED] = $debtDateIssued;
        }

        // Check for DebtDateDue
        if (isset($_POST[FIELD_DEBT_DATE_DUE])) {

            // Get DebtDateDue from POST
            $debtDateDue = $_POST[FIELD_DEBT_DATE_DUE] ? $_POST[FIELD_DEBT_DATE_DUE]    : '';

            // Add DebtDateDue to update details
            $debtDetails[FIELD_DEBT_DATE_DUE] = $debtDateDue;
        }

        // Check for DebtDescription
        if (isset($_POST[FIELD_DEBT_DESCRIPTION])) {

            // Get DebtDescription from POST
            $debtDescription = $_POST[FIELD_DEBT_DESCRIPTION] ? $_POST[FIELD_DEBT_DESCRIPTION] : '';

            // Add DebtDescription to update details
            $debtDetails[FIELD_DEBT_DESCRIPTION] = $debtDescription;
        }

        // Check and sanitize debt amount
        $debtAmount = $debtFunctions->checkAndSanitizeDebtAmount($debtAmount);

        // Add other details to debt details array
        $debtDetails[FIELD_USER_ID]             = $userId;          // Add UserId
        $debtDetails[FIELD_CONTACT_ID]          = $contactId;       // Add ContactId
        $debtDetails[FIELD_DEBT_AMOUNT]         = $debtAmount;      // Add DebtAmount
        $debtDetails[FIELD_CONTACT_TYPE]        = $contactType;     // Add ContactType

        // Loop through array removing null key value pairs if any
        foreach ($debtDetails as $key => $value) {

            // Check for null
            if (is_null($value) || $value == '') {

                unset($debtDetails[$key]); // Unset key and value
            }
        }

        $addDebt = $debtFunctions->addContactsDebt($debtDetails); // Add debt to contact

        // Check if debt was added
        if ($addDebt !== false) {
            // Debt added

            // Set success message
            $response[KEY_SUCCESS_MESSAGE]  = "Debt added successfully!";

            // Return inserted debt associative array
            $response[KEY_DEBT]             = $addDebt;

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script

        } else {

            // Set response error to true and add error message
            $response[KEY_ERROR]           = true;
            $response[KEY_ERROR_MESSAGE]   = "Adding debt failed!";

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script
        }
    } else {
        // Contact does not exist

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Contact does not exist!";

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

// EOF: addContactsDebts.php
