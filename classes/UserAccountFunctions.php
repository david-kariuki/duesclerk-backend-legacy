<?php
// User user account functions class

error_reporting(1);

class UserAccountFunctions {

	// Connection status value variable
	private $connectToDB;
	private $fieldKeys;

  // Constructor
	function __construct() {

    // Call required functions classes
    require_once 'Connection.php';
		require_once 'FieldKeys.php';

    // Creating objects of the required Classes
    $connection 					= new Connection();

		// Initializing variable connection
    $this->connectToDB 		= $connection->Connect();
		$this->fieldKeys   		= new FieldKeys();
  }

  // Destructor
  function __destruct() {

		// Close database connection
		mysqli_close($this->connectToDB);
	}

  /**
  * Function to signup user
  * @param firstName, @param lastName, @param username, @param emailAddress, @param password, @param countryIso3, @param phoneNumber, @param dateOfBirth, @param gender
  */
  public function signupUser($firstName, $lastName, $username, $emailAddress, $password, $countryAlpha2, $phoneNumber, $dateOfBirth, $gender){

    // Create userId
    $userId = $this->generateUniqueId("user", $this->fieldKeys->keyTableUsers, "UserId");

		// Hash password
		$hash = $this->hashPassword($password);

    // Get account creation date
    $signupDateTime = $this->getUserTimestamp($countryAlpha2);

    // Insert into Users
    $stmt = $this->connectToDB->prepare("INSERT INTO {$this->fieldKeys->keyTableUsers}(`UserId`,`FirstName`,`LastName`,`Username`,`EmailAddress`,`Hash`,`CountryAlpha2`,`PhoneNumber`,`DateOfBirth`,`Gender`,`SignupDate`)
                                          VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssssssssss", $userId, $firstName, $lastName, $username, $emailAddress, $hash, $countryAlpha2, $phoneNumber, $dateOfBirth, $gender, $signupDateTime);
    $result = $stmt->execute();
    $stmt->close();

    // Check for query execution
    if ($result){
			// Signup successful

			// Store signup log
			if ($this->storeUserLog($this->fieldKeys->logTypeSignup, $signupDateTime, $userId)){

	      // Get stored values
	      $stmt = $this->connectToDB->prepare("SELECT * FROM {$this->fieldKeys->keyTableUsers} AS Users WHERE Users.UserId = ? AND Users.EmailAddress = ?");
	      $stmt->bind_param("ss", $userId, $emailAddress);
	      $stmt->execute();
	      $user = $stmt->get_result()->fetch_assoc();
	      $stmt->close();

	      // Return user details
	      return $user;
			} else {
				// Logging failed

				// Return null
				return null;
			}
    } else {
      // User details not stored

      // Return false
      return false;
    }
  }

	/**
	 * Check if username is in users table.
	 * @param username
	*/
	public function isUsernameInUsersTable($username) {

			// Check for username in users table
			$stmt = $this->connectToDB->prepare("SELECT Users.Username FROM {$this->fieldKeys->keyTableUsers} AS Users WHERE Users.Username = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$stmt->store_result();

			// Check if records found
			if ($stmt->num_rows > 0){
				// Username found

				// Close statement
				$stmt->close();

				// Return true
				return true;

			} else {
				// Username not found

				// Close statement
				$stmt->close();

				// Return false
				return false;
			}
	}

	/**
	 * Check if email address is in users table.
	 * @param emailAddress
	*/
	public function isEmailAddressInUsersTable($emailAddress) {

			// Check for email address in users table
			$stmt = $this->connectToDB->prepare("SELECT Users.EmailAddress FROM {$this->fieldKeys->keyTableUsers} AS Users WHERE Users.EmailAddress = ?");
			$stmt->bind_param("s", $emailAddress);
			$stmt->execute();
			$stmt->store_result();

			// Check if records found
			if ($stmt->num_rows > 0){
				// Email address found

				// Close statement
				$stmt->close();

				// Return true
				return true;

			} else {
				// Email address not found

				// Close statement
				$stmt->close();

				// Return false
				return false;
			}
	}

	/**
	 * Check if phone number is in users table.
	 * @param phoneNumber
	*/
  public function isPhoneNumberInUsersTable($phoneNumber){

			// Check for phone number in users table
			$stmt = $this->connectToDB->prepare("SELECT Users.PhoneNumber FROM {$this->fieldKeys->keyTableUsers} AS Users WHERE Users.PhoneNumber = ?");
      $stmt->bind_param("s", $phoneNumber);
      $stmt->execute();
      $stmt->store_result();

			// Check if records found
      if ($stmt->num_rows > 0){
        // Phone number found

				// Close statement
        $stmt->close();

				// Return true
				return true;
      } else {
        // Phone number not found

				// Close statement
				$stmt->close();

				// Return false
				return false;
      }
  }

	/**
   * Get user by email address and password
  */
  public function getUserByUsernameAndPassword($username, $password) {

		// Get User Details
		$stmt = $this->connectToDB->prepare("SELECT DISTINCT Users.*, ProfilePictures.ProfilePictureName, ProfilePictures.ProfilePictureDate, Countries.* FROM {$this->fieldKeys->keyTableUsers} AS Users
																				 LEFT OUTER JOIN {$this->fieldKeys->keyTableProfilePictures} as ProfilePictures ON ProfilePictures.UserId = Users.UserId
																				 LEFT OUTER JOIN {$this->fieldKeys->keyTableCountries} as Countries ON Countries.CountryAlpha2 = Users.CountryAlpha2
																				 WHERE Users.Username = ?");
    $stmt->bind_param("s", $username);

		// Check for query execution
    if ($stmt->execute()) {

      $user = $stmt->get_result()->fetch_assoc();
      $stmt->close();

			// Get hash from result
			$hash = $user['Hash'];

      // Verifying password
      $validity = $this->verifyPassword($password, $hash);

      // Check for password validity
      if (true == $validity) {
        // Password match

				// Return array
				return $user;
      } else {
      	// Return false

				return false;
      }
    } else {
			// User not found

			// Close statement
			$stmt->close();

			// Return null
      return null;
    }
  }

	/**
	* Function to check if profile picture exists for UserId
	* @param UserId
	*/
	public function checkForProfilePicture($userId){

		// Check if profile picture exists for user
		$stmt = $this->connectToDB->prepare("SELECT * FROM {$this->fieldKeys->keyTableProfilePictures} AS ProfilePictures WHERE ProfilePictures.UserId = ?");
		$stmt->bind_param("s", $userId);
		$stmt->execute();
		$stmt->store_result();

		// Check for affected rows
		if ($stmt->num_rows > 0){
			// Previous profile picture found

			// Close statement
			$stmt->close();

			// Return true
			return true;
		} else {
			// Profile picture not found

			// Close statement
			$stmt->close();

			// Return false
			return false;
		}
	}

	/**
		* Function to update profile picture
		* @param profilePictureName, @param userId
	*/
	public function updateProfilePicture($userId, $profilePictureName, $updateDate, $profilePictureFileType){

		// Store new profile picture
		$stmt = $this->connectToDB->prepare("INSERT INTO {$this->fieldKeys->keyTableProfilePictures}(`UserId`, `ProfilePictureName`, `ProfilePictureDate`, `ProfilePictureFileType`)
																				 VALUES ( ?, ?, ?, ?)");
		$stmt->bind_param("ssss", $userId, $profilePictureName, $updateDate, $profilePictureFileType);
		$update = $stmt->execute();
		$stmt->close();

		// Check if query executed
		if ($update){
			// Profile picture updated

			// Get profile picture details
			$stmt = $this->connectToDB->prepare("SELECT * FROM {$this->fieldKeys->keyTableProfilePictures} AS ProfilePicture WHERE ProfilePicture.UserId = ?");
			$stmt->bind_param("s", $userId);
			$stmt->execute();
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
		* Function to store user logs
		* @param userLogType, @param logTime, @param userId
	*/
	private function storeUserLog($userLogType, $logTime, $userId){

		// Create UserLogId
		$userLogId = $this->generateUniqueId("usrLog", $this->fieldKeys->keyTableUserLogs, "UserLogId");

		$stmt = $this->connectToDB->prepare("INSERT INTO {$this->fieldKeys->keyTableUserLogs}(`UserLogId`,`UserLogType`,`UserLogTime`,`UserId`) VALUES( ?, ?, ?, ?)");
		$stmt->bind_param("ssss", $userLogId, $userLogType, $logTime, $userId);

		if ($stmt->execute()){

				// Close statement
				$stmt->close();

				// Return true
				return true;
		} else {

			// Close statement
			$stmt->close();

			// Return false
			return false;
		}
	}

	/**
		* Function to generate UserId
		* @param uniqueIdKey, @param tableName, @param idFieldName
 	*/
	public function generateUniqueId($uniqueIdKey, $tableName, $idFieldName){

		while (1 == 1){

			// Create userId
			$uniqueId = substr($uniqueIdKey . md5(mt_rand()), 0, $this->fieldKeys->tableIdsLength);

			// Check if unique id is in associate table
			$stmt = $this->connectToDB->prepare("SELECT * FROM {$tableName} WHERE " . $idFieldName . " = ?");
			$stmt->bind_param("s", $uniqueId);
			$stmt->execute();
			$stmt->store_result();

			if ($stmt->num_rows == 0){
				// UniqueId does not exist

				// Close statement
				$stmt->close();

				// Break from loop
				return $uniqueId;
			}

			// Close statement
			$stmt->close();
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
		* Function to get users' timeStamp
		* @param countryAlpha2
	*/
	public function getUserTimestamp($countryAlpha2){

		// Get user timeZone
		$timeZone = $this->getUserTimezone($countryAlpha2);

		// Get Default Set Server TimeZone
		date_default_timezone_set($timeZone);

		// Get current date and time from the server
		$timeStamp 	= date("l") . " " . date('d') . ", " . date('F Y') . " " . date('H:i:s');

		// Return user timestamp
		return $timeStamp;
	}

	/**
		* Function to get user timezone by alpha2
		* @param countryAlpha2
	*/
	public function getUserTimezone($countryAlpha2){

		// Create timezone array
     $timeZone = array();

    // Get timezone by country alpha2
     $timeZone = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, strtoupper($countryAlpha2));

     // Get timezone in array at current position incase of multiple timezones in array
     $current = current($timeZone);

		// Return TimeZone
		return $current;
	}

}?>
