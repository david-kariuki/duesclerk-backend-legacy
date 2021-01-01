<?php

/**
* Mail configurations file
* This file contains all the constants required for mail processing
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 - 2021 David Kariuki (dk) All Rights Reserved.
*/

// Namespace declaration
namespace duesclerk\configs;

// SMTP Email address usernames
define("EMAIL_ADDRESS_INFO_USERNAME",       "info@"     . ROOT_DOMAIN);
define("EMAIL_ADDRESS_SUPPORT_USERNAME",    "support@"  . ROOT_DOMAIN);
define("EMAIL_ADDRESS_NO_REPLY_USERNAME",   "no-reply@" . ROOT_DOMAIN);

// SMTP Email address passwords
define("EMAIL_ADDRESS_INFO_PASSWORD",       "Z+&Yb#c-rl+v!1m*tl");
define("EMAIL_ADDRESS_SUPPORT_PASSWORD",    "LH+.g1AdpOL3+YyCL9");
define("EMAIL_ADDRESS_NO_REPLY_PASSWORD",   ";ejMn];A5!;JNPGy0}");

// Set main(mail) and backup SMTP(webdisk) servers.
define("MAIL_HOST",         "mail." . ROOT_DOMAIN . ";" . "webdisk." . ROOT_DOMAIN);

define("MAIL_PORT",         587); // 587 (Non ssl)

// Peers
define("VERIFY_PEER",       false);
define("VERIFY_PEER_NAME",  false);

// Signing
define("ALLOW_SELF_SIGNED", true);

// Auth
define("SMTP_AUTH",         true);


// Class declaration for autoloaer visibility
class MailConfiguration
{

    /**
    * Class destructor
    */
    function __construct()
    {

    }


    /**
    * Class destructor
    */
    function __destruct()
    {

    }
}

// EOF: MailConfiguration.php