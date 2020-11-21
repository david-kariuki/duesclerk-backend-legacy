<?php

//    Copyright (c) 2020 by David Kariuki (dk).
//    All Rights Reserved.

// Mail Configuration Keys

// Email address usernames
define("EMAIL_ADDRESS_INFO_USERNAME",       "info@"     . ROOT_DOMAIN);
define("EMAIL_ADDRESS_SUPPORT_USERNAME",    "support@"  . ROOT_DOMAIN);
define("EMAIL_ADDRESS_NO_REPLY_USERNAME",   "noreply@"  . ROOT_DOMAIN);

// Email address passwords
define("EMAIL_ADDRESS_INFO_PASSWORD",       "PYo6}?;yemR~9=cOQ.");
define("EMAIL_ADDRESS_SUPPORT_PASSWORD",    "-^A2+X=?)P,&pj&1Tt");
define("EMAIL_ADDRESS_NO_REPLY_PASSWORD",   "EX&r#fjPA_U=K4V&y[");

// Specify main(mail) and backup SMTP(webdisk) servers.
define("MAIL_HOST",                         "mail."     . ROOT_DOMAIN . ";" .
                                            "webdisk."  . ROOT_DOMAIN);
define("SMTP_SECURE",                       "tls");
define("MAIL_PORT",                         25);


class MailConfiguration
{

    /**
    * Class constructor
    */
    function __construct()
    {
        require_once '../Keys.php'; // Call keys file
    }


    /**
    * Class destructor
    */
    function __destruct()
    {}

}

?>
