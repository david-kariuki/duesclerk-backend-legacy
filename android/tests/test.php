<?php

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required php files
use duesclerk\user\UserAccountFunctions;
use duesclerk\mail\MailFunctions;
use duesclerk\src\DateTimeFunctions;
use duesclerk\src\SharedFunctions;
use duesclerk\contact\ContactFunctions;

// Create required classes objects
$userAccountFunctions = new UserAccountFunctions();
$mailFunctions = new MailFunctions();
$dateTimeFunctions = new DateTimeFunctions();
$sharedFunctions = new SharedFunctions();
$contactFunctions = new ContactFunctions();

// Common fields
$emailAddress = "dkaris.k@gmail.com";
$password = "password";
$userId = "userb803aac84ee";
$verificationTypeEmail = "VerificationEmailAccount";
$verificationTypeReset = "VerificationPasswordReset";

//$check = $mailFunctions->checkForVerificationRequestRecord($userId, $verificationTypeEmail);
//echo "Check Response : " . json_encode($check);

// Contact details array
    $contactDetails = array(
        FIELD_CONTACT_FULL_NAME     => "",
        FIELD_CONTACT_PHONE_NUMBER  => "",
        FIELD_CONTACT_EMAIL_ADDRESS => "",
        FIELD_CONTACT_ADDRESS       => "",
        FIELD_CONTACT_TYPE          => "",
    );

    $contactDetails[FIELD_CONTACT_FULL_NAME]        = "David kariuki";
    $contactDetails[FIELD_CONTACT_PHONE_NUMBER]     = "+254700619045";
    $contactDetails[FIELD_CONTACT_EMAIL_ADDRESS]    = "dkaris.k@gmail.com";
    $contactDetails[FIELD_CONTACT_ADDRESS]          = "Eldoret, Kenya";
    $contactDetails[FIELD_CONTACT_TYPE]             = "ContactPeopleOwingMe";
$add = $contactFunctions->addContact($userId, $contactDetails);

json_encode($add);
// EOF: test.php
