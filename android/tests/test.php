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

$add = $contactFunctions->fetchContactsByUserId($userId);

echo json_encode($add);
// EOF: test.php
