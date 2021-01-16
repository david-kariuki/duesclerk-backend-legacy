<?php

/**
* Paths file
* This file contains defined paths to required project resources
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 - 2021 David Kariuki (dk) All Rights Reserved.
*/

// Paths Class

// Namespace declaration
namespace duesclerk\constants;

// Project directory path
define("PATH_DIRECTORY_PROJECT",    $_SERVER["DOCUMENT_ROOT"]   . "/android"    . "/");

// Resources directory path
define("PATH_DIRECTORY_RESOURCES",  PATH_DIRECTORY_PROJECT      . "resources"   . "/");

// Images directory paths
define("PATH_DIRECTORY_IMAGES",     PATH_DIRECTORY_RESOURCES    . "images"      . "/");
define("PATH_DIRECTORY_IMAGES_PHP_MAILER",    PATH_DIRECTORY_IMAGES . "php_mailer"  . "/");

// Image paths
define(
    "IMAGE_ACCOUNT_EMAIL_VERIFICATION",
    PATH_DIRECTORY_IMAGES_PHP_MAILER . "account_email_verification.png"
);

define(
    "IMAGE_PASSWORD_RESET_EMAIL_VERIFICATION",
    PATH_DIRECTORY_IMAGES_PHP_MAILER . "password_reset_email_verification.png"
);


// Class declaration for autoloaer visibility
class Paths
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

// EOF: Paths.php
