<?php

/**
* Logs functions class
* This class contains all the functions required for logging purposes
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
use duesclerk\mail\MailFunctions;
use duesclerk\src\DateTimeFunctions;
use duesclerk\src\SharedFunctions;


// Class declaration
class LogsFunctions
{

    private $databaseConnectionToDB;    // Create DatabaseConnection object
    private $constants;                 // Create Constants object
    private $mailFunctions;             // Create MailFunctions object
    private $dateTimeFunctions;         // Create DateTimeFunctions object
    private $sharedFunctions;           // Create SharedFunctions class object


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
        $this->dateTimeFunctions = new DateTimeFunctions();

        // Initialize SharedFunctions object
        $this->sharedFunctions = new SharedFunctions();
    }

    /**
    * Class destructor
    */
    function __destruct()
    {

    }


    /**
    * Function to log user actions
    *
    * @param userLogType    - action to be logged
    * @param logTime        - Time of logging
    * @param userId         - UserId
    *
    * @return boolean       - true/fasle - (log stored / not stored)
    */
    public function createStoreUserLogs($userLogType, $logDateTime, $userId) {

        // Create UserLogId
        $userLogId = $this->sharedFunctions->generateUniqueId(
            "eventLog",
            TABLE_USER_LOGS,
            "UserLogId",
            LENGTH_TABLE_IDS_LONG
        );

        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "INSERT INTO {$this->constants->valueOfConst(TABLE_USER_LOGS)}
            (
                {$this->constants->valueOfConst(FIELD_USER_LOG_ID)},
                {$this->constants->valueOfConst(FIELD_USER_LOG_TYPE)},
                {$this->constants->valueOfConst(FIELD_USER_LOG_TIME)},
                {$this->constants->valueOfConst(FIELD_USER_ID)}
            )
            VALUES( ?, ?, ?, ?)"
        );

        // Bind parameters
        $stmt->bind_param("ssss", $userLogId, $userLogType, $logDateTime, $userId);
        $store = $stmt->execute(); // Execute statement
        $stmt->close(); // Close statement

        if ($store) {
            // Log stored

            // Return true
            return true; // Return false

        } else {

            // Return false
            return false; // Return false
        }
    }
}
