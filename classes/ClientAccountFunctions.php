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
	* @return boolean - true/false if/not found
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
	* @return boolean - true/false if/not found
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
    * Client details array on success/false or failure/null on sql error
	* @return client/boolean/null
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
        $get = $stmt->execute(); // Execute statement
        $stmt->close(); // Close statement

		// Check for query execution
		if ($get) {
			$client = $stmt->get_result()->fetch_assoc();

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

			return null; // Return null
		}
	}


    /**
	* Function to get client by email address and password
	*
	* @param clientId - clients id
    *
    * Client details array on success/false or failure/null on sql error
	* @return client/boolean/null
	*/
	public function getClientByClientId($clientId)
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
	* Function to signup client
	*
	* @param signUpDetails - array with signup details
    *
    * Return signup details on success / boolean on failure / null on logging failed
	* @return client/boolean/null
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
                "INSERT INTO {$this->keys->constValueOf(TABLE_CLIENTS)}(
                `ClientId`, `FirstName`, `LastName`, `PhoneNumber`, `EmailAddress`, `CountryCode`, `CountryAlpha2`, `Hash`, `Gender`, `AccountType`, `SignUpDateTime`)
			VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
			VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )");
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
                    .{$this->keys->constValueOf(FIELD_EMAIL_ADDRESS)}
                    = ?");
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

			// Return false
			return false; // Return false
		}
	}


		/**
		* Function to update client profile
        *
		* @param clientId - clients Id
		* @param accountType - clients account type
		* @param updateDetails - array with associative array of fields key value pair to be updated
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

                    $firstName = $updateDetails[FIELD_FIRST_NAME]; // Get first name

                    // Add last name to update params
					$updateParams .= ", {$this->keys->constValueOf(FIELD_FIRST_NAME)} = ?";
                }

                // Check for last name
                if (array_key_exists(FIELD_LAST_NAME, $updateDetails)) {

                    $lastName = $updateDetails[FIELD_LAST_NAME]; // Get last name

                    // Add last name to update params
					$updateParams .= ", {$this->keys->constValueOf(FIELD_LAST_NAME)} = ?";
                }

                // Check for gender
                if (array_key_exists(FIELD_GENDER, $updateDetails)) {

                    $gender = $updateDetails[FIELD_GENDER]; // Get gender

                    // Add gender to update params
					$updateParams .= ", {$this->keys->constValueOf(FIELD_GENDER)} = ?";
                }

			} else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
				// Business account

				// Check for business name
                if (array_key_exists(FIELD_BUSINESS_NAME, $updateDetails)) {

	                $businessName = $updateDetails[FIELD_BUSINESS_NAME]; // Get business name

                    // Add business name to update params
					$updateParams .= ", {$this->keys->constValueOf(FIELD_BUSINESS_NAME)} = ?";
                }

                // Check for city name
                if (array_key_exists(FIELD_CITY_NAME, $updateDetails)) {

	                $cityName = $updateDetails[FIELD_CITY_NAME]; // Get city name

                    // Add city name to update params
					$updateParams .= ", {$this->keys->constValueOf(FIELD_CITY_NAME)} = ?";
                }
			}

            // Check fo phone number
            if (array_key_exists(FIELD_PHONE_NUMBER, $updateDetails)) {

                $phoneNumber = $updateDetails[FIELD_PHONE_NUMBER]; // Get phone number

                // Add phone number to update params
				$updateParams .= ", {$this->keys->constValueOf(FIELD_PHONE_NUMBER)} = ?";

            }

            // Check for email address
            if (array_key_exists(FIELD_EMAIL_ADDRESS, $updateDetails)) {

                $emailAddress = $updateDetails[FIELD_EMAIL_ADDRESS]; // Get email address

                // Add email address to update params
                $updateParams .= ", {$this->keys->constValueOf(FIELD_EMAIL_ADDRESS)} = ?";
            }

            // Check for country code
            if (array_key_exists(FIELD_COUNTRY_CODE, $updateDetails)) {

                $countryCode = $updateDetails[FIELD_COUNTRY_CODE]; // Get country code

                // Add country code to update params
                $updateParams .= ", {$this->keys->constValueOf(FIELD_COUNTRY_CODE)} = ?";
            }

            // Check for country alpha2
            if (array_key_exists(FIELD_COUNTRY_ALPHA2, $updateDetails)) {

                $countryAlpha2 = $updateDetails[FIELD_COUNTRY_ALPHA2]; // Get country alpha2

                // Add country alpha2 to update params
				$updateParams .= ", {$this->keys->constValueOf(FIELD_COUNTRY_ALPHA2)} = ?";
            }

			// Construct SQL command with the update params above
			$sqlCommand = "UPDATE {$this->keys->constValueOf(TABLE_CLIENTS)}
            SET {$updateParams} WHERE {$this->keys->constValueOf(TABLE_CLIENTS)}
            .{$this->keys->constValueOf(FIELD_CLIENT_ID)}
            = '$clientId'";

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
				if ($this->storeClientLog(LOG_TYPE_UPDATE, $updateDateTime, $clientId)) {
                    // Log stored

                    // Check if email address was updated so as to revoke email address
                    // verification after updating to a new one for re-verification
                    if (array_key_exists(FIELD_EMAIL_ADDRESS, $updateDetails)) {
						// Email address was updated

						// Revoke email address verification and check for failure
						if (!$this->mailFunctions->revokeEmailVerification($clientId)) {
                            // Email revoking failed

                            return false; // Return false
                        }
					}

                    return true; // Return false on successful update

				} else {
					// Logging failed

					// Return null
					return false; // Return false
				}

			} else {
				// Update field

				return false; // Return false
			}
		}


        /**
        * Function to change clients password
        *
        * @param clientId - Clients id
        * @param currentPassword - Clients current password for verification
        * @param newPassword - Clients new password
        */
        public function changeClientPassword($clientId, $newPassword)
        {

        }


		/**
		* Function to log client actions
		*
		* @param clientLogType - action to be logged
		* @param logTime - Time of logging
		* @param clientId - clients Id
		*/
		private function storeClientLog($clientLogType, $logDateTime, $clientId) {

			// Create ClientLogId
			$clientLogId = $this->generateUniqueId(
                "clientLog",
                TABLE_CLIENT_LOGS,
                "ClientLogId");

			$stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->keys->constValueOf(TABLE_CLIENT_LOGS)}(`ClientLogId`, `ClientLogType`, `ClientLogTime`, `ClientId`) VALUES( ?, ?, ?, ?)");
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
		*/
		public function generateUniqueId($uniqueIdKey, $tableName, $idFieldName) {

			// Loop infinitely
			while (1 == 1) {

				// Create clientId
				$uniqueId = substr(
                    $uniqueIdKey . md5(mt_rand()),
                    0,
                    LENGTH_TABLE_IDS);

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
		* Returns Salt And Encrypted Password
		*/
		private function hashPassword($password) {

			// Using BCRYPT, which will always be 60 characters.
			$options = [

				// Setting Server Cost
				'cost' => 10,
			];

			// Generate Hash
			$hash = password_hash($password, PASSWORD_BCRYPT, $options);

			// Return hash
			return $hash;
		}


		/**
		* Function To Decrypt password
        *
		* @param password, @param hash
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


        /**
        * Function to return constant value within SQL statements
        *
        * @param constant - Constants value
        */
        public function constValueOfOf($constant){
            return $constant;
        }
	}


    // EOF : ClientAccountFunctions.php
