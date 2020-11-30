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

    // Connection status value variable
    private $connectToDB;
    //private $keys;
    private $mailFunctions;
    private $dateTimeFunctions;

    /**
    * Class constructor
    */
    function __construct()
    {

        // Call required functions classes
        require_once 'DatabaseConnection.php'; // Call database connection class
        require_once 'Keys.php'; // Call keys file
        require_once 'MailFunctions.php'; // Call mail functions class
        require_once 'DateTimeFunctions.php'; // Call date and time functions

        // Creating objects of the required Classes
        $connect                  = new DatabaseConnection(); // Initialize variable connection
        $this->connectToDB        = $connect->Connect();      // Initialize connection object
        $this->keys               = new Keys();               // Initialize keys object
        $this->mailFunctions      = new MailFunctions();      // Initialize MailFunctions object
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
    * Check if email address is in clients table.
    *
    * @param emailAddress
    *
    * @return boolean - true/false - (if/not found)
    */
    public function isEmailAddressInClientsTable($emailAddress)
    {

        // Check for email address in clients table
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->constValueOf(KEY_CLIENT)}
            .{$this->keys->constValueOf(FIELD_EMAIL_ADDRESS)}
            FROM {$this->keys->constValueOf(TABLE_CLIENTS)}
            AS {$this->keys->constValueOf(KEY_CLIENT)}
            WHERE {$this->keys->constValueOf(KEY_CLIENT)}
            .{$this->keys->constValueOf(FIELD_EMAIL_ADDRESS)} = ?"
        );
        $stmt->bind_param("s", $emailAddress);
        $stmt->execute(); // Execute SQL statement
        $stmt->store_result();

        // Check if records found
        if ($stmt->num_rows > 0) {
            // Email address found

            $stmt->close(); // Close statement

            // Return true
            return true; // Return false

        } else {
            // Email address not found

            $stmt->close(); // Close statement

            // Return false
            return false; // Return false
        }
    }


    /**
    * Check if phone number is in clients table.
    *
    * @param phoneNumber - clients phone number
    *
    * @return boolean - true/false - (if/not found)
    */
    public function isPhoneNumberInClientsTable($phoneNumber)
    {

        // Check for phone number in clients table
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->constValueOf(KEY_CLIENT)}
            .{$this->keys->constValueOf(FIELD_PHONE_NUMBER)}
            FROM {$this->keys->constValueOf(TABLE_CLIENTS)}
            AS {$this->keys->constValueOf(KEY_CLIENT)}
            WHERE {$this->keys->constValueOf(KEY_CLIENT)}
            .{$this->keys->constValueOf(FIELD_PHONE_NUMBER)} = ?"
        );
        $stmt->bind_param("s", $phoneNumber);
        $stmt->execute(); // Execute SQL statement
        $stmt->store_result();

        // Check if records found
        if ($stmt->num_rows > 0) {
            // Phone number found

            $stmt->close(); // Close statement

            // Return true
            return true; // Return false

        } else {
            // Phone number not found

            $stmt->close(); // Close statement

            // Return false
            return false; // Return false
        }
    }


    /**
    * Function to get client by email address and password
    *
    * @param emailAddress - clients email address
    * @param password - clients password
    *
    * @return array - client details
    * @return boolean - false - (on password mismatch)
    * @return null - on fetch failure
    */
    public function getClientByEmailAddressAndPassword($emailAddress, $password)
    {

        // Check for email in Table Clients
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->constValueOf(KEY_CLIENT)}.*,
            {$this->keys->constValueOf(KEY_COUNTRY)}.*
            FROM {$this->keys->constValueOf(TABLE_CLIENTS)}
            AS {$this->keys->constValueOf(KEY_CLIENT)}
            LEFT OUTER JOIN {$this->keys->constValueOf(TABLE_COUNTRIES)}
            AS {$this->keys->constValueOf(KEY_COUNTRY)}
            ON {$this->keys->constValueOf(KEY_COUNTRY)}
            .{$this->keys->constValueOf(FIELD_COUNTRY_ALPHA2)}
            = {$this->keys->constValueOf(KEY_CLIENT)}
            .{$this->keys->constValueOf(FIELD_COUNTRY_ALPHA2)}
            AND {$this->keys->constValueOf(KEY_COUNTRY)}
            .{$this->keys->constValueOf(FIELD_COUNTRY_CODE)}
            = {$this->keys->constValueOf(KEY_CLIENT)}
            .{$this->keys->constValueOf(FIELD_COUNTRY_CODE)}
            WHERE {$this->keys->constValueOf(KEY_CLIENT)}
            .{$this->keys->constValueOf(FIELD_EMAIL_ADDRESS)}
            = ?"
        );
        $stmt->bind_param("s", $emailAddress);

        // Check for query execution
        if ($stmt->execute()) {
            $client = $stmt->get_result()->fetch_assoc();

            $stmt->close(); // Close statement

            // Get password hash from client details array
            $hash = $client[FIELD_HASH];

            // Verify password
            $verify = $this->verifyPassword($password, $hash);

            // Check password validity
            if ($verify == true) {
                // Pasword matches hash

                // Get current sign up date and time
                $signUpDateTime = $client[FIELD_SIGN_UP_DATE_TIME];
                $countryAlpha2  = $client[FIELD_COUNTRY_ALPHA2];

                // Update signup date to clients local time
                $client[FIELD_SIGN_UP_DATE_TIME] = $this->dateTimeFunctions->getLocalTime($signUpDateTime, $countryAlpha2);

                return $client; // Return client details array

            } else {
                // Password mismatch

                return false; // Return false
            }
        } else {
            // Client not found

            $stmt->close(); // Close statement

            return null; // Return null
        }
    }


    /**
    * Function to get client by email address and password
    *
    * @param clientId - clients id
    *
    * @return array - cleint details
    * @return boolean - false - (fetch failure)
    */
    private function getClientByClientId($clientId)
    {

        // Check for email in Table Clients
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->constValueOf(KEY_CLIENT)}.*,
            {$this->keys->constValueOf(KEY_COUNTRY)}.*
            FROM {$this->keys->constValueOf(TABLE_CLIENTS)}
            AS {$this->keys->constValueOf(KEY_CLIENT)}
            LEFT OUTER JOIN {$this->keys->constValueOf(TABLE_COUNTRIES)}
            AS {$this->keys->constValueOf(KEY_COUNTRY)}
            ON {$this->keys->constValueOf(KEY_COUNTRY)}
            .{$this->keys->constValueOf(FIELD_COUNTRY_ALPHA2)}
            = {$this->keys->constValueOf(KEY_CLIENT)}
            .{$this->keys->constValueOf(FIELD_COUNTRY_ALPHA2)}
            AND {$this->keys->constValueOf(KEY_COUNTRY)}
            .{$this->keys->constValueOf(FIELD_COUNTRY_CODE)}
            = {$this->keys->constValueOf(KEY_CLIENT)}
            .{$this->keys->constValueOf(FIELD_COUNTRY_CODE)}
            WHERE {$this->keys->constValueOf(KEY_CLIENT)}
            .{$this->keys->constValueOf(FIELD_CLIENT_ID)}
            = ?"
        );
        $stmt->bind_param("s", $clientId);

        // Check for query execution
        if ($stmt->execute()) {
            $client = $stmt->get_result()->fetch_assoc();

            $stmt->close(); // Close statement

            // Get current sign up date and time
            $signUpDateTime = $client[FIELD_SIGN_UP_DATE_TIME];
            $countryAlpha2  = $client[FIELD_COUNTRY_ALPHA2];

            // Update signup date to clients local time
            $client[FIELD_SIGN_UP_DATE_TIME] = $this->dateTimeFunctions->getLocalTime($signUpDateTime, $countryAlpha2);

            return $client; // Return client details array

        } else {
            // Client not found

            $stmt->close(); // Close statement

            return false; // Return false
        }
    }


    /**
    * Function to signup client
    *
    * @param signUpDetails - array with signup details
    *
    * @return array - client details
    * @return boolean - false (on signup failure)
    * @return null - on logging failed
    */
    public function signUpClient($signUpDetails) {

        // Get phoneNumber, emailAddress, countryCode, countryAlpha2, password,
        // accountType from SignUpDetails array
        $phoneNumber      = $signUpDetails[FIELD_PHONE_NUMBER];
        $emailAddress     = $signUpDetails[FIELD_EMAIL_ADDRESS];
        $countryCode      = $signUpDetails[FIELD_COUNTRY_CODE];
        $countryAlpha2    = $signUpDetails[FIELD_COUNTRY_ALPHA2];
        $password         = $signUpDetails[FIELD_PASSWORD];
        $accountType      = $signUpDetails[FIELD_ACCOUNT_TYPE];

        // Create clientId
        $clientId = $this->generateUniqueId(
            strtolower(KEY_CLIENT),
            TABLE_CLIENTS,
            FIELD_CLIENT_ID
        );

        // Hash password
        $hash = $this->hashPassword($password);

        // Get account creation date
        $signupDateTime = $this->dateTimeFunctions->getDefaultTimeZoneDateTime();

        $result;
        // Insert into Clients
        if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {

            global $stmt;

            // Get first name, last name and gender
            $firstName   = $signUpDetails[FIELD_FIRST_NAME];
            $lastName    = $signUpDetails[FIELD_LAST_NAME];
            $gender      = $signUpDetails[FIELD_GENDER];

            // Personal account
            $stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->keys->constValueOf(TABLE_CLIENTS)}(`ClientId`, `FirstName`, `LastName`, `PhoneNumber`, `EmailAddress`, `CountryCode`, `CountryAlpha2`, `Hash`, `Gender`, `AccountType`, `SignUpDateTime`) VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssssssssss", $clientId, $firstName, $lastName, $phoneNumber, $emailAddress, $countryCode, $countryAlpha2, $hash, $gender, $accountType, $signupDateTime);
            $result = $stmt->execute(); // Execute SQL statement
            $stmt->close(); // Close statement

        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {

            // Get business name and city
            $businessName    = $signUpDetails[FIELD_BUSINESS_NAME];
            $cityName        = $signUpDetails[FIELD_CITY_NAME];

            // Business account
            $stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->keys->constValueOf(TABLE_CLIENTS)}(`ClientId`, `PhoneNumber`, `EmailAddress`, `CountryCode`, `CountryAlpha2`, `Hash`, `BusinessName`, `CityName`, `AccountType`, `SignUpDateTime`)
                VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )"
            );
            $stmt->bind_param("ssssssssss", $clientId, $phoneNumber, $emailAddress, $countryCode, $countryAlpha2, $hash, $businessName, $cityName, $accountType, $signupDateTime);
            $result = $stmt->execute(); // Execute SQL statement
            $stmt->close(); // Close statement
        }

        // Check for query execution
        if ($result) {
            // Signup successful

            // Log signup event
            if ($this->storeClientLog(LOG_TYPE_SIGN_UP, $signupDateTime, $clientId)) {
                // Loging successful

                // Get stored values
                $stmt = $this->connectToDB->prepare(
                    "SELECT * FROM {$this->keys->constValueOf(TABLE_CLIENTS)}
                    AS {$this->keys->constValueOf(KEY_CLIENT)}
                    WHERE {$this->keys->constValueOf(KEY_CLIENT)}
                    .{$this->keys->constValueOf(FIELD_CLIENT_ID)} = ?
                    AND {$this->keys->constValueOf(KEY_CLIENT)}
                    .{$this->keys->constValueOf(FIELD_EMAIL_ADDRESS)} = ?"
                );
                $stmt->bind_param("ss", $clientId, $emailAddress);
                $stmt->execute();
                $client = $stmt->get_result()->fetch_assoc();
                $stmt->close(); // Close statement

                // Return client details
                return $client;

            } else {
                // Logging failed

                // Return null
                return null; // Return null
            }
        } else {
            // Client details not stored

            return false; // Return false
        }
    }


    /**
    * Function to update client profile
    *
    * @param clientId - clients Id
    * @param accountType - clients account type
    * @param updateDetails - array with associative array of fields key value pair to be updated
    *
    * @return boolean - true/false (on revokation success / revokation failure)
    * @return boolean - true/false (on email sent / email not sent)
    * @return - null/0 (on logging failed/on update failure)
    */
    public function updateClientProfile($clientId, $accountType, $updateDetails) {
        // Get details from array

        // Get phoneNumber, emailAddress, countryCode, countryAlpha2, password,
        // accountType from update details array
        $updateParams = "";

        $firstName = "";
        $lastName = "";
        $gender = "";
        $businessName = "";
        $cityName = "";
        $bindParamValue = array();

        // Insert into Clients
        if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
            // Personal account

            // Check for first name
            if (array_key_exists(FIELD_FIRST_NAME, $updateDetails)) {

                // Add last name to update params
                $updateParams .= ", {$this->keys->constValueOf(FIELD_FIRST_NAME)} = ?";
            }

            // Check for last name
            if (array_key_exists(FIELD_LAST_NAME, $updateDetails)) {

                // Add last name to update params
                $updateParams .= ", {$this->keys->constValueOf(FIELD_LAST_NAME)} = ?";
            }

            // Check for gender
            if (array_key_exists(FIELD_GENDER, $updateDetails)) {

                // Add gender to update params
                $updateParams .= ", {$this->keys->constValueOf(FIELD_GENDER)} = ?";
            }

        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
            // Business account

            // Check for business name
            if (array_key_exists(FIELD_BUSINESS_NAME, $updateDetails)) {

                // Add business name to update params
                $updateParams .= ", {$this->keys->constValueOf(FIELD_BUSINESS_NAME)} = ?";
            }

            // Check for city name
            if (array_key_exists(FIELD_CITY_NAME, $updateDetails)) {

                // Add city name to update params
                $updateParams .= ", {$this->keys->constValueOf(FIELD_CITY_NAME)} = ?";
            }
        }

        // Check fo phone number
        if (array_key_exists(FIELD_PHONE_NUMBER, $updateDetails)) {

            // Add phone number to update params
            $updateParams .= ", {$this->keys->constValueOf(FIELD_PHONE_NUMBER)} = ?";

        }

        // Check for email address
        if (array_key_exists(FIELD_EMAIL_ADDRESS, $updateDetails)) {

            // Add email address to update params
            $updateParams .= ", {$this->keys->constValueOf(FIELD_EMAIL_ADDRESS)} = ?";
        }

        // Check for country code and country alpha2
        if (array_key_exists(FIELD_COUNTRY_CODE, $updateDetails)
        && array_key_exists(FIELD_COUNTRY_ALPHA2, $updateDetails)) {

            // Add country code to update params
            $updateParams .= ", {$this->keys->constValueOf(FIELD_COUNTRY_CODE)} = ?";

            // Add country alpha2 to update params
            $updateParams .= ", {$this->keys->constValueOf(FIELD_COUNTRY_ALPHA2)} = ?";
        }

        // Check for password hash
        if (array_key_exists(FIELD_HASH, $updateDetails)) {

            // Add password hash to update params
            $updateParams .= ", {$this->keys->constValueOf(FIELD_HASH)} = ?";
        }

        // Construct SQL command with the update params above
        $sqlCommand = "UPDATE {$this->keys->constValueOf(TABLE_CLIENTS)}
        SET {$updateParams} WHERE {$this->keys->constValueOf(TABLE_CLIENTS)}
        .{$this->keys->constValueOf(FIELD_CLIENT_ID)} = '$clientId'";

        // Remove the comma after SET keyword
        $updateProfileSQLStatement = str_replace("SET ,", "SET", $sqlCommand);

        $count = 0; // Loop count variable
        // Get bind param value from associative array
        foreach ($updateDetails as $key => $value) {
            if ($count < count($updateDetails)) {

                // Add value to value array
                $bindParamValue[$count] = $value;
                $count++; // Increment loop count variable
            }
        }

        // Prepare statement
        $stmt = $this->connectToDB->prepare($updateProfileSQLStatement);
        $bind_types = str_repeat("s", count($bindParamValue));

        // Bind params to prepared statement
        $stmt->bind_param($bind_types, ...$bindParamValue);
        $update = $stmt->execute();
        $stmt->close(); // Close statement

        // Get update time
        $updateDateTime = $this->dateTimeFunctions->getDefaultTimeZoneDateTime();

        // Check for query execution
        if ($update) {
            // Update successful

            // Log update event
            if (array_key_exists(FIELD_HASH, $updateDetails)) {

                // Log password change
                if ($this->storeClientLog(LOG_TYPE_UPDATE_PASSWORD, $updateDateTime, $clientId)) {
                    // Log stored

                    // Get client details
                    $client = $this->getClientByClientId($clientId);

                    // Get firsnName and email address
                    $firstName      = $client[FIELD_FIRST_NAME];
                    $emailAddress   = $client[FIELD_EMAIL_ADDRESS];

                    // Notify user of password change on email
                    if ($this->mailFunctions->sendPasswordChangeNotificationMail(
                        $firstName,
                        $emailAddress
                        ) !== false
                    ) {
                        // Email sent

                        return true; // Return true

                    } else {
                        // Email not sent

                        return false; // Return false
                    }
                } else {
                    // Logging failed

                    return null; // Return null
                }
            } else {

                // Log profile update
                if ($this->storeClientLog(LOG_TYPE_UPDATE_PROFILE, $updateDateTime, $clientId)) {
                    // Log stored

                    // Check if email address was updated so as to revoke email address
                    // verification after updating to a new one for re-verification
                    if (array_key_exists(FIELD_EMAIL_ADDRESS, $updateDetails)) {
                        // Email address was updated

                        // Revoke email address verification and check for failure
                        if (!$this->mailFunctions->revokeEmailVerification($clientId)) {
                            // Email revoking failed

                            return false; // Return false on revokation failure
                        }
                    }

                    return true; // Return true on successful update

                } else {
                    // Logging failed

                    return null; // Return null
                }
            }
        } else {
            // Update field

            return 0; // Return false
        }
    }


    /**
    * Function to update clients password
    *
    * @param clientId - Clients id
    * @param currentPassword - Clients current password for verification
    * @param newPassword - Clients new password
    *
    * @return boolean true/false/0 - password updated/not updated/ password not verified
    */
    public function updateClientPassword($clientId, $currentPassword, $newPassword)
    {
        // Get clients profile details
        $client = $this->getClientByClientId($clientId);

        // Check if client details were fetched
        if ($client !== false) {
            // Client details fetched

            // Get hash from client details
            $hash = $client[FIELD_HASH];

            // Verify password and check validity
            if ($this->verifyPassword($currentPassword, $hash)) {
                // Pasword matches hash

                // Hash new password
                $hash = $this->hashPassword($newPassword);

                // Create clientDetails array and add hash
                $clientDetails = array(FIELD_HASH => $hash);

                // Update hash in database
                if ($this->updateClientProfile($clientId, "", $clientDetails) !== false) {
                    // Password updated successfully

                    return true; // Return true
                } else {
                    // Password update unsuccessful

                    return false; // Return false
                }
            } else {
                // Password mismatch

                return 0; // Return zero
            }
        } else {
            // Client details fetching failed

            return null; // Return null
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
    private function storeClientLog($clientLogType, $logDateTime, $clientId) {

        // Create ClientLogId
        $clientLogId = $this->generateUniqueId(
            "clientLog",
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

            // Return true
            return true; // Return false
        } else {

            // Return false
            return false; // Return false
        }
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
    public function generateUniqueId($uniqueIdKey, $tableName, $idFieldName) {

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
    * Function To Encrypt Password
    *
    * @param password - clients password
    *
    * @return string - Hashed Password
    */
    private function hashPassword($password) {

        // Using BCRYPT, which will always be 60 characters.
        $options = [

            // Setting Server Cost
            'cost' => 10,
        ];

        // Return password hash
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }


    /**
    * Function To Decrypt password
    *
    * @param password, @param hash
    *
    * @return boolean - true/false (password verified / not verified)
    */
    private function verifyPassword($password, $hash) {

        // Verify password
        if (password_verify($password, $hash)) {
            // Password matches hash

            return true; // Return false

        } else {
            // Password doesn't match hash

            return false; // Return false
        }
    }
}


// EOF : ClientAccountFunctions.php
