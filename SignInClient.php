<?php

// Client Signin

// Enable Error Reporting
error_reporting(1);

// Call Required Functions Classes
require_once 'classes/ClientAccountFunctions.php';
require_once 'classes/FieldKeys.php';


// Create Classes Objects
$clientAccountFunctions	= new ClientAccountFunctions();
$fieldKeys				= new FieldKeys();

// Create Json Response Array And Initialize Error To FALSE
$response = array($fieldKeys->keyError => false);

// Receive Email Address And Password
if (isset($_POST[$fieldKeys->keyEmailAddress]) && isset($_POST[$fieldKeys->keyPassword])) {

	// Get Values From POST
	$emailAddress 	= $_POST[$fieldKeys->keyEmailAddress]	? $_POST[$fieldKeys->keyEmailAddress]	: '';
	$password 		= $_POST[$fieldKeys->keyPassword] 		? $_POST[$fieldKeys->keyPassword]		: '';

	// Get client by email address and password
	$getClient = $clientAccountFunctions->getClientByEmailAddressAndPassword($emailAddress, $password);

	// Check if client was found
	if ($getClient !== false) {
		// Client found

		// Set response error to false
		$response[$fieldKeys->keyError] = false;

		// Add Client Details To Response Array
		$response[$fieldKeys->keySignIn][$fieldKeys->keyClientId]   	= $getClient[$fieldKeys->keyClientId];
		$response[$fieldKeys->keySignIn][$fieldKeys->keyEmailAddress]	= $getClient[$fieldKeys->keyEmailAddress];
		$response[$fieldKeys->keySignIn][$fieldKeys->keyPassword] 		= $password;

		// Encode and echo Json response
		echo json_encode($response);

	} else {
		// Client Not Found

		// Check For Wrong Password (Credentials Mismatch)
		if ($clientAccountFunctions->isEmailAddressInClientsTable($emailAddress)) {
			// Client with the emailAddress exists in the database

			// Set response error to true
			$response[$fieldKeys->keyError]        = true;
			$response[$fieldKeys->keyErrorMessage] = "Incorrect email address or password!";

			// Encode and echo Json response
			echo json_encode($response);

		} else {
			// Client not found

			// Set response error to true
			$response[$fieldKeys->keyError]         = true;
			$response[$fieldKeys->keyErrorMessage]  = "We didn't find an account with that emailAddress!";

			// Encode and echo Json response
			echo json_encode($response);
		}
	}
} else {
	// Mising Fields

	// Set response error to true
	$response[$fieldKeys->keyError]          = true;
	$response[$fieldKeys->keyErrorMessage]   = "Something went terribly wrong!";

	//Return Response
	echo json_encode($response);
}

?>
