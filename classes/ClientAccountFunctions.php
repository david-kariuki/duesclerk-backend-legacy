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
    private $mailFunctions;

	/**
    * Class constructor
    */
	function __construct()
    {

		// Call required functions classes
		require_once 'DatabaseConnection.php'; // Call database connection class
		require_once 'Keys.php'; // Call keys file
        require_once 'MailFunctions.php'; // Call mail functions class

		// Creating objects of the required Classes
		$connect              = new DatabaseConnection(); // Initialize variable connection
		$this->connectToDB    = $connect->Connect();      // Initialize connection object
        $this->mailFunctions  = new MailFunctions();      // Initialize MailFunctions object
	}

	/**
    * Class destructor
    */
	function __destruct()
    {

		// Close database connection
		// mysqli_close($this->connectToDB);
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
            "SELECT {$this->constValue(KEY_CLIENT)}.{$this->constValue(FIELD_EMAIL_ADDRESS)}
            FROM {$this->constValue(TABLE_CLIENTS)} AS {$this->constValue(KEY_CLIENT)}
            WHERE {$this->constValue(KEY_CLIENT)}.{$this->constValue(FIELD_EMAIL_ADDRESS)} = ?"
        );
		$stmt->bind_param("s", $emailAddress);
		$stmt->execute(); // Execute SQL statement
		$stmt->store_result();

		// Check if records found
		if ($stmt->num_rows > 0) {
			// Email address found

			$stmt->close(); // Close statement

			// Return true
			return true;

		} else {
			// Email address not found

			$stmt->close(); // Close statement

			// Return false
			return false;
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
            "SELECT {$this->constValue(KEY_CLIENT)}.{$this->constValue(FIELD_PHONE_NUMBER)}
            FROM {$this->constValue(TABLE_CLIENTS)} AS {$this->constValue(KEY_CLIENT)}
            WHERE {$this->constValue(KEY_CLIENT)}.{$this->constValue(FIELD_PHONE_NUMBER)} = ?"
        );
		$stmt->bind_param("s", $phoneNumber);
		$stmt->execute(); // Execute SQL statement
		$stmt->store_result();

		// Check if records found
		if ($stmt->num_rows > 0) {
			// Phone number found

			$stmt->close(); // Close statement

			// Return true
			return true;

		} else {
			// Phone number not found

			$stmt->close(); // Close statement

			// Return false
			return false;
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
            "SELECT {$this->constValue(KEY_CLIENT)}.*, {$this->constValue(KEY_COUNTRY)}.*
            FROM {$this->constValue(TABLE_CLIENTS)} AS {$this->constValue(KEY_CLIENT)}
            LEFT OUTER JOIN {$this->constValue(TABLE_COUNTRIES)}
            AS {$this->constValue(KEY_COUNTRY)}
            ON {$this->constValue(KEY_COUNTRY)}.{$this->constValue(FIELD_COUNTRY_ALPHA2)}
            = {$this->constValue(KEY_CLIENT)}.{$this->constValue(FIELD_COUNTRY_ALPHA2)}
            AND {$this->constValue(KEY_COUNTRY)}.{$this->constValue(FIELD_COUNTRY_CODE)}
            = {$this->constValue(KEY_CLIENT)}.{$this->constValue(FIELD_COUNTRY_CODE)}
            WHERE {$this->constValue(KEY_CLIENT)}.{$this->constValue(FIELD_EMAIL_ADDRESS)} = ?"
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

				return $client; // Return client details array

			} else {
				// Password mismatch

				return false;
			}
		} else {
			// Client not found

			$stmt->close(); // Close statement

			return null;
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
		$signupDateTime = $this->getClientTimestamp($countryAlpha2);

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
                "INSERT INTO {$this->constValue(TABLE_CLIENTS)}(
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
                "INSERT INTO {$this->constValue(TABLE_CLIENTS)}(`ClientId`, `PhoneNumber`, `EmailAddress`, `CountryCode`, `CountryAlpha2`, `Hash`, `BusinessName`, `CityName`, `AccountType`, `SignUpDateTime`)
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
                    "SELECT * FROM {$this->constValue(TABLE_CLIENTS)}
                    AS {$this->constValue(KEY_CLIENT)}
                    WHERE {$this->constValue(KEY_CLIENT)}.{$this->constValue(FIELD_CLIENT_ID)} = ?
                    AND {$this->constValue(KEY_CLIENT)}.{$this->constValue(FIELD_EMAIL_ADDRESS)}
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
				return null;
			}
		} else {
			// Client details not stored

			// Return false
			return false;
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
			$phoneNumber = $updateDetails[FIELD_PHONE_NUMBER];
			$emailAddress = $updateDetails[FIELD_EMAIL_ADDRESS];
			$countryCode = $updateDetails[FIELD_COUNTRY_CODE];
			$countryAlpha2 = $updateDetails[FIELD_COUNTRY_ALPHA2];
			$firstName = "";
			$lastName = "";
			$gender = "";
			$businessName = "";
			$cityName = "";
			$emailAddressUpdated = false;
			$bindParamValue = array();

			// Insert into Clients
			if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
				// Personal account

				// Get first name, last name and gender
				$firstName = $updateDetails[FIELD_FIRST_NAME];
				$lastName = $updateDetails[FIELD_LAST_NAME];
				$gender = $updateDetails[FIELD_GENDER];

				if ($firstName != '') {
					$updateParams .= ", {FIELD_FIRST_NAME} = ?"; // Add last name to update params
				}

				if ($lastName != '') {
					$updateParams .= ", {$this->constValue(FIELD_LAST_NAME)} = ?"; // Add last name to update params
				}

				if ($gender != '') {
					$updateParams .= ", {$this->constValue(FIELD_GENDER)} = ?"; // Add gender to update params
				}

			} else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
				// Business account

				// Get business name and city
				$businessName = $updateDetails[FIELD_BUSINESS_NAME];
				$cityName = $updateDetails[FIELD_CITY_NAME];

				if ($businessName != '') {
					$updateParams .= ", {$this->constValue(FIELD_BUSINESS_NAME)} = ?"; // Add business name to update params
				}

				if ($cityName != '') {
					$updateParams .= ", {$this->constValue(FIELD_CITY_NAME)} = ?"; // Add city name to update params
				}
			}

			// Other shared params
			if ($phoneNumber != '') {
				$updateParams .= ", {$this->constValue(FIELD_PHONE_NUMBER)} = ?"; // Add phone number to update params
			}

			if ($emailAddress != '') {
				$updateParams .= ", {$this->constValue(FIELD_EMAIL_ADDRESS)} = ?"; // Add email address to update params

				// Set email address updated to true so as to revoke email address verification after
				// updating to a new one for re-verification
				$emailAddressUpdated = true;
			}

			if ($countryCode != '') {
				$updateParams .= ", {$this->constValue(FIELD_COUNTRY_CODE)} = ?"; // Add country code to update params
			}

			if ($countryAlpha2 != '') {
				$updateParams .= ", {$this->constValue(FIELD_COUNTRY_ALPHA2)} = ?"; // Add country alpha2 to update params
			}

			// Combine UPDATE command with update params
			$sqlCommand = "UPDATE {$this->constValue(TABLE_CLIENTS)} SET {$updateParams}
            WHERE {$this->constValue(TABLE_CLIENTS)}.{$this->constValue(FIELD_CLIENT_ID)}
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

			// Check for query execution
			if ($update) {
				// Update successful

				// Log update event
				if ($this->storeClientLog(LOG_TYPE_UPDATE, $signupDateTime, $clientId)) {
                    // Log stored

					// Check if email address was updated
					if ($emailAddressUpdated) {
						// Email address was updated

						// Revoke email address verification
						$this->mailFunctions->revokeEmailVerification($clientId);
					}

				} else {
					// Logging failed

					// Return null
					return null;
				}

			} else {
				// Update field

				return false; // Return false
			}
		}


		/**
		* Function to log client actions
		*
		* @param clientLogType - action to be logged
		* @param logTime - Time of logging
		* @param clientId - clients Id
		*/
		private function storeClientLog($clientLogType, $logTime, $clientId) {

			// Create ClientLogId
			$clientLogId = $this->generateUniqueId(
                "clientLog",
                TABLE_CLIENT_LOGS,
                "ClientLogId");

			$stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->constValue(TABLE_CLIENT_LOGS)}(`ClientLogId`, `ClientLogType`, `ClientLogTime`, `ClientId`) VALUES( ?, ?, ?, ?)");
			$stmt->bind_param("ssss", $clientLogId, $clientLogType, $logTime, $clientId);

			if ($stmt->execute()) {

				$stmt->close(); // Close statement

				// Return true
				return true;

			} else {

				$stmt->close(); // Close statement

				// Return false
				return false;
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
				$stmt = $this->connectToDB->prepare("SELECT * FROM {$tableName} WHERE " . $idFieldName . " = ?");
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

			if (password_verify($password, $hash)) {
				// Password matches hash

				return true;

			} else {
				// Password doesn't match hash

				return false;
			}
		}


		/**
		* Function to get clients' timeStamp
        *
		* @param countryAlpha2 - country alpha2
		*/
		public function getClientTimestamp($countryAlpha2) {

			// Get client timeZone using countryAlpha2
			$timeZone = $this->getClientTimezone($countryAlpha2);

			// Get Default Set Server TimeZone
			date_default_timezone_set($timeZone);

			// Get current date and time from the server
			$timeStamp 	= date("l") . " " . date('d') . ", " . date('F Y') . " " . date('H:i:s');

			// Return client timestamp
			return $timeStamp;
		}


		/**
		* Function to get client timezone by alpha2
        *
		* @param countryAlpha2 - country alpha2
		*/
		public function getClientTimezone($countryAlpha2) {

			// Create timezone array
			$timeZone = array();

			// Get timezone by country alpha2
			$timeZone = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, strtoupper($countryAlpha2));

			// Get timezone in array at current position incase of multiple timezones in array
			$current = current($timeZone);

			// Return TimeZone
			return $current;
		}


        /**
        * Function to return constant value within SQL statements
        *
        * @param constant - Constants value
        */
        public function constValue($constant){
            return $constant;
        }
	}


	?>
