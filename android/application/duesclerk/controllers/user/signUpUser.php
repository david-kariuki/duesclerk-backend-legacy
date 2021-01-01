<?php

/**
* User Sign Up file
* This file Signs Up users / creates users accounts and returns response in json
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/


error_reporting(1); // Enable error reporting

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required functions classes
use duesclerk\user\UserAccountFunctions;

// Create Classes Objects
$userAccountFunctions = new UserAccountFunctions();

// Create JSON response array and initialize error to false
$response       = array(KEY_ERROR => false);
$signUpDetails  = array(

    FIELD_FIRST_NAME        => "",
    FIELD_LAST_NAME         => "",
    FIELD_BUSINESS_NAME     => "",
    FIELD_EMAIL_ADDRESS     => "",
    FIELD_COUNTRY_CODE      => "",
    FIELD_COUNTRY_ALPHA2    => "",
    FIELD_PASSWORD          => "",
    FIELD_ACCOUNT_TYPE      => ""
);

// Required fields
$firstName      = "";
$lastName       = "";
$businessName   = "";
$emailAddress   = "";
$countryCode    = "";
$countryAlpha2  = "";

// Check for set POST params
if (
    (isset($_POST[FIELD_FIRST_NAME])        &&
    isset($_POST[FIELD_LAST_NAME]))
    ||
    isset($_POST[FIELD_BUSINESS_NAME])
    ||
    (isset($_POST[FIELD_EMAIL_ADDRESS])      &&
    isset($_POST[FIELD_COUNTRY_CODE])       &&
    isset($_POST[FIELD_COUNTRY_ALPHA2])     &&
    isset($_POST[FIELD_PASSWORD])           &&
    isset($_POST[FIELD_ACCOUNT_TYPE]))
) {

    // Get Values From POST
    $accountType = $_POST[FIELD_ACCOUNT_TYPE] ? $_POST[FIELD_ACCOUNT_TYPE] : '';

    if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
        // Personal account

        $firstName  = $_POST[FIELD_FIRST_NAME]  ? $_POST[FIELD_FIRST_NAME]  : '';
        $lastName   = $_POST[FIELD_LAST_NAME]   ? $_POST[FIELD_LAST_NAME]   : '';

    } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
        // Business account

        $businessName   = $_POST[FIELD_BUSINESS_NAME]  ? $_POST[FIELD_BUSINESS_NAME]    : '';
    }

    // Other fields
    $emailAddress   = $_POST[FIELD_EMAIL_ADDRESS]   ? $_POST[FIELD_EMAIL_ADDRESS]   : '';
    $countryCode    = $_POST[FIELD_COUNTRY_CODE]    ? $_POST[FIELD_COUNTRY_CODE]    : '';
    $countryAlpha2  = $_POST[FIELD_COUNTRY_ALPHA2]  ? $_POST[FIELD_COUNTRY_ALPHA2]  : '';
    $password       = $_POST[FIELD_PASSWORD]        ? $_POST[FIELD_PASSWORD]        : '';



    /**
    * Check email address validity
    * Check the maximum allowed length of the email address
    * Total length in RFC_3696 is 320 characters
    * The local part of the email address—your username—must not exceed 64 characters.
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


    } else if ($userAccountFunctions->isEmailAddressInUsersTable($emailAddress)) {
        // Email address exists

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_SIGN_UP]          = FIELD_EMAIL_ADDRESS;
        $response[KEY_ERROR_MESSAGE]    = "An account with that email address already exists!";

        // Echo encoded JSON response
        echo json_encode($response);

        // Check password length
    } else if (strlen($password) < LENGTH_MIN_PASSWORD) {
        // Password too short

        // Set response error to true and add error message
        $response[KEY_ERROR]            = true;
        $response[KEY_SIGN_UP]          = FIELD_PASSWORD;
        $response[KEY_ERROR_MESSAGE]    = 'Passwords should be 8 characters or longer!';

        // Echo encoded JSON response
        echo json_encode($response);

    } else {

        if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
            // Personal

            // Check if first name is alphabetical
            if (!preg_match(EXPRESSION_NAMES, $firstName)) {
                // Invalid first name

                // Set response error to true and add error message
                $response[KEY_ERROR]            = true;
                $response[KEY_ERROR_MESSAGE]    = 'The first name you entered does not appear to
                be valid!';

                // Echo encoded JSON response
                echo json_encode($response);

                // Check if last name is alphabetical
            } else if (!preg_match(EXPRESSION_NAMES, $lastName)) {
                // Invalid last name

                // Set response error to true and add error message
                $response[KEY_ERROR]            = true;
                $response[KEY_ERROR_MESSAGE]    = 'The last name you entered does not appear to
                be valid!';

                // Echo encoded JSON response
                echo json_encode($response);

                // Check first name Length
            } else if (strlen($firstName) < LENGTH_MIN_SINGLE_NAME) {
                // First name too Short

                // Set response error to true and add error message
                $response[KEY_ERROR]            = true;
                $response[KEY_SIGN_UP]          = FIELD_FIRST_NAME;
                $response[KEY_ERROR_MESSAGE]    = 'The first name you entered is too short!';

                // Echo encoded JSON response
                echo json_encode($response);

                // Check last name length
            } else if (strlen($lastName) < LENGTH_MIN_SINGLE_NAME) {
                // Last name too Short

                // Set response error to true and add error message
                $response[KEY_ERROR]          = true;
                $response[KEY_SIGN_UP]        = FIELD_LAST_NAME;
                $response[KEY_ERROR_MESSAGE]  = 'The last name you entered is too short!';

                // Echo encoded JSON response
                echo json_encode($response);

            }

            // Check for set params
            if (isset($_POST[FIELD_FIRST_NAME]) && isset($_POST[FIELD_LAST_NAME])) {

                // Add first name and last name to associative array
                $signUpDetails[FIELD_FIRST_NAME]    = $firstName;
                $signUpDetails[FIELD_LAST_NAME]     = $lastName;
            }
        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
            // Business account

            // Check for set params
            if (isset($_POST[FIELD_BUSINESS_NAME])) {

                // Add business name to associative array
                $signUpDetails[FIELD_BUSINESS_NAME] = $businessName;
            }
        }

        // Add the other fields to associative array
        $signUpDetails[FIELD_EMAIL_ADDRESS]     = $emailAddress;
        $signUpDetails[FIELD_COUNTRY_CODE]      = $countryCode;
        $signUpDetails[FIELD_COUNTRY_ALPHA2]    = $countryAlpha2;
        $signUpDetails[FIELD_PASSWORD]          = $password;
        $signUpDetails[FIELD_ACCOUNT_TYPE]      = $accountType;


        // Signup user
        $signupUser = $userAccountFunctions->signUpUser($signUpDetails);

        // Check if user was signed up
        if ($signupUser) {
            // User signed up

            // Add user details JSON response array
            $response[KEY_SIGN_UP][FIELD_USER_ID] = $signupUser[FIELD_USER_ID];

            // Check account type
            if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
                // Personal account

                $response[KEY_SIGN_UP][FIELD_FIRST_NAME]    = $signupUser[FIELD_FIRST_NAME];
                $response[KEY_SIGN_UP][FIELD_LAST_NAME]     = $signupUser[FIELD_LAST_NAME];

            } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
                // Business account

                $response[KEY_SIGN_UP][FIELD_BUSINESS_NAME] = $signupUser[FIELD_BUSINESS_NAME];
            }

            $response[KEY_SIGN_UP][FIELD_EMAIL_ADDRESS] = $signupUser[FIELD_EMAIL_ADDRESS];
            $response[KEY_SIGN_UP][FIELD_ACCOUNT_TYPE]  = $signupUser[FIELD_ACCOUNT_TYPE];

            // Echo encoded JSON response
            echo json_encode($response);

        } else {
            // Signup failed

            // Set response error to true and add error message
            $response[KEY_ERROR]            = true;
            $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

            // Echo encoded JSON response
            echo json_encode($response);
        }
    }
} else {
    // Missing params

    // Set response error to true and add error message
    $response[KEY_ERROR]            = true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Echo encoded JSON response
    echo json_encode($response);
}

// EOF: signUpUser.php
