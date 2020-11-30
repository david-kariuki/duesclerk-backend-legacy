<?php

//    Copyright (c) 2020 by David Kariuki (dk).
//    All Rights Reserved.


// Client account functions class

error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_NOTICE|E_STRICT);

class ClientAccountFunctions
{

    /**
    * Class constructor
    */
    function __construct()
    {

        // Call required functions classes
        require_once 'DatabaseConnection.php'; // Call database connection class
        require_once 'Keys.php'; // Call keys file
        require_once 'DateTimeFunctions.php'; // Call date and time functions

        // Creating objects of the required Classes
        $connect                  = new DatabaseConnection(); // Initialize variable connection
        $this->connectToDB        = $connect->Connect();      // Initialize connection object
        $this->keys               = new Keys();               // Initialize keys object
        $this->dateTimeFunctions  = new DateTimeFunctions();  // Initialize DateTimeFunctions object
    }

    /**
    * Class destructor
    */
    function __destruct()
    {

        // Close database connection
        mysqli_close($this->connectToDB);
    }


    /**
    * Function to generate ClientId
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

            // Create clientId
            $uniqueId = substr(
                $uniqueIdKey . md5(mt_rand()),
                0,
                LENGTH_TABLE_IDS
            );

            // Check if unique id is in associate table
            $stmt = $this->connectToDB->prepare(
                "SELECT * FROM {$tableName} WHERE " . $idFieldName . " = ?"
            );
            $stmt->bind_param("s", $uniqueId);
            $stmt->execute(); // Execute SQL statement
            $stmt->store_result();

            // Check if id does/was exists/found
            if ($stmt->num_rows == 0) {
                // UniqueId does not exist

                $stmt->close(); // Close statement

                // Break from loop
                return $uniqueId;
            }

            $stmt->close(); // Close statement
        }
    }


    /**
    * Function to log client actions
    *
    * @param clientLogType - action to be logged
    * @param logTime - Time of logging
    * @param clientId - clients Id
    *
    * @return boolean - true/fasle - (log stored / not stored)
    */
    private function createStoreClientLogs($clientLogType, $logDateTime, $clientId) {

        // Create ClientLogId
        $clientLogId = $this->generateUniqueId(
            "eventLog",
            TABLE_CLIENT_LOGS,
            "ClientLogId"
        );

        $stmt = $this->connectToDB->prepare(
            "INSERT INTO {$this->keys->constValueOf(TABLE_CLIENT_LOGS)}(`ClientLogId`, `ClientLogType`, `ClientLogTime`, `ClientId`) VALUES( ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $clientLogId, $clientLogType, $logDateTime, $clientId);
        $store = $stmt->execute();
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
