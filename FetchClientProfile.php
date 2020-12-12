<?php

/**
* Fetch client profile details file
* This file fetches clients profile and returns response in json
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

// Enable Error Reporting
error_reporting(0);

// Call required functions classes
require_once 'classes/ClientAccountFunctions.php';
require_once 'classes/Keys.php';


// Create classes objects
$clientAccountFunctions = new ClientAccountFunctions();

// Create Json response array and initialize error to false
$response = array(KEY_ERROR => false);

// Check for set POST params
if (isset($_POST[FIELD_EMAIL_ADDRESS]) && isset($_POST[FIELD_PASSWORD])) {

    // Get values from Post
    $emailAddress   = $_POST[FIELD_EMAIL_ADDRESS]   ? $_POST[FIELD_EMAIL_ADDRESS]	: '';
    $password 		= $_POST[FIELD_PASSWORD]        ? $_POST[FIELD_PASSWORD]        : '';


    // Get client by emailAddress and password
    $client = $clientAccountFunctions->getClientByEmailAddressAndPassword(
        $emailAddress,
        $password
    );

    // Check if client Was Found
    if ($client != false) {
        // Client details found

        // Set response error to false
        $response[KEY_ERROR] = false;

        // Get account type
        $accountType = $client[FIELD_ACCOUNT_TYPE];

        // Add client business and personal account details to response array
        if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
            // Personal account

            $response[KEY_CLIENT][FIELD_FIRST_NAME]   = $client[FIELD_FIRST_NAME];
            $response[KEY_CLIENT][FIELD_LAST_NAME]    = $client[FIELD_LAST_NAME];
            $response[KEY_CLIENT][FIELD_GENDER]       = $client[FIELD_GENDER];

        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
            // Business account

            $response[KEY_CLIENT][FIELD_BUSINESS_NAME] = $client[FIELD_BUSINESS_NAME];
        }

        // Add client Details To Response Array
        $response[KEY_CLIENT][FIELD_CLIENT_ID]        = $client[FIELD_CLIENT_ID];
        $response[KEY_CLIENT][FIELD_EMAIL_ADDRESS]    = $client[FIELD_EMAIL_ADDRESS];

        // Get country flag and strip out extension type to get flag name
        $countryFlagName = str_replace(FILE_TYPE_PNG, "", $client[FIELD_COUNTRY_FLAG]);
        $response[KEY_CLIENT][FIELD_COUNTRY_FLAG]     = $countryFlagName;

        $response[KEY_CLIENT][FIELD_COUNTRY_NAME]     = $client[FIELD_COUNTRY_NAME];
        $response[KEY_CLIENT][FIELD_COUNTRY_CODE]     = $client[FIELD_COUNTRY_CODE];
        $response[KEY_CLIENT][FIELD_COUNTRY_ALPHA2]   = $client[FIELD_COUNTRY_ALPHA2];
        $response[KEY_CLIENT][FIELD_EMAIL_VERIFIED]   = $client[FIELD_EMAIL_VERIFIED];
        $response[KEY_CLIENT][FIELD_SIGN_UP_DATE_TIME] = $client[FIELD_SIGN_UP_DATE_TIME];

        // Encode and echo json response
        echo json_encode($response);

    } else {
        // Client not found

        // Set response error to true
        $response[KEY_ERROR]            = true;
        $response[KEY_ERROR_MESSAGE]    = "Please sign in to continue!";

        // Encode and echo json response
        echo json_encode($response);
    }

} else {
    // EmailAddress Or password Or both missing

    // Set error to true
    $response[KEY_ERROR]			= true;
    $response[KEY_ERROR_MESSAGE] 	= "Something went terribly wrong!";

    // Encode and echo Json response
    echo json_encode($response);
}

// EOF: FetchClientDetails.php
