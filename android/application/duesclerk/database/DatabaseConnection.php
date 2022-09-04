<?php

/**
* Database connection class
* This class contains all the functions required for database connection
* Only one connection is allowed through class instance
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 - 2022 David Kariuki (dk) All Rights Reserved.
*/


// Namespace declaration
namespace duesclerk\database;

// Enable error reporting
ini_set('display_errors', 1); // Enable displaying of errors
ini_set('display_startup_errors', 1); // Enable displaying of startup errors
ini_set('log_errors', 1); // Enabke error logging
error_reporting(E_ALL | E_NOTICE | E_STRICT); // eNable all error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable MYSQLI error reporting


// Call required classes
use duesclerk\configs\DatabaseConfiguration;


// Class declaration
class DatabaseConnection
{

    private $databaseConfiguration;     // Database configuration class object
    private $databaseConnection;        // Connection status value variable
    private static $connectionInstance; // Databse single connection instance


    /**
    * Class constructor
    */
    private function __construct()
    {

        // Check for mysql installation
        if (function_exists('mysqli_init') && extension_loaded('mysqli')) {
            // mysqli installed

            // Initialize database configuration object
            $this->databaseConfiguration = new DatabaseConfiguration();

            // Connecting to MYSQL database
            $this->databaseConnection = new \mysqli(
                SERVER_HOST,
                SERVER_HOST_USERNAME,
                SERVER_HOST_PASSWORD,
                DATABASE_NAME
            );

            // Check if connection was successful
            if ($this->databaseConnection->connect_error) {
                // Connection error

                //\trigger_error(
                //    "Connection Error: " . $this->databaseConnection->connect_error(),
                //    E_USER_ERROR
                //);
            }
        } else {
            // Throw exception


        }
    }


    /**
    * Class destructor
    */
    function __destruct()
    {

    }


    /**
    * Get an instance of the database connection
    *
    * @return self instance - connection instance
    */
    public static function getConnectionInstance()
    {
        // Check for another existent instance
        if (!self::$connectionInstance) {

            // Create new instance if not existing
            self::$connectionInstance = new self();
        }

        return self::$connectionInstance; // Return connection instance
    }


    /**
    * Clone() method
    * Leaving it empty to prevent duplication of database connection
    */
    private function __clone()
    {

    }


    /**
    * Function to get database connection
    *
    * @return mysqli - databaseConnection
    */
    public function getDatabaseConnection()
    {
        return $this->databaseConnection; // Return database connection
    }

}

// EOF: DatabaseConnection.php
