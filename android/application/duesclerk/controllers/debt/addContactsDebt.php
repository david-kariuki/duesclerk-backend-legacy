<?php

/**
* Fetch user contact file
* This file adds contacts debts then returns response in json
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 - 2021 David Kariuki (dk) All Rights Reserved.
*/

// Enable error reporting
error_reporting(1);

// Call autoloader file
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

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
if (isset($_POST[FIELD_DEBT_AMOUNT]) && isset($_POST[FIELD_DEBT_DATE_ISSUED])
&& isset($_POST[FIELD_DEBT_DATE_DUE]) && isset($_POST[FIELD_USER_ID])
&& isset($_POST[FIELD_CONTACT_ID])) {

    // Get Values From POST

    // Get DebtAmount
    $debtAmount     = $_POST[FIELD_DEBT_AMOUNT]  ? $_POST[FIELD_DEBT_AMOUNT]  : '';

    // Get DebtDateIssued
    $debtDateIssued = $_POST[FIELD_DEBT_DATE_ISSUED]  ? $_POST[FIELD_DEBT_DATE_ISSUED]  : '';

    // Get DebtDateDue
    $debtDateDue    = $_POST[FIELD_DEBT_DATE_DUE]  ? $_POST[FIELD_DEBT_DATE_DUE]  : '';

    $userId         = $_POST[FIELD_USER_ID]     ? $_POST[FIELD_USER_ID]     : ''; // Get UserId
    $contactId      = $_POST[FIELD_CONTACT_ID]  ? $_POST[FIELD_CONTACT_ID]  : ''; // Get ContactId
    $contactType    = ""; // Contact type

    // Get contact by ContactId
    $getContact = $contactFunctions->getContactDetailsByContactId($contactId);

    // Check if contact fetched
    if ($getContact !== false) {
        // Contact fetched

        $contactType = $getContact[FIELD_CONTACT_TYPE]; // Get ContactType

    } else {
        // Contact does not exist

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Contact does not exist!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script
    }

    // Check for debt description
    if (isset($_POST[FIELD_DEBT_DESCRIPTION])) {

        // Get debt description
        $debtDescription  = $_POST[FIELD_DEBT_DESCRIPTION] ? $_POST[FIELD_DEBT_DESCRIPTION] : '';

        // Add debt description to debt details array
        $debtDetails[FIELD_DEBT_DESCRIPTION] = $debtDescription;
    }

    // Check and sanitize debt amount
    $debtAmount = $debtFunctions->checkAndSanitizeDebtAmount($debtAmount);

    // Add other details to debt details array
    $debtDetails[FIELD_DEBT_AMOUNT]         = $debtAmount;      // Add DebtAmount
    $debtDetails[FIELD_DEBT_DATE_ISSUED]    = $debtDateIssued;  // Add DebtDateIssued
    $debtDetails[FIELD_DEBT_DATE_DUE]       = $debtDateDue;     // Add DebtDateDue
    $debtDetails[FIELD_CONTACT_ID]          = $contactId;       // Add ContactId
    $debtDetails[FIELD_CONTACT_TYPE]        = $contactType;     // Add ContactType
    $debtDetails[FIELD_USER_ID]             = $userId;          // Add UserId

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
    // Mising fields

    // Set response error to true and add error message
    $response[KEY_ERROR]           = true;
    $response[KEY_ERROR_MESSAGE]   = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);

    exit; // Exit script
}

// EOF: fetchContactsDebts.php
