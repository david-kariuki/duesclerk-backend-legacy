<?php

/**
* User Sign In file
* This file Signs In / logs in users and returns response in json
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
require_once $_SERVER["DOCUMENT_ROOT"] . "duesclerk_php/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\user\UserAccountFunctions;

// Create Classes Objects
$userAccountFunctions = new UserAccountFunctions();

// Create JSON response array and initialize error to false
$response = array(KEY_ERROR => false);

// Check for set POST params
if (isset($_POST[FIELD_EMAIL_ADDRESS]) && isset($_POST[FIELD_PASSWORD])) {

	// Get Values From POST
	$emailAddress 	= $_POST[FIELD_EMAIL_ADDRESS]	? $_POST[FIELD_EMAIL_ADDRESS]	: '';
	$password 		= $_POST[FIELD_PASSWORD] 		? $_POST[FIELD_PASSWORD]		: '';

	// Get user by EmailAddress and password
	$getUser = $userAccountFunctions->getUserByEmailAddressAndPassword(
        $emailAddress,
        $password
    );

	// Check if user was found
	if ($getUser !== false) {
		// User found

		// Add user details to response array
		$response[KEY_SIGN_IN][FIELD_USER_ID]         = $getUser[FIELD_USER_ID];
		$response[KEY_SIGN_IN][FIELD_ACCOUNT_TYPE]    = $getUser[FIELD_ACCOUNT_TYPE];
		$response[KEY_SIGN_IN][FIELD_EMAIL_ADDRESS]   = $getUser[FIELD_EMAIL_ADDRESS];

		// Echo encoded JSON response
		echo json_encode($response);

        exit; // Exit script

	} else {
		// User Not Found

		// Check for EmailAddress in database
		if ($userAccountFunctions->isEmailAddressInUsersTable($emailAddress)) {
			// User with the EmailAddress exists in the database

			// Set response error to true and add error message
			$response[KEY_ERROR]         = true;
			$response[KEY_ERROR_MESSAGE] = "Incorrect email address or password!";

			// Echo encoded JSON response
			echo json_encode($response);

            exit; // Exit script

		} else {
			// User not found

			// Set response error to true and add error message
			$response[KEY_ERROR]         = true;
			$response[KEY_ERROR_MESSAGE] = "We didn't find an account with that emailAddress!";

			// Echo encoded JSON response
			echo json_encode($response);

            exit; // Exit script
		}
	}
} else {
	// Mising Fields

	// Set response error to true and add error message
	$response[KEY_ERROR]           = true;
	$response[KEY_ERROR_MESSAGE]   = "Something went terribly wrong!";

	// Echo encoded JSON response
	echo json_encode($response);

    exit; // Exit script
}

// EOF: signInUser.php
