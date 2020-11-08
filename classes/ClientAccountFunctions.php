<?php
// Client account functions class

error_reporting(1);

class ClientAccountFunctions {

	// Connection status value variable
	private $connectToDB;
	private $fieldKeys;

	// Constructor
	function __construct() {

		// Call required functions classes
		require_once 'Connection.php';
		require_once 'FieldKeys.php';

		// Creating objects of the required Classes
		$connection 		= new Connection();

		// Initializing variable connection
		$this->connectToDB	= $connection->Connect();
		$this->fieldKeys   	= new FieldKeys();
	}

	// Destructor
	function __destruct() {
		// Close database connection
		mysqli_close($this->connectToDB);
	}


	/**
	* Function to signup client
	*
	* @param $signUpDetails - array with signup details
	* @return $client(signup details on success) or boolean(on failure)
	*/
	public function signUpClient($signUpDetails) {

		// Get phoneNumber, emailAddress, countryCode, countryAlpha2, password,
		// accountType from SignUpDetails array
		$phoneNumber = $signUpDetails[$this->fieldKeys->keyPhoneNumber];
		$emailAddress = $signUpDetails[$this->fieldKeys->keyEmailAddress];
		$countryCode = $signUpDetails[$this->fieldKeys->keyCountryCode];
		$countryAlpha2 = $signUpDetails[$this->fieldKeys->keyCountryAlpha2];
		$password = $signUpDetails[$this->fieldKeys->keyPassword];
		$accountType = $signUpDetails[$this->fieldKeys->keyAccountType];

		json_encode("haha");
		// Create clientId
		$clientId = $this->generateUniqueId("client", $this->fieldKeys->keyTableClients, "ClientId");

		// Hash password
		$hash = $this->hashPassword($password);

		// Get account creation date
		$signupDateTime = $this->getClientTimestamp($countryAlpha2);

		$result;
		// Insert into Clients
		if ($accountType == $this->fieldKeys->keyAccountTypePersonal) {

			global $stmt;

			// Get first name, last name and gender
			$firstName = $signUpDetails[$this->fieldKeys->keyFirstName];
			$lastName = $signUpDetails[$this->fieldKeys->keyLastName];
			$gender = $signUpDetails[$this->fieldKeys->keyGender];

			// Personal account
			$stmt = $this->connectToDB->prepare("INSERT INTO {$this->fieldKeys->keyTableClients}(`ClientId`,`FirstName`,`LastName`,`PhoneNumber`,`EmailAddress`,`CountryCode`,`CountryAlpha2`,`Hash`,`Gender`,`AccountType`, `SignupDateTime`)
			VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param("sssssssssss", $clientId, $firstName, $lastName, $phoneNumber, $emailAddress, $countryCode, $countryAlpha2, $hash, $gender, $accountType, $signupDateTime);
			$result = $stmt->execute(); // Execute SQL statement
			$stmt->close(); // Close statement

		} else if ($accountType == $this->fieldKeys->keyAccountTypeBusiness) {

			// Get business nae and city
			$businessName = $signUpDetails[$this->fieldKeys->keyBusinessName];
			$cityName = $signUpDetails[$this->fieldKeys->$keyCityName];

			// Business account
			$stmt = $this->connectToDB->prepare("INSERT INTO {$this->fieldKeys->keyTableClients}(`ClientId`,`PhoneNumber`,`EmailAddress`,`CountryCode`,`CountryAlpha2`,`Hash`,`BusinessName`,`CityName`,`AccountType`, `SignupDateTime`)
			VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )");
			$stmt->bind_param("ssssssssss", $clientId, $phoneNumber, $emailAddress, $countryCode, $countryAlpha2, $hash, $businessName, $cityName, $accountType, $signupDateTime);
			$result = $stmt->execute(); // Execute SQL statement
			$stmt->close(); // Close statement
		}

		// Check for query execution
		if ($result) {
			// Signup successful

			// Store signup log
			if ($this->storeClientLog($this->fieldKeys->logTypeSignUp, $signupDateTime, $clientId)) {

				// Get stored values
				$stmt = $this->connectToDB->prepare("SELECT * FROM {$this->fieldKeys->keyTableClients} AS Clients WHERE Clients.ClientId = ? AND Clients.EmailAddress = ?");
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
	* Check if email address is in clients table.
	* @param emailAddress
	*/
	public function isEmailAddressInClientsTable($emailAddress) {

		// Check for email address in clients table
		$stmt = $this->connectToDB->prepare("SELECT Clients.EmailAddress FROM {$this->fieldKeys->keyTableClients} AS Clients WHERE Clients.EmailAddress = ?");
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
	* @param phoneNumber
	* @return boolean
	*/
	public function isPhoneNumberInClientsTable($phoneNumber) {

		// Check for phone number in clients table
		$stmt = $this->connectToDB->prepare("SELECT Clients.PhoneNumber FROM {$this->fieldKeys->keyTableClients} AS Clients WHERE Clients.PhoneNumber = ?");
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
	* @param emailAddress - Clients email address
	* @param password - Clients password
	*/
	public function getClientByEmailAddressAndPassword($emailAddress, $password){

		// Check for email in Table Clients
		$stmt = $this->connectToDB->prepare("SELECT Clients.*, Countries.* FROM {$this->fieldKeys->keyTableClients} AS
			Clients LEFT OUTER JOIN {$this->fieldKeys->keyTableCountries} AS Countries ON
			Countries.CountryAlpha2 = Clients.CountryAlpha2 AND Countries.CountryCode = Clients.CountryCode
			WHERE Clients.EmailAddress = ?");
			$stmt->bind_param("s", $emailAddress);

			// Check for query execution
			if ($stmt->execute()){
				$client = $stmt->get_result()->fetch_assoc();
				$stmt->close(); // Close statement

				// Get password hash from client details array
				$hash = $client[$this->fieldKeys->keyHash];

				// Verify password
				$verify = $this->verifyPassword($password, $hash);

				// Check password validity
				if ($verify == true){
					// Pasword matches hash

					return $client; // Return client details array
				} else {
					// Password mismatch

					return false;
				}
			} else {
				// Client not found

				return null;
			}
		}


		/**
		* Function to check if profile picture exists for ClientId
		* @param ClientId
		*/
		public function checkForProfilePicture($clientId) {

			// Check if profile picture exists for client
			$stmt = $this->connectToDB->prepare("SELECT * FROM {$this->fieldKeys->keyTableProfilePictures} AS ProfilePictures WHERE ProfilePictures.ClientId = ?");
			$stmt->bind_param("s", $clientId);
			$stmt->execute(); // Execute SQL statement
			$stmt->store_result();

			// Check for affected rows
			if ($stmt->num_rows > 0) {
				// Previous profile picture found

				$stmt->close(); // Close statement

				// Return true
				return true;
			} else {
				// Profile picture not found

				$stmt->close(); // Close statement

				// Return false
				return false;
			}
		}

		/**
		* Function to update profile picture
		* @param profilePictureName,
		* @param clientId
		*/
		public function updateProfilePicture($clientId, $profilePictureName, $updateDate, $profilePictureFileType) {

			// Store new profile picture
			$stmt = $this->connectToDB->prepare("INSERT INTO {$this->fieldKeys->keyTableProfilePictures}(`ClientId`, `ProfilePictureName`, `ProfilePictureDate`, `ProfilePictureFileType`)
			VALUES ( ?, ?, ?, ?)");
			$stmt->bind_param("ssss", $clientId, $profilePictureName, $updateDate, $profilePictureFileType);
			$update = $stmt->execute(); // Execute SQL statement
			$stmt->close(); // Close statement

			// Check if query executed
			if ($update) {
				// Profile picture updated

				// Get profile picture details
				$stmt = $this->connectToDB->prepare("SELECT * FROM {$this->fieldKeys->keyTableProfilePictures} AS ProfilePicture WHERE ProfilePicture.ClientId = ?");
				$stmt->bind_param("s", $clientId);
				$stmt->execute(); // Execute SQL statement
				$profilePicture = $stmt->get_result()->fetch_assoc();

				// Return profile picture array
				return $profilePicture;
			} else {
				// Profile picture update failed

				// Return false
				return false;
			}
		}

		/**
		* Function to store client logs
		* @param clientLogType, @param logTime, @param clientId
		*/
		private function storeClientLog($clientLogType, $logTime, $clientId) {

			// Create ClientLogId
			$clientLogId = $this->generateUniqueId("clientLog", $this->fieldKeys->keyTableClientLogs, "ClientLogId");

			$stmt = $this->connectToDB->prepare("INSERT INTO {$this->fieldKeys->keyTableClientLogs}(`ClientLogId`,`ClientLogType`,`ClientLogTime`,`ClientId`) VALUES( ?, ?, ?, ?)");
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
		* @param uniqueIdKey, @param tableName, @param idFieldName
		*/
		public function generateUniqueId($uniqueIdKey, $tableName, $idFieldName) {

			while (1 == 1) {

				// Create clientId
				$uniqueId = substr($uniqueIdKey . md5(mt_rand()), 0, $this->fieldKeys->tableIdsLength);

				// Check if unique id is in associate table
				$stmt = $this->connectToDB->prepare("SELECT * FROM {$tableName} WHERE " . $idFieldName . " = ?");
				$stmt->bind_param("s", $uniqueId);
				$stmt->execute(); // Execute SQL statement
				$stmt->store_result();

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
		* @param password
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
		* @param password, @param hash
		*/
		private function verifyPassword($password, $hash) {

			if (password_verify($password, $hash)) {
				// Password is valid

				return true;
			} else {
				// Invalid password

				return false;
			}
		}

		/**
		* Function to get clients' timeStamp
		* @param countryAlpha2
		*/
		public function getClientTimestamp($countryAlpha2) {

			// Get client timeZone
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
		* @param countryAlpha2
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
	}

	?>
