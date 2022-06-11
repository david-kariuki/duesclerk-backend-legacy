<?php

/**
* User Sign Up file
* This file Signs Up users / creates users accounts and returns response in json
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
$response       = array(KEY_ERROR => false);
$signUpDetails  = array(

    FIELD_FULL_NAME_OR_BUSINESS_NAME    => "",
    FIELD_EMAIL_ADDRESS                 => "",
    FIELD_COUNTRY_CODE                  => "",
    FIELD_COUNTRY_ALPHA2                => "",
    FIELD_PASSWORD                      => ""
);

// Required fields
$fullNameOrBusinessName = "";
$emailAddress           = "";
$countryCode            = "";
$countryAlpha2          = "";

// Check for set POST params
if (
    isset($_POST[FIELD_FULL_NAME_OR_BUSINESS_NAME]) &&
    isset($_POST[FIELD_EMAIL_ADDRESS])              &&
    isset($_POST[FIELD_COUNTRY_CODE])               &&
    isset($_POST[FIELD_COUNTRY_ALPHA2])             &&
    isset($_POST[FIELD_PASSWORD])
) {

    // Get Values From POST
    $fullNameOrBusinessName = $_POST[FIELD_FULL_NAME_OR_BUSINESS_NAME]  ? $_POST[FIELD_FULL_NAME_OR_BUSINESS_NAME]  : '';
    $emailAddress   = $_POST[FIELD_EMAIL_ADDRESS]   ? $_POST[FIELD_EMAIL_ADDRESS]   : '';
    $countryCode    = $_POST[FIELD_COUNTRY_CODE]    ? $_POST[FIELD_COUNTRY_CODE]    : '';
    $countryAlpha2  = $_POST[FIELD_COUNTRY_ALPHA2]  ? $_POST[FIELD_COUNTRY_ALPHA2]  : '';
    $password       = $_POST[FIELD_PASSWORD]        ? $_POST[FIELD_PASSWORD]        : '';


    /**
    * Check EmailAddress validity
    * Check the maximum allowed length of the EmailAddress
    * Total length in RFC_3696 is 320 characters
    * The local part of the EmailAddress—your username—must not exceed 64 characters.
    * The domain name is limited to 255 characters.
    */
    if ((!filter_var($emailAddress, FILTER_VALIDATE_EMAIL))
    || (strlen($emailAddress) > LENGTH_MAX_EMAIL_ADDRESS)) {
        // Invalid email

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_SIGN_UP]          = FIELD_EMAIL_ADDRESS;
        $response[KEY_ERROR_MESSAGE]    = "The email address you entered is invalid!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script


    } else if ($userAccountFunctions->isEmailAddressInUsersTable($emailAddress)) {
        // EmailAddress exists

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_SIGN_UP]          = FIELD_EMAIL_ADDRESS;
        $response[KEY_ERROR_MESSAGE]    = "An account with that email address already exists!";

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

        // Check password length
    } else if (strlen($password) < LENGTH_MIN_PASSWORD) {
        // Password too short

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_SIGN_UP]          = FIELD_PASSWORD;
        $response[KEY_ERROR_MESSAGE]    = 'Passwords should be 8 characters or longer!';

        // Echo encoded JSON response
        echo json_encode($response);

        exit; // Exit script

    } else {

        // Check if FullNameOrBusinessName is alphabetical
        if (!preg_match(EXPRESSION_NAMES, $fullNameOrBusinessName)) {
            // Invalid FullNameOrBusinessName

            // Set response error to true and add error message
            $response[KEY_ERROR]            = true;
            $response[KEY_ERROR_MESSAGE]    = 'The full name or business name you entered does not appear to be valid!';

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script

            // Check FullNameOrBusinessName Length
        } else if (strlen($fullNameOrBusinessName) < LENGTH_MIN_SINGLE_NAME) {
            // First name too Short

            // Set response error to true and add error message
            $response[KEY_ERROR]            = true;
            $response[KEY_SIGN_UP]          = FIELD_FULL_NAME_OR_BUSINESS_NAME;
            $response[KEY_ERROR_MESSAGE]    = 'The full name or business name you entered is too short!';

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script
        }

        // Add the fields to associative array
        $signUpDetails[FIELD_FULL_NAME_OR_BUSINESS_NAME] = $fullNameOrBusinessName;
        $signUpDetails[FIELD_EMAIL_ADDRESS]     = $emailAddress;
        $signUpDetails[FIELD_COUNTRY_CODE]      = $countryCode;
        $signUpDetails[FIELD_COUNTRY_ALPHA2]    = $countryAlpha2;
        $signUpDetails[FIELD_PASSWORD]          = $password;

        // Signup user
        $signupUser = $userAccountFunctions->signUpUser($signUpDetails);

        // Check if user was signed up
        if ($signupUser !== false) {
            // User signed up

            // Add user details JSON response array
            $response[KEY_SIGN_UP][FIELD_USER_ID] = $signupUser[FIELD_USER_ID];
            $response[KEY_SIGN_UP][FIELD_FULL_NAME_OR_BUSINESS_NAME] = $signupUser[FIELD_FULL_NAME_OR_BUSINESS_NAME];
            $response[KEY_SIGN_UP][FIELD_EMAIL_ADDRESS] = $signupUser[FIELD_EMAIL_ADDRESS];
            $response[KEY_SIGN_UP][FIELD_ACCOUNT_TYPE]  = $signupUser[FIELD_ACCOUNT_TYPE];

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script

        } else {
            // Signup failed

            // Set response error to true and add error message
            $response[KEY_ERROR]            = true;
            $response[KEY_ERROR_MESSAGE]    = "Signup failed!";

            // Echo encoded JSON response
            echo json_encode($response);

            exit; // Exit script
        }
    }
} else {
    // Missing params

    // Set response error to true and add error message
    $response[KEY_ERROR]            = true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);

    exit; // Exit script
}

// EOF: signUpUser.php
