<?php

//    Copyright (c) 2020 by David Kariuki (dk).
//    All Rights Reserved.

// Mail Configuration Keys

// Call keys file
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/andr/classes/Keys.php');

// SMTP Email address usernames
define("EMAIL_ADDRESS_INFO_USERNAME",       "info@"     . ROOT_DOMAIN);
define("EMAIL_ADDRESS_SUPPORT_USERNAME",    "support@"  . ROOT_DOMAIN);
define("EMAIL_ADDRESS_NO_REPLY_USERNAME",   "noreply@"  . ROOT_DOMAIN);

// SMTP Email address passwords
define("EMAIL_ADDRESS_INFO_PASSWORD",       "PYo6}?;yemR~9=cOQ.");
define("EMAIL_ADDRESS_SUPPORT_PASSWORD",    "-^A2+X=?)P,&pj&1Tt");
define("EMAIL_ADDRESS_NO_REPLY_PASSWORD",   "EX&r#fjPA_U=K4V&y[");

// Set main(mail) and backup SMTP(webdisk) servers.
define("MAIL_HOST",         "mail."     . ROOT_DOMAIN . ";" . "webdisk."  . ROOT_DOMAIN);

define("SMTP_SECURE",       "tls");
define("MAIL_PORT",         587); // 587 (Non ssl)

// Peers
define("VERIFY_PEER",       false);
define("VERIFY_PEER_NAME",  false);

// Signing
define("ALLOW_SELF_SIGNED", true);

// Auth
define("SMTP_AUTH",         true);

// EOF: MailConfiguration.php
