<?php


// Call required php files
require_once '../classes/ClientAccountFunctions.php';
require_once '../classes/MailFunctions.php';
require_once '../classes/DateTimeFunctions.php';
require_once '../classes/SharedFunctions.php';

// Create required classes objects
$clientAccountFunctions = new ClientAccountFunctions();
$mailFunctions = new MailFunctions();
$dateTimeFunctions = new DateTimeFunctions();
$sharedFunctions = new SharedFunctions();

// Common fields
$emailAddress = "dkaris.k@gmail.com";
$password = "password";
$clientId = "clientdb3f6fb90";
$verificationTypeEmail = "VerificationEmailAccount";
$verificationTypeReset = "VerificationPasswordReset";

$check = $mailFunctions->checkForOldVerificationCode($clientId, $verificationType);
echo "Check Response : " . json_encode($check);
?>
