<?php

/**
* Shared functions class
* This class contains all the functions that will be shared by differebt other classes
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 - 2021 David Kariuki (dk) All Rights Reserved.
*/

// Namespace declaration
namespace duesclerk\src;

// Enable error reposrting
error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_NOTICE|E_STRICT);

// Call project classes
use duesclerk\database\DatabaseConnection;
use duesclerk\constants\Constants;
use duesclerk\mail\MailFunctions;
use duesclerk\src\DateTimeFunctions;


// Class declaration
class SharedFunctions
{

    private $databaseConnectionToDB;    // Create DatabaseConnection object
    private $constants;                 // Create Constants object
    private $mailFunctions;             // Create MailFunctions object
    private $dateTimeFunctions;         // Create DateTimeFunctions object


    /**
    * Class constructor
    */
    function __construct()
    {

        // Initialize database connection class instance
        $connectionInstance = DatabaseConnection::getConnectionInstance();

        // Initialize connection object
        $this->connectToDB  = $connectionInstance->getDatabaseConnection();

        // Initialize constants object
        $this->constants    = new Constants();

        // Initialize DateTimeFunctions object
        $this->dateTimeFunctions    = new DateTimeFunctions();
    }

    /**
    * Class destructor
    */
    function __destruct()
    {

    }


    /**
    * Function to generate UserId
    *
    * @param uniqueIdKey    - Key to be concatenated to the beginning of the uniqueId
    * @param tableName      - Table name to check for existing uniqueId
    * @param idFieldName    - Table field name to check of existing uniqueId
    * @param uniqueIdLength - Unique id length - (Short / Long id)
    *
    * @return string uniqueId
    */
    public function generateUniqueId($uniqueIdKey, $tableName, $idFieldName, $uniqueIdLength)
    {

        // Loop infinitely
        while (1 == 1) {

            // Create unique id
            $uniqueId = substr(
                $uniqueIdKey . md5(mt_rand()),
                0,
                $uniqueIdLength
            );

            // Check if unique id is in associate table
            // Prepare statement
            $stmt = $this->connectToDB->prepare(
                "SELECT * FROM {$tableName} WHERE " . $idFieldName . " = ?"
            );
            $stmt->bind_param("s", $uniqueId); // Bind parameters
            $stmt->execute(); // Execute SQL statement
            $stmt->store_result(); // Store result

            // Check if id does/was exists/found
            if ($stmt->num_rows == 0) {
                // UniqueId does not exist

                $stmt->close(); // Close statement

                return $uniqueId; // Break from loop returning uniqueId
            }

            $stmt->close(); // Close statement
        }
    }

    /**
    * Function to sanitize array elements
    * This function removes square brackets from array elements
    *
    * @param array      - Array to sanitize
    *
    * @return int       - 0 - If array size is 0
    * @return boolean   - false - If passed parameter is not an array
    * @return array     - Sanitized array
    */
    public function sanitizeArrayElements($array)
    {

        // Check if passed variable is array
        if (is_array($array)) {
            // Passed parameter is array

            // Check array size
            if (sizeof($array) > 0) {
                // Array size greater than 0

                $sanitizedArray = array(); // Array to hold new sanitized elements

                // Loop through array
                foreach ($array as $element) {

                    // Remove unwanted characters from array passed with array elements
                    $element = str_replace(
                        // Array of unwanted characters
                        array('[', ']'),
                        "",
                        $element
                    );

                    $sanitizedArray[] = $element; // Add element to sanitized array
                }

                return $sanitizedArray; // Return sanitized array

            } else {
                // Array size is 0

                return 0; // Return 0
            }
        } else {
            // Passed parameter is not an array

            return false; // Return false
        }
    }
}
