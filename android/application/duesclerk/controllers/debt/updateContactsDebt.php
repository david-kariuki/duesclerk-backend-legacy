<?php

/**
* Update contacts debt details file
*
* This file updates contacts debt details and returns response in json
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

error_reporting(1); // Enable Error Reporting

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\debt\DebtFunctions;

// Create classes objects
$debtFunctions = new DebtFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Update details associative array
$updateDetails  = array(

    FIELD_CONTACT_ID        => "",
    FIELD_DEBT_ID           => "",
    FIELD_DEBT_AMOUNT       => "",
    FIELD_DEBT_DATE_ISSUED  => "",
    FIELD_DEBT_DATE_DUE     => "",
    FIELD_DEBT_DESCRIPTION  => ""
);

// Check for set POST params
if (isset($_POST[FIELD_CONTACT_ID]) && isset($_POST[FIELD_DEBT_ID])
&& isset($_POST[FIELD_DEBT_AMOUNT])) {

    // Get Values From POST
    $contactId          = $_POST[FIELD_CONTACT_ID]          ? $_POST[FIELD_CONTACT_ID]  : '';
    $debtId             = $_POST[FIELD_DEBT_ID]             ? $_POST[FIELD_DEBT_ID]     : '';
    $debtAmount         = $_POST[FIELD_DEBT_AMOUNT]         ? $_POST[FIELD_DEBT_AMOUNT] : '';

    // Get contact by ContactId
    $getDebt = $debtFunctions->getDebtDetailsByDebtId($debtId);

    // Check if debt fetched
    if ($getDebt !== false) {
        // Debt fetched

        // Check and sanitize debt amount
        $debtAmount = $debtFunctions->checkAndSanitizeDebtAmount($debtAmount);

        // Add debt details to update details associative array
        $updateDetails[FIELD_CONTACT_ID]    = $contactId;
        $updateDetails[FIELD_DEBT_ID]       = $debtId;
        $updateDetails[FIELD_DEBT_AMOUNT]   = $debtAmount;

        // Check for DebtDateIssued
        if (isset($_POST[FIELD_DEBT_DATE_ISSUED])) {

            // Get DebtDescription from POST
            $debtDateIssued = $_POST[FIELD_DEBT_DATE_ISSUED] ? $_POST[FIELD_DEBT_DATE_ISSUED] : '';

            // Add DebtDateIssued to update details
            $updateDetails[FIELD_DEBT_DATE_ISSUED] = $debtDateIssued;
        }

        // Check for DebtDateDue
        if (isset($_POST[FIELD_DEBT_DATE_DUE])) {

            // Get DebtDateDue from POST
            $debtDateDue = $_POST[FIELD_DEBT_DATE_DUE] ? $_POST[FIELD_DEBT_DATE_DUE]    : '';

            // Add DebtDateDue to update details
            $updateDetails[FIELD_DEBT_DATE_DUE] = $debtDateDue;
        }

        // Check for DebtDescription
        if (isset($_POST[FIELD_DEBT_DESCRIPTION])) {

            // Get DebtDescription from POST
            $debtDescription = $_POST[FIELD_DEBT_DESCRIPTION] ? $_POST[FIELD_DEBT_DESCRIPTION] : '';

            // Add DebtDescription to update details
            $updateDetails[FIELD_DEBT_DESCRIPTION] = $debtDescription;
        }

        // Loop through array removing null key value pairs if any
        foreach ($updateDetails as $key => $value) {

            // Check for null
            if (is_null($value) || $value == '') {

                unset($updateDetails[$key]); // Unset key and value
            }
        }

        // Update debt
        $updateDebt = $debtFunctions->updateDebtDetails($updateDetails);

        // Check for successful contact update
        if ($updateDebt !== false) {
            // Debt updated

            // Set success message
            $response[KEY_UPDATE_DEBT][KEY_SUCCESS_MESSAGE] = "Debt updated successfully!";

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script

        } else {
            // Contact update failed

            // Set error to true
            $response[KEY_ERROR]			= true;
            $response[KEY_ERROR_MESSAGE]    = "Debt update failed!";

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script
        }
    } else {
        // Contact does not exist

        // Set response error to true and add error message
        $response[KEY_ERROR]           = true;
        $response[KEY_ERROR_MESSAGE]   = "Debt does not exist!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script
    }
} else {
    // Missing userId

    // Set error to true
    $response[KEY_ERROR]			= true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);

    exit; // Exit script
}

// EOF: updateUserContact.php
