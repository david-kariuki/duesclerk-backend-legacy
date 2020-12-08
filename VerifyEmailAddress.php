<?php

/**
* Send verify email address file
* This file verifies email address by comparing stored verification code to that sent on
* mail and returns response in json
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/


// Enable Error Reporting
error_reporting(1);

// Call Required Functions Classes
require_once 'classes/ClientAccountFunctions.php';  // Client account functions php file
require_once 'classes/DateTimeFunctions.php';       // DateTimeFunctions php class
require_once 'classes/MailFunctions.php';           // MailFunctions php file
require_once 'classes/Keys.php';

// Create Classes Objects
$clientAccountFunctions = new ClientAccountFunctions();
$dateTimeFunctions      = new DateTimeFunctions();
$mailFunctions          = new MailFunctions();

// Create Json Response Array And Initialize Error To FALSE
$response = array(KEY_ERROR => false);

// Associative array to update email verification status
$updateDetails = array(FIELD_EMAIL_VERIFIED => "false");

// Check for set POST params
if (isset($_POST[FIELD_CLIENT_ID]) && isset($_POST[FIELD_VERIFICATION_TYPE])
&& isset($_POST[FIELD_VERIFICATION_CODE])){

    // Get values from POST params
    $clientId           = $_POST[FIELD_CLIENT_ID]           ? $_POST[FIELD_CLIENT_ID] : '';
    $verificationType   = $_POST[FIELD_VERIFICATION_TYPE]   ? $_POST[FIELD_VERIFICATION_TYPE] : '';
    $verificationCode   = $_POST[FIELD_VERIFICATION_CODE]   ? $_POST[FIELD_VERIFICATION_CODE] : '';

    // Check if client had requested verification
    $check = $mailFunctions->checkForVerificationRequestRecord($clientId, $verificationType);

    // Check if record was found
    if ($check !== false) {
        // Verification record found

        // Verify clients verification code
        if ($mailFunctions->verifyEmaiVerificationCode(
            $clientId,
            $verificationType,
            $verificationCode
        )) {
            // Verification code matched ClientId

            $updateDetails[FIELD_EMAIL_VERIFIED] = "true"; // Set email verified value

            // Update email verified field in clients table
            $update = $clientAccountFunctions->updateClientProfile(
                $clientId,
                "",
                $updateDetails
            );

            // Check if update was successful
            if ($update !== false) {
                // Email verified field update successful

                // Get client details
                $client = $clientAccountFunctions->getClientByClientId($clientId);

                // Get email verified field value
                $emailVerified = $client[FIELD_EMAIL_VERIFIED];

                // Check if email verified value is true
                if ($emailVerified == "true") {
                    // Email verified field updated successfully

                    // Delete email verification record
                    if ($mailFunctions->deleteEmailVerificationDetails(
                        $clientId,
                        $verificationType
                    )) {
                        // Email verification details deleted successfully

                        // Set response error to false
                        $response[KEY_ERROR]            = false;
                        $response[KEY_SUCCESS_MESSAGE]  = "Your email address has been verified!";

                        // Encode and echo json response
                        echo json_encode($response);

                    } else {
                        // Email verification details deletion failed

                        // Set response error to true
                        $response[KEY_ERROR]            = true;
                        $response[KEY_ERROR_MESSAGE]    = "Email verification details not deleted!";

                        // Encode and echo json response
                        echo json_encode($response);
                    }
                }
            } else {
                // Email verified field update failed

                // Set response error to true
                $response[KEY_ERROR]            = true;
                $response[KEY_ERROR_MESSAGE]    = "Email address verification failed!";

                // Encode and echo json response
                echo json_encode($response);
            }
        } else {
            //  Wrong verification code passed

            // Set response error to true
            $response[KEY_ERROR]            = true;
            $response[KEY_ERROR_MESSAGE]    = "Your verification code does not exist!";

            // Encode and echo json response
            echo json_encode($response);
        }
    } else {
        // Verification code record not found

        // Set response error to true
        $response[KEY_ERROR]            = true;
        $response[KEY_ERROR_MESSAGE]    = "You have not requested an email verification!";

        // Encode and echo json response
        echo json_encode($response);
    }
} else {
    // Missing params

    // Set response error to true
    $response[KEY_ERROR]            = true;
    $response[KEY_ERROR_MESSAGE]    = "Something went terribly wrong!";

    // Encode and echo json response
    echo json_encode($response);
}

// EOF: VerifyEmailAddress.php
