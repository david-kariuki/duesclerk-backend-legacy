<?php

/**
* Delete contacts debts file
* This file deletes user contacts debts from database and returns response in json
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

// Enable Error Reporting
error_reporting(1);

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\debt\DebtFunctions;

// Create Classes Objects
$debtFunctions = new DebtFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Check for set POST params
if (isset($_POST[KEY_DEBTS_IDS]) && isset($_POST[FIELD_CONTACT_ID])) {

    // Get Values From POST
    $debtsIds   = $_POST[KEY_DEBTS_IDS]     ? $_POST[KEY_DEBTS_IDS]     : ''; // Get debts ids
    $contactId  = $_POST[FIELD_CONTACT_ID]  ? $_POST[FIELD_CONTACT_ID]  : ''; // Get contact id

    // Decode JSON array with debts ids into PHP array
    $debtsIds = json_decode($debtsIds, true);

    // Delete debt(s)
    $deleteDebts = $debtFunctions->deleteContactsDebts($debtsIds, $contactId);

    if ($deleteDebts == false) {
        // Debt(s) not deleted

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Debt not deleted!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

    } else if ($deleteDebts == true) {
        // Debts(s) deleted

        // Set success message
        $response[KEY_DELETE_DEBTS][KEY_SUCCESS_MESSAGE] = "Debt deleted!";

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
} else {
    // Mising fields

    // Set response error to true and add error message
    $response[KEY_ERROR]           = true;
    $response[KEY_ERROR_MESSAGE]   = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);

    exit; // Exit script
}

// EOF: deleteDebts.php
