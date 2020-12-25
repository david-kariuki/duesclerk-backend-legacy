<?php


/**
* Shared functions class
* This class contains all the functions that will be shared by differebt other classes
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
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
use duesclerk\configs\Constants;
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
    * @param uniqueIdKey    - key to be concatenated to uniqueId
    * @param tableName      - table name to check for existing uniqueId
    * @param idFieldName    - table field name to check of existing uniqueId
    *
    * @return string uniqueId
    */
    public function generateUniqueId($uniqueIdKey, $tableName, $idFieldName)
    {

        // Loop infinitely
        while (1 == 1) {

            // Create userId
            $uniqueId = substr(
                $uniqueIdKey . md5(mt_rand()),
                0,
                LENGTH_TABLE_IDS
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
}
