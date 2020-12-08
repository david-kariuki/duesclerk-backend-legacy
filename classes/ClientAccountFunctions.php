<?php

/**
* Client account functions class
* This class contains all the functions required to process a clients account
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/


error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_NOTICE|E_STRICT);

class ClientAccountFunctions
{

    // Connection status value variable
    private $connectToDB;       // Create DatabaseConnection class object
    private $keys;              // Create Keys class object
    private $mailFunctions;     // Create MailFunctions class object
    private $dateTimeFunctions; // Create DateTimeFunctions class object
    private $sharedFunctions;   // Create SharedFunctions class object

    /**
    * Class constructor
    */
    function __construct()
    {

        // Call required functions classes
        require_once 'DatabaseConnection.php';  // Call database connection php file
        require_once 'Keys.php';                // Call keys php file
        require_once 'MailFunctions.php';       // Call mail functions php file
        require_once 'DateTimeFunctions.php';   // Call date and time functions php file
        require_once 'SharedFunctions.php';     // Call shared functions php file


        // Creating objects of the required classes

        // Initialize database connection class instance
        $connectionInstance         = DatabaseConnection::getConnectionInstance();

        // Initialize connection object
        $this->connectToDB          = $connectionInstance->getDatabaseConnection();

        $this->keys                 = new Keys(); // Initialize keys object

        // Initialize MailFunctions class object
        $this->mailFunctions        = new MailFunctions();

        // Initialize DateTimeFunctions class object
        $this->dateTimeFunctions    = new DateTimeFunctions();

        // Initialize SharedFunctions class object
        $this->sharedFunctions      = new SharedFunctions();
    }


    /**
    * Class destructor
    */
    function __destruct()
    {

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
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->valueOfConst(KEY_CLIENT)}
            .{$this->keys->valueOfConst(FIELD_EMAIL_ADDRESS)}
            FROM {$this->keys->valueOfConst(TABLE_CLIENTS)}
            AS {$this->keys->valueOfConst(KEY_CLIENT)}
            WHERE {$this->keys->valueOfConst(KEY_CLIENT)}
            .{$this->keys->valueOfConst(FIELD_EMAIL_ADDRESS)} = ?"
        );
        $stmt->bind_param("s", $emailAddress); // Bind parameters
        $stmt->execute(); // Execute statement
        $stmt->store_result(); // Store result

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
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->valueOfConst(KEY_CLIENT)}
            .{$this->keys->valueOfConst(FIELD_PHONE_NUMBER)}
            FROM {$this->keys->valueOfConst(TABLE_CLIENTS)}
            AS {$this->keys->valueOfConst(KEY_CLIENT)}
            WHERE {$this->keys->valueOfConst(KEY_CLIENT)}
            .{$this->keys->valueOfConst(FIELD_PHONE_NUMBER)} = ?"
        );
        $stmt->bind_param("s", $phoneNumber); // Bind parameters
        $stmt->execute(); // Execute statement
        $stmt->store_result(); // Store result

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
    * @return array - Associative array (client details)
    * @return boolean - false - (on password mismatch)
    * @return null - on fetch failure
    */
    public function getClientByEmailAddressAndPassword($emailAddress, $password)
    {

        // Check for email in Table Clients
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->valueOfConst(KEY_CLIENT)}.*,
            {$this->keys->valueOfConst(KEY_COUNTRY)}.*
            FROM {$this->keys->valueOfConst(TABLE_CLIENTS)}
            AS {$this->keys->valueOfConst(KEY_CLIENT)}
            LEFT OUTER JOIN {$this->keys->valueOfConst(TABLE_COUNTRIES)}
            AS {$this->keys->valueOfConst(KEY_COUNTRY)}
            ON {$this->keys->valueOfConst(KEY_COUNTRY)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            = {$this->keys->valueOfConst(KEY_CLIENT)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            AND {$this->keys->valueOfConst(KEY_COUNTRY)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_CODE)}
            = {$this->keys->valueOfConst(KEY_CLIENT)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_CODE)}
            WHERE {$this->keys->valueOfConst(KEY_CLIENT)}
            .{$this->keys->valueOfConst(FIELD_EMAIL_ADDRESS)}
            = ?"
        );
        $stmt->bind_param("s", $emailAddress); // Bind parameters

        // Check for query execution
        if ($stmt->execute()) {
            // Query executed

            $client = $stmt->get_result()->fetch_assoc(); // Get result array
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
    * @return array - Associative array (client details)
    * @return boolean - false - (fetch failure)
    */
    public function getClientByClientId($clientId)
    {

        // Check for email in Table Clients
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->valueOfConst(KEY_CLIENT)}.*,
            {$this->keys->valueOfConst(KEY_COUNTRY)}.*
            FROM {$this->keys->valueOfConst(TABLE_CLIENTS)}
            AS {$this->keys->valueOfConst(KEY_CLIENT)}
            LEFT OUTER JOIN {$this->keys->valueOfConst(TABLE_COUNTRIES)}
            AS {$this->keys->valueOfConst(KEY_COUNTRY)}
            ON {$this->keys->valueOfConst(KEY_COUNTRY)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            = {$this->keys->valueOfConst(KEY_CLIENT)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            AND {$this->keys->valueOfConst(KEY_COUNTRY)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_CODE)}
            = {$this->keys->valueOfConst(KEY_CLIENT)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_CODE)}
            WHERE {$this->keys->valueOfConst(KEY_CLIENT)}
            .{$this->keys->valueOfConst(FIELD_CLIENT_ID)}
            = ?"
        );
        $stmt->bind_param("s", $clientId); // Bind parameters

        // Check for query execution
        if ($stmt->execute()) {
            // Query executed

            $client = $stmt->get_result()->fetch_assoc(); // Get result array
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
    * @return array - Associative array (client details)
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
        $clientId = $this->sharedFunctions->generateUniqueId(
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
            // Personal account

            $stmt; // Statement variable

            // Get first name, last name and gender
            $firstName   = $signUpDetails[FIELD_FIRST_NAME];
            $lastName    = $signUpDetails[FIELD_LAST_NAME];
            $gender      = $signUpDetails[FIELD_GENDER];

            // Prepare statement
            $stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->keys->valueOfConst(TABLE_CLIENTS)}(`ClientId`, `FirstName`, `LastName`, `PhoneNumber`, `EmailAddress`, `CountryCode`, `CountryAlpha2`, `Hash`, `Gender`, `AccountType`, `SignUpDateTime`)
                VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            // Bind parameters
            $stmt->bind_param("sssssssssss", $clientId, $firstName, $lastName, $phoneNumber, $emailAddress, $countryCode, $countryAlpha2, $hash, $gender, $accountType, $signupDateTime);

            $result = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
            // Business account

            // Get business name and city
            $businessName    = $signUpDetails[FIELD_BUSINESS_NAME];
            $cityName        = $signUpDetails[FIELD_CITY_NAME];

            // Prepare statement
            $stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->keys->valueOfConst(TABLE_CLIENTS)}(`ClientId`, `PhoneNumber`, `EmailAddress`, `CountryCode`, `CountryAlpha2`, `Hash`, `BusinessName`, `CityName`, `AccountType`, `SignUpDateTime`)
                VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )"
            );

            // Bind parameters
            $stmt->bind_param(
                "ssssssssss",
                $clientId, $phoneNumber, $emailAddress, $countryCode, $countryAlpha2, $hash, $businessName, $cityName, $accountType, $signupDateTime
            );

            $result = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement
        }

        // Check for query execution
        if ($result) {
            // Signup successful

            // Log signup event
            if ($this->sharedFunctions->createStoreClientLogs(
                LOG_TYPE_SIGN_UP,
                $signupDateTime,
                $clientId
                )
            ) {
                // Loging successful

                // Get stored values
                // Prepare statement
                $stmt = $this->connectToDB->prepare(
                    "SELECT * FROM {$this->keys->valueOfConst(TABLE_CLIENTS)}
                    AS {$this->keys->valueOfConst(KEY_CLIENT)}
                    WHERE {$this->keys->valueOfConst(KEY_CLIENT)}
                    .{$this->keys->valueOfConst(FIELD_CLIENT_ID)} = ?
                    AND {$this->keys->valueOfConst(KEY_CLIENT)}
                    .{$this->keys->valueOfConst(FIELD_EMAIL_ADDRESS)} = ?"
                );

                $stmt->bind_param("ss", $clientId, $emailAddress); // Bind parameters
                $stmt->execute(); // Execute statement
                $client = $stmt->get_result()->fetch_assoc(); // Get result array
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

        $updateParams = ""; // Update params

        $firstName = "";
        $lastName = "";
        $gender = "";
        $businessName = "";
        $cityName = "";
        $bindParamValues = array();

        // Insert into Clients
        if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
            // Personal account

            // Check for first name
            if (array_key_exists(FIELD_FIRST_NAME, $updateDetails)) {

                // Add last name to update params
                $updateParams .= ", {$this->keys->valueOfConst(FIELD_FIRST_NAME)} = ?";
            }

            // Check for last name
            if (array_key_exists(FIELD_LAST_NAME, $updateDetails)) {

                // Add last name to update params
                $updateParams .= ", {$this->keys->valueOfConst(FIELD_LAST_NAME)} = ?";
            }

            // Check for gender
            if (array_key_exists(FIELD_GENDER, $updateDetails)) {

                // Add gender to update params
                $updateParams .= ", {$this->keys->valueOfConst(FIELD_GENDER)} = ?";
            }

        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
            // Business account

            // Check for business name
            if (array_key_exists(FIELD_BUSINESS_NAME, $updateDetails)) {

                // Add business name to update params
                $updateParams .= ", {$this->keys->valueOfConst(FIELD_BUSINESS_NAME)} = ?";
            }

            // Check for city name
            if (array_key_exists(FIELD_CITY_NAME, $updateDetails)) {

                // Add city name to update params
                $updateParams .= ", {$this->keys->valueOfConst(FIELD_CITY_NAME)} = ?";
            }
        }

        // Check fo phone number
        if (array_key_exists(FIELD_PHONE_NUMBER, $updateDetails)) {

            // Add phone number to update params
            $updateParams .= ", {$this->keys->valueOfConst(FIELD_PHONE_NUMBER)} = ?";

        }

        // Check for email address
        if (array_key_exists(FIELD_EMAIL_ADDRESS, $updateDetails)) {

            // Add email address to update params
            $updateParams .= ", {$this->keys->valueOfConst(FIELD_EMAIL_ADDRESS)} = ?";
        }

        // Check for country code and country alpha2
        if (array_key_exists(FIELD_COUNTRY_CODE, $updateDetails)
        && array_key_exists(FIELD_COUNTRY_ALPHA2, $updateDetails)) {

            // Add country code to update params
            $updateParams .= ", {$this->keys->valueOfConst(FIELD_COUNTRY_CODE)} = ?";

            // Add country alpha2 to update params
            $updateParams .= ", {$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)} = ?";
        }

        // Check for password hash
        if (array_key_exists(FIELD_HASH, $updateDetails)) {

            // Add password hash to update params
            $updateParams .= ", {$this->keys->valueOfConst(FIELD_HASH)} = ?";
        }

        // Check for email verified field
        if (array_key_exists(FIELD_EMAIL_VERIFIED, $updateDetails)) {

            // Add password hash to update params
            $updateParams .= ", {$this->keys->valueOfConst(FIELD_EMAIL_VERIFIED)} = ?";
        }

        // Construct SQL command with the update params above
        $updateStatement = "UPDATE {$this->keys->valueOfConst(TABLE_CLIENTS)}
        SET {$updateParams} WHERE {$this->keys->valueOfConst(TABLE_CLIENTS)}
        .{$this->keys->valueOfConst(FIELD_CLIENT_ID)} = '$clientId'";

        // Remove the comma after SET keyword
        $updateStatement = str_replace("SET ,", "SET", $updateStatement);

        $count = 0; // Loop count variable
        // Get bind param value from associative array
        foreach ($updateDetails as $key => $value) {
            if ($count < count($updateDetails)) {

                // Add value to value array
                $bindParamValues[$count] = $value;
                $count++; // Increment loop count variable
            }
        }

        $stmt = $this->connectToDB->prepare($updateStatement);  // Prepare statement
        $bind_types = str_repeat("s", count($bindParamValues)); // Repeat bind data type

        // Bind params to prepared statement
        $stmt->bind_param($bind_types, ...$bindParamValues); // Bind parameters
        $update = $stmt->execute(); // Execute statement
        $stmt->close(); // Close statement

        // Get update time
        $updateDateTime = $this->dateTimeFunctions->getDefaultTimeZoneTextualDateTime();

        // Check for query execution
        if ($update) {
            // Update successful

            // Log update event
            if (array_key_exists(FIELD_HASH, $updateDetails)) {

                // Log password change
                if ($this->sharedFunctions->createStoreClientLogs(
                    LOG_TYPE_UPDATE_PASSWORD,
                    $updateDateTime,
                    $clientId
                    )
                ) {
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
                if ($this->sharedFunctions->createStoreClientLogs(
                    LOG_TYPE_UPDATE_PROFILE,
                    $updateDateTime,
                    $clientId
                    )
                ) {
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
