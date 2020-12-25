<?php

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";

// Call required php files
use duesclerk\user\UserAccountFunctions;
use duesclerk\mail\MailFunctions;

// Create required classes objects
$userAccountFunctions = new UserAccountFunctions();
$mailFunctions = new MailFunctions();
//$dateTimeFunctions = new DateTimeFunctions();
//$sharedFunctions = new SharedFunctions();

// Common fields
$emailAddress = "dkaris.k@gmail.com";
$password = "password";
$userId = "userb803aac84ee";
$verificationTypeEmail = "VerificationEmailAccount";
$verificationTypeReset = "VerificationPasswordReset";

$check = $mailFunctions->checkForVerificationRequestRecord($userId, $verificationTypeEmail);
echo "Check Response : " . json_encode($check);
?>
