<?php

/**
* Database configuration file
* This file contains all the constants required for database connection
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

// Namespace declaration
namespace duesclerk\configs;

// Enable error reporting
error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_NOTICE|E_STRICT);

// Database Configuration
define("SERVER_HOST",          "localhost");
define("SERVER_HOST_USERNAME", "duescler");
define("SERVER_HOST_PASSWORD", "GnU?U^btY%1m.otKfw");
define("DATABASE_NAME",        "duescler_db_duesclerk");


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
