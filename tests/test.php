<?php


// Call required php files
require_once '../classes/UserAccountFunctions.php';
require_once '../classes/MailFunctions.php';
require_once '../classes/DateTimeFunctions.php';
require_once '../classes/SharedFunctions.php';

// Create required classes objects
$userAccountFunctions = new UserAccountFunctions();
$mailFunctions = new MailFunctions();
$dateTimeFunctions = new DateTimeFunctions();
$sharedFunctions = new SharedFunctions();

// Common fields
$emailAddress = "dkaris.k@gmail.com";
$password = "password";
$userId = "userdb3f6fb90";
$verificationTypeEmail = "VerificationEmailAccount";
$verificationTypeReset = "VerificationPasswordReset";

$check = $mailFunctions->checkForOldVerificationCode($userId, $verificationType);
echo "Check Response : " . json_encode($check);
?>
