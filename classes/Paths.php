<?php

/**
* Paths file
* This file contains defined paths to required project resources
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

// Paths Class

// Project directory path
define("PATH_PROJECT_DIRECTORY",    dirname(__DIR__) . "/");

// Images directory paths
define("PATH_IMAGES_FOLDER",        PATH_PROJECT_DIRECTORY . "images/");
define("PATH_PHP_MAILER_IMAGES",    PATH_IMAGES_FOLDER . "php_mailer_images/");

// Images paths
define(
    "PATH_IMAGE_ACCOUNT_EMAIL_VERIFICATION",
PATH_PHP_MAILER_IMAGES . "account_email_verification.png"
);

define(
    "PATH_IMAGE_PASSWORD_RESET_EMAIL_VERIFICATION",
    PATH_PHP_MAILER_IMAGES . "password_reset_email_verification.png"
);

// EOF: Paths.php
