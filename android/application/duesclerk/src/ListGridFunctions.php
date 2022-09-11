<?php

/**
* List and grid functions class
* This class contains all the list and grid functions required throught the project
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 - 2022 David Kariuki (dk) All Rights Reserved.
*/


// Namespace declaration
namespace duesclerk\src;

// Enable error reporting
ini_set('display_errors', 1); // Enable displaying of errors
ini_set('display_startup_errors', 1); // Enable displaying of startup errors
ini_set('log_errors', 1); // Enabke error logging
error_reporting(E_ALL | E_NOTICE | E_STRICT); // eNable all error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable MYSQLI error reporting


// Call project classes
use duesclerk\database\DatabaseConnection;
use duesclerk\constants\Constants;
use duesclerk\src\DateTimeFunctions;
use duesclerk\src\SharedFunctions;


// Class declaration
class ListGridFunctions{

    // Connection status value variable
    private $connectToDB;
    private $constants;


    // Constructor
    function __construct() {

        // Create and initialize required classes objects
        // Initialize database connection class instance
        $connectionInstance = DatabaseConnection::getConnectionInstance();

        // Initialize connection object
        $this->connectToDB      = $connectionInstance->getDatabaseConnection();

        $this->constants        = new Constants();      // Initialize constants object

    }

    // Destructor
    function __destruct() {

        // Close database connection
        mysqli_close($this->connectToDB);
    }


    /**
    * Function To empty countries table
    * @param null
    */
    public function emptyCountriesTable() {

        // Check ff table has data
        $stmt = $this->connectToDB->prepare("SELECT * FROM {$this->constants->valueOfConst(TABLE_COUNTRIES)}");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            // Table not empty

            // Empty table
            $stmt = $this->connectToDB->prepare("DELETE FROM {$this->constants->valueOfConst(TABLE_COUNTRIES)}");

            // Check if query executed
            if ($stmt->execute()) {
                // Table emptied

                // Close statement
                $stmt->close();

                // Return true
                return true;
            } else {
                // Table not emptied

                // Close statement
                $stmt->close();

                // Return false
                return false;
            }
        } else {
            // Table is empty

            // Return null
            return null;
        }
    }


    /**
    * Function to load countries data
    * @param countryId, @param countryName, @param countryCode, @param countryAlpha2, @param countryAlpha3, @param countryFlag
    */
    public function loadCountriesTable($countryId, $countryName, $countryCode, $countryAlpha2, $countryAlpha3, $countryFlag) {

        // Insert into table
        $stmt = $this->connectToDB->prepare("INSERT INTO {$this->constants->valueOfConst(TABLE_COUNTRIES)}(CountryId, CountryName, CountryCode, CountryAlpha2, CountryAlpha3, CountryFlag) VALUES( ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $countryId, $countryName, $countryCode, $countryAlpha2, $countryAlpha3, $countryFlag);

        // Check for query execution
        if ($stmt->execute()) {
            // Query executed

            // Close statement
            $stmt->close();

            // Return true
            return true;
        } else {
            // Query failed

            // Close statement
            $stmt->close();

            // Return false
            return false;
        }
    }


    /**
    * Function to fetch countries data
    * @param null
    */
    public function fetchCountries() {

        // Get countries data
        $stmt = $this->connectToDB->prepare("SELECT * FROM {$this->constants->valueOfConst(TABLE_COUNTRIES)}");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        // Check for query execution
        if ($result) {
            // Query executed

            // Create countries array
            $countries= array();

            // Loop through result
            while ($countryItem = $result->fetch_assoc()) {

                // Add countryItem to array position
                $countriesList[] = $countryItem;
            }

            // Return countries data
            return $countriesList;
        } else {
            // Query failed

            // Return false
            return false;
        }
    }

}

// EOF: ListGridFunctions.php
