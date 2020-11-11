<?php

//    Copyright (c) 2020 by David Kariuki (dk).
//    All Rights Reserved.


// Fetch Client Profile Details

// Enable Error Reporting
error_reporting(1);

// Call required functions classes
require_once 'classes/ClientAccountFunctions.php';
require_once 'classes/FieldKeys.php';


// Create classes objects
$clientAccountFunctions = new ClientAccountFunctions();
$fieldKeys		        = new FieldKeys();

// Create Json response array and initialize error to false
$response = array($fieldKeys->keyError => false);

// Receive email address and password
if (isset($_POST[$fieldKeys->keyEmailAddress]) && isset($_POST[$fieldKeys->keyPassword])) {

    // Receiving Values From Post
    $emailAddress 	= $_POST[$fieldKeys->keyEmailAddress];
    $password 		= $_POST[$fieldKeys->keyPassword];


    // Get client by emailAddress and password
    $client = $clientAccountFunctions->getClientByEmailAddressAndPassword($emailAddress, $password);

    // Check if client Was Found
    if ($client != false) {

        /**
        * Client was found
        * @param clientId, @param firstName, @param lastName, @param password, @param emailAddress, @param emailVerification, @param countryCode, @param countryName, @param phoneNumber, @param gender, @param profilePicture, @param createdAt;
        */

        // Set response error to false
        $response[$fieldKeys->keyError] = false;

        // Get account type
        $accountType = $client[$fieldKeys->keyAccountType];

        // Add client business and personal account details to response array
        if ($accountType == $fieldKeys->keyAccountTypePersonal) {
            // Personal account

            $response[$fieldKeys->keyClient][$fieldKeys->keyFirstName]   	= $client[$fieldKeys->keyFirstName];
            $response[$fieldKeys->keyClient][$fieldKeys->keyLastName] 	   	= $client[$fieldKeys->keyLastName];
            $response[$fieldKeys->keyClient][$fieldKeys->keyGender] 	    = $client[$fieldKeys->keyGender];

        } else if ($accountType == $fieldKeys->keyAccountTypeBusiness) {
            // Business account

            $response[$fieldKeys->keyClient][$fieldKeys->keyBusinessName] 	= $client[$fieldKeys->keyBusinessName];
            $response[$fieldKeys->keyClient][$fieldKeys->keyCityName] 	    = $client[$fieldKeys->keyCityName];
        }

        // Add client Details To Response Array
        $response[$fieldKeys->keyClient][$fieldKeys->keyClientId]       = $client[$fieldKeys->keyClientId];
        $response[$fieldKeys->keyClient][$fieldKeys->keyPhoneNumber] 	= $client[$fieldKeys->keyPhoneNumber];
        $response[$fieldKeys->keyClient][$fieldKeys->keyEmailAddress] 	= $client[$fieldKeys->keyEmailAddress];
        $response[$fieldKeys->keyClient][$fieldKeys->keyCountryCode]    = $client[$fieldKeys->keyCountryCode];
        $response[$fieldKeys->keyClient][$fieldKeys->keyCountryName]    = $client[$fieldKeys->keyCountryName];
        $response[$fieldKeys->keyClient][$fieldKeys->keyEmailVerified]  = $client[$fieldKeys->keyEmailVerified];
        $response[$fieldKeys->keyClient][$fieldKeys->keyAccountType]    = $accountType;

        // Encode and echo json response
        echo json_encode($response);

    } else {
        // Client not found

        // Set response error to true
        $response[$fieldKeys->keyError] 		= true;
        $response[$fieldKeys->keyErrorMessage]	= "Please sign in to continue!";

        // Encode and echo json response
        echo json_encode($response);
    }

} else {
    // EmailAddress Or password Or both missing

    // Set error to true
    $response[$fieldKeys->keyError]			= true;
    $response[$fieldKeys->keyErrorMessage] 	= "Something went terribly wrong!";

    // Encode and echo Json response
    echo json_encode($response);
}

?>
