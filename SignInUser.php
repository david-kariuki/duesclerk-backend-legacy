<?php

/**
* User Sign In file
* This file Signs In / logs in users and returns response in json
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

// Enable Error Reporting
error_reporting(0);

// Call Required Functions Classes
require_once 'classes/UserAccountFunctions.php';
require_once 'classes/Keys.php';


// Create Classes Objects
$userAccountFunctions = new UserAccountFunctions();

// Create Json Response Array And Initialize Error To FALSE
$response = array(KEY_ERROR => false);

// Check for set POST params
if (isset($_POST[FIELD_EMAIL_ADDRESS]) && isset($_POST[FIELD_PASSWORD])) {

	// Get Values From POST
	$emailAddress 	= $_POST[FIELD_EMAIL_ADDRESS]	? $_POST[FIELD_EMAIL_ADDRESS]	: '';
	$password 		= $_POST[FIELD_PASSWORD] 		? $_POST[FIELD_PASSWORD]		: '';

	// Get user by email address and password
	$getUser = $userAccountFunctions->getUserByEmailAddressAndPassword(
        $emailAddress,
        $password
    );

	// Check if user was found
	if ($getUser !== false) {
		// User found

		// Set response error to false
		$response[KEY_ERROR] = false;

		// Add User Details To Response Array
		$response[KEY_SIGN_IN][FIELD_USER_ID]       = $getUser[FIELD_USER_ID];
		$response[KEY_SIGN_IN][FIELD_ACCOUNT_TYPE]    = $getUser[FIELD_ACCOUNT_TYPE];
		$response[KEY_SIGN_IN][FIELD_EMAIL_ADDRESS]   = $getUser[FIELD_EMAIL_ADDRESS];

		// Encode and echo Json response
		echo json_encode($response);

	} else {
		// User Not Found

		// Check For Wrong Password (Credentials Mismatch)
		if ($userAccountFunctions->isEmailAddressInUsersTable($emailAddress)) {
			// User with the emailAddress exists in the database

			// Set response error to true
			$response[KEY_ERROR]         = true;
			$response[KEY_ERROR_MESSAGE] = "Incorrect email address or password!";

			// Encode and echo Json response
			echo json_encode($response);

		} else {
			// User not found

			// Set response error to true
			$response[KEY_ERROR]         = true;
			$response[KEY_ERROR_MESSAGE] = "We didn't find an account with that emailAddress!";

			// Encode and echo Json response
			echo json_encode($response);
		}
	}
} else {
	// Mising Fields

	// Set response error to true
	$response[KEY_ERROR]           = true;
	$response[KEY_ERROR_MESSAGE]   = "Something went terribly wrong!";

	// Encode and echo Json response
	echo json_encode($response);
}

// EOF: SignInUser.php
