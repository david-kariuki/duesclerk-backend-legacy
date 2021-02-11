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

    // Get debt amount
    $debtAmount     = $_POST[FIELD_DEBT_AMOUNT]  ? $_POST[FIELD_DEBT_AMOUNT]  : '';

    // Get debt date issued
    $debtDateIssued = $_POST[FIELD_DEBT_DATE_ISSUED]  ? $_POST[FIELD_DEBT_DATE_ISSUED]  : '';

    // Get debt date due
    $debtDateDue    = $_POST[FIELD_DEBT_DATE_DUE]  ? $_POST[FIELD_DEBT_DATE_DUE]  : '';

    $userId         = $_POST[FIELD_USER_ID]     ? $_POST[FIELD_USER_ID]     : ''; // Get UserId
    $contactId      = $_POST[FIELD_CONTACT_ID]  ? $_POST[FIELD_CONTACT_ID]  : ''; // Get ContactId
    $contactType    = ""; // Contact type

    // Get contact by contact id
    $getContact = $contactFunctions->getContactDetailsByContactId($contactId);

    // Check if contact fetched
    if ($getContact !== false) {
        // Contact fetched

        $contactType = $getContact[FIELD_CONTACT_TYPE]; // Get contact type

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

    // Get first element of debt amount
    $firstElementOfAmount = $debtAmount[0];

    // Check if the first element is a dot
    if ($firstElementOfAmount == ".") {
        // Amount is a float with a leading dot without 0

        // Add a zero to the beggining of debt amount
        $debtAmount = "0" . $debtAmount;
    }

    // Add other details to debt details array
    $debtDetails[FIELD_DEBT_AMOUNT]         = $debtAmount;      // Add debt amount
    $debtDetails[FIELD_DEBT_DATE_ISSUED]    = $debtDateIssued;  // Add debt date issued
    $debtDetails[FIELD_DEBT_DATE_DUE]       = $debtDateDue;     // Add debt date due
    $debtDetails[FIELD_CONTACT_ID]          = $contactId;       // Add contact id
    $debtDetails[FIELD_CONTACT_TYPE]        = $contactType;     // Add contact type
    $debtDetails[FIELD_USER_ID]             = $userId;          // Add user id

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