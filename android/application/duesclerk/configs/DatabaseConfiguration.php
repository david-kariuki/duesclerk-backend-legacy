<?php

/**
* Database configuration file
* This file contains all the constants required for database connection
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 - 2021 David Kariuki (dk) All Rights Reserved.
*/


// Namespace declaration
namespace duesclerk\configs;

// Enable error reporting
ini_set('display_errors', 1); // Enable displaying of errors
ini_set('display_startup_errors', 1); // Enable displaying of startup errors
ini_set('log_errors', 1); // Enabke error logging
error_reporting(E_ALL | E_NOTICE | E_STRICT); // eNable all error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable MYSQLI error reporting


// Database Configuration
define("SERVER_HOST",          "");
define("SERVER_HOST_USERNAME", "");
define("SERVER_HOST_PASSWORD", "");
define("DATABASE_NAME",        "");


// Class declaration for autoloaer visibility
class DatabaseConfiguration
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

// EOF: DatabaseConfiguration.php
