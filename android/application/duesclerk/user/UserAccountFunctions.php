<?php

/**
* User account functions class
* This class contains all the functions required to manage user accounts
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 - 2021 David Kariuki (dk) All Rights Reserved.
*/

// Namespace declaration
namespace duesclerk\user;

// Enable error reporting
error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_NOTICE|E_STRICT);

// Call project classes
use duesclerk\database\DatabaseConnection;
use duesclerk\constants\Constants;
use duesclerk\mail\MailFunctions;
use duesclerk\src\SharedFunctions;
use duesclerk\src\LogsFunctions;
use duesclerk\src\DateTimeFunctions;


// Class declaration
class UserAccountFunctions
{

    // Connection status value variable
    private $connectToDB;       // Create DatabaseConnection class object
    private $constants;         // Create Constants class object
    private $mailFunctions;     // Create MailFunctions class object
    private $sharedFunctions;   // Create SharedFunctions class object
    private $logsFunctions;     // Create LogsFunctions class object
    private $dateTimeFunctions; // Create DateTimeFunctions class object


    /**
    * Class constructor
    */
    function __construct()
    {

        // Creating objects of the required classes

        // Initialize database connection class instance
        $connectionInstance = DatabaseConnection::getConnectionInstance();

        // Initialize connection object
        $this->connectToDB      = $connectionInstance->getDatabaseConnection();
        $this->constants        = new Constants();      // Initialize constants object
        $this->mailFunctions    = new MailFunctions();  // Initialize MailFunctions class object
        $this->sharedFunctions  = new SharedFunctions(); // Initialize SharedFunctions class object
        $this->logsFunctions    = new LogsFunctions();  // Initialize SharedFunctions class object

        // Initialize DateTimeFunctions class object
        $this->dateTimeFunctions = new DateTimeFunctions();
    }


    /**
    * Class destructor
    */
    function __destruct()
    {

    }


    /**
    * Function to check if EmailAddress is in users table.
    *
    * @param emailAddress   - Users EmailAddress
    *
    * @return boolean       - true/false - (if/not found)
    */
    public function isEmailAddressInUsersTable($emailAddress)
    {

        // Check for EmailAddress in users table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_EMAIL_ADDRESS)}
            FROM {$this->constants->valueOfConst(TABLE_USERS)}
            AS {$this->constants->valueOfConst(KEY_USER)}
            WHERE {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_EMAIL_ADDRESS)} = ?"
        );
        $stmt->bind_param("s", $emailAddress); // Bind parameters
        $stmt->execute(); // Execute statement
        $stmt->store_result(); // Store result

        // Check if records found
        if ($stmt->num_rows > 0) {
            // EmailAddress found

            $stmt->close(); // Close statement

            // Return true
            return true; // Return false

        } else {
            // EmailAddress not found

            $stmt->close(); // Close statement

            // Return false
            return false; // Return false
        }
    }


    /**
    * Function to get user by EmailAddress and password
    *
    * @param emailAddress   - users EmailAddress
    * @param password       - users password
    *
    * @return array         - Associative array (user details)
    * @return boolean       - false - (on password mismatch)
    * @return null          - on fetch failure
    */
    public function getUserByEmailAddressAndPassword($emailAddress, $password)
    {

        // Check for email in Table Users
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_USER)}.*,
            {$this->constants->valueOfConst(KEY_COUNTRY)}.*
            FROM {$this->constants->valueOfConst(TABLE_USERS)}
            AS {$this->constants->valueOfConst(KEY_USER)}
            LEFT OUTER JOIN {$this->constants->valueOfConst(TABLE_COUNTRIES)}
            AS {$this->constants->valueOfConst(KEY_COUNTRY)}
            ON {$this->constants->valueOfConst(KEY_COUNTRY)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            = {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            AND {$this->constants->valueOfConst(KEY_COUNTRY)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_CODE)}
            = {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_CODE)}
            WHERE {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_EMAIL_ADDRESS)}
            = ?"
        );
        $stmt->bind_param("s", $emailAddress); // Bind parameters

        // Check for query execution
        if ($stmt->execute()) {
            // Query executed

            $user = $stmt->get_result()->fetch_assoc(); // Get result array
            $stmt->close(); // Close statement

            // Get password hash from user details array
            $hash = $user[FIELD_HASH];

            // Verify password
            $verify = $this->verifyPassword($password, $hash);

            // Check password validity
            if ($verify == true) {
                // Pasword matches hash

                // Get current sign up date and time
                $signUpDateTime = $user[FIELD_SIGN_UP_DATE_TIME];
                $countryAlpha2  = $user[FIELD_COUNTRY_ALPHA2];

                // Update signup date to users local time
                $user[FIELD_SIGN_UP_DATE_TIME] = $this->dateTimeFunctions->getLocalTime(
                    $signUpDateTime,
                    $countryAlpha2
                );

                return $user; // Return user details array

            } else {
                // Password mismatch

                return false; // Return false
            }
        } else {
            // User not found

            $stmt->close(); // Close statement

            return null; // Return null
        }
    }


    /**
    * Function to get user by userId
    *
    * @param userId     - UserId
    *
    * @return array     - Associative array (user details)
    * @return boolean   - false - (fetch failure)
    */
    public function getUserByUserId($userId)
    {

        // Check for email in Table Users
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_USER)}.*,
            {$this->constants->valueOfConst(KEY_COUNTRY)}.*
            FROM {$this->constants->valueOfConst(TABLE_USERS)}
            AS {$this->constants->valueOfConst(KEY_USER)}
            LEFT OUTER JOIN {$this->constants->valueOfConst(TABLE_COUNTRIES)}
            AS {$this->constants->valueOfConst(KEY_COUNTRY)}
            ON {$this->constants->valueOfConst(KEY_COUNTRY)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            = {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            AND {$this->constants->valueOfConst(KEY_COUNTRY)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_CODE)}
            = {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_CODE)}
            WHERE {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_USER_ID)}
            = ?"
        );
        $stmt->bind_param("s", $userId); // Bind parameters

        // Check for query execution
        if ($stmt->execute()) {
            // Query executed

            $user = $stmt->get_result()->fetch_assoc(); // Get result array
            $stmt->close(); // Close statement

            // Get current sign up date and time
            $signUpDateTime = $user[FIELD_SIGN_UP_DATE_TIME];
            $countryAlpha2  = $user[FIELD_COUNTRY_ALPHA2];

            // Update signup date to users local time
            $user[FIELD_SIGN_UP_DATE_TIME] = $this->dateTimeFunctions->getLocalTime(
                $signUpDateTime,
                $countryAlpha2
            );

            return $user; // Return user details array

        } else {
            // User not found

            $stmt->close(); // Close statement

            return false; // Return false
        }
    }


    /**
    * Function to get user by EmailAddress
    *
    * @param emailAddress   - users EmailAddress
    *
    * @return array         - Associative array (user details)
    * @return boolean       - false - (fetch failure)
    */
    public function getUserByEmailAddress($emailAddress)
    {

        // Check for email in users table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_USER)}.*,
            {$this->constants->valueOfConst(KEY_COUNTRY)}.*
            FROM {$this->constants->valueOfConst(TABLE_USERS)}
            AS {$this->constants->valueOfConst(KEY_USER)}
            LEFT OUTER JOIN {$this->constants->valueOfConst(TABLE_COUNTRIES)}
            AS {$this->constants->valueOfConst(KEY_COUNTRY)}
            ON {$this->constants->valueOfConst(KEY_COUNTRY)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            = {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            AND {$this->constants->valueOfConst(KEY_COUNTRY)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_CODE)}
            = {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_COUNTRY_CODE)}
            WHERE {$this->constants->valueOfConst(KEY_USER)}
            .{$this->constants->valueOfConst(FIELD_EMAIL_ADDRESS)}
            = ?"
        );
        $stmt->bind_param("s", $emailAddress); // Bind parameters

        // Check for query execution
        if ($stmt->execute()) {
            // Query executed

            $user = $stmt->get_result()->fetch_assoc(); // Get result array
            $stmt->close(); // Close statement

            // Get current sign up date and time
            $signUpDateTime = $user[FIELD_SIGN_UP_DATE_TIME];
            $countryAlpha2  = $user[FIELD_COUNTRY_ALPHA2];

            // Update signup date to users local time
            $user[FIELD_SIGN_UP_DATE_TIME] = $this->dateTimeFunctions->getLocalTime(
                $signUpDateTime,
                $countryAlpha2
            );

            return $user; // Return user details array

        } else {
            // User not found

            $stmt->close(); // Close statement

            return false; // Return false
        }
    }


    /**
    * Function to signup user
    *
    * @param signUpDetails  - array with signup details
    *
    * @return array         - Associative array (user details)
    * @return boolean       - false (on signup failure)
    * @return null          - on logging failed
    */
    public function signUpUser($signUpDetails) {

        // Get emailAddress, countryCode, countryAlpha2, password from SignUpDetails array
        $emailAddress     = $signUpDetails[FIELD_EMAIL_ADDRESS];
        $countryCode      = $signUpDetails[FIELD_COUNTRY_CODE];
        $countryAlpha2    = $signUpDetails[FIELD_COUNTRY_ALPHA2];
        $password         = $signUpDetails[FIELD_PASSWORD];
        $accountType      = KEY_ACCOUNT_TYPE_FREE;

        // Create userId
        $userId = $this->sharedFunctions->generateUniqueId(
            strtolower(KEY_USER),
            TABLE_USERS,
            FIELD_USER_ID,
            LENGTH_TABLE_IDS_SHORT
        );

        // Hash password
        $hash = $this->hashPassword($password);

        // Get account creation date
        $signupDateTime = $this->dateTimeFunctions->getDefaultTimeZoneTextualDateTime(
            FORMAT_DATE_TIME_FULL
        );

        // Get FullNameOrBusinessName
        $fullNameOrBusinessName = $signUpDetails[FIELD_FULL_NAME_OR_BUSINESS_NAME];

        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "INSERT INTO {$this->constants->valueOfConst(TABLE_USERS)}
            (
                {$this->constants->valueOfConst(FIELD_USER_ID)},
                {$this->constants->valueOfConst(FIELD_FULL_NAME_OR_BUSINESS_NAME)},
                {$this->constants->valueOfConst(FIELD_EMAIL_ADDRESS)},
                {$this->constants->valueOfConst(FIELD_COUNTRY_CODE)},
                {$this->constants->valueOfConst(FIELD_COUNTRY_ALPHA2)},
                {$this->constants->valueOfConst(FIELD_HASH)},
                {$this->constants->valueOfConst(FIELD_ACCOUNT_TYPE)},
                {$this->constants->valueOfConst(FIELD_SIGN_UP_DATE_TIME)}
            )
            VALUES( ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        // Bind parameters
        $stmt->bind_param("ssssssss", $userId, $fullNameOrBusinessName, $emailAddress, $countryCode, $countryAlpha2, $hash, $accountType, $signupDateTime);

        $result = $stmt->execute(); // Execute statement
        $stmt->close(); // Close statement

        // Check for query execution
        if ($result) {
            // Signup successful

            // Log signup event
            if ($this->logsFunctions->createStoreUserLogs(
                LOG_TYPE_SIGN_UP,
                $signupDateTime,
                $userId
                )
            ) {
                // Loging successful

                // Return user details
                return $this->getUserByUserId($userId);

            } else {
                // Logging failed

                // Return null
                return null; // Return null
            }
        } else {
            // User details not stored

            return false; // Return false
        }
    }


    /**
    * Function to update user profile
    *
    * @param userId         - UserId
    * @param updateDetails  - associative array of user fields key value pair to be updated
    *
    * @return boolean       - true/false (on revokation success / revokation failure)
    * @return boolean       - true/false (on email sent / email not sent)
    * @return null          - on logging failed
    * @return int           - 0 - (on update failure)
    */
    public function updateUserProfile($userId, $updateDetails)
    {
        // Get details from array

        // Get emailAddress, countryCode, countryAlpha2, password from update details array

        $updateParams = ""; // Update params
        $fullNameOrBusinessName = ""; // FullNameOrBusinessName
        $bindParamValues = array(); // Param values array

        // Check for FullNameOrBusinessName
        if (array_key_exists(FIELD_FULL_NAME_OR_BUSINESS_NAME, $updateDetails)) {

            // Add FullNameOrBusinessName to update params
            $updateParams .= ", {$this->constants->valueOfConst(FIELD_FULL_NAME_OR_BUSINESS_NAME)} = ?";
        }

        // Check for EmailAddress
        if (array_key_exists(FIELD_EMAIL_ADDRESS, $updateDetails)) {

            // Add EmailAddress to update params
            $updateParams .= ", {$this->constants->valueOfConst(FIELD_EMAIL_ADDRESS)} = ?";
        }

        // Check for CountryCode and CountryAlpha2
        if (array_key_exists(FIELD_COUNTRY_CODE, $updateDetails)
        && array_key_exists(FIELD_COUNTRY_ALPHA2, $updateDetails)) {

            // Add CountryCode to update params
            $updateParams .= ", {$this->constants->valueOfConst(FIELD_COUNTRY_CODE)} = ?";

            // Add CountryAlpha2 to update params
            $updateParams .= ", {$this->constants->valueOfConst(FIELD_COUNTRY_ALPHA2)} = ?";
        }

        // Check for password hash
        if (array_key_exists(FIELD_HASH, $updateDetails)) {

            // Add password hash to update params
            $updateParams .= ", {$this->constants->valueOfConst(FIELD_HASH)} = ?";
        }

        // Check for EmailVerified field
        if (array_key_exists(FIELD_EMAIL_VERIFIED, $updateDetails)) {

            // Add password hash to update params
            $updateParams .= ", {$this->constants->valueOfConst(FIELD_EMAIL_VERIFIED)} = ?";
        }

        // Construct SQL command with the update params above
        $updateStatement = "UPDATE {$this->constants->valueOfConst(TABLE_USERS)}
        SET {$updateParams} WHERE {$this->constants->valueOfConst(TABLE_USERS)}
        .{$this->constants->valueOfConst(FIELD_USER_ID)} = '$userId'";

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
        $updateDateTime = $this->dateTimeFunctions->getDefaultTimeZoneTextualDateTime(
            FORMAT_DATE_TIME_FULL
        );

        // Check for query execution
        if ($update) {
            // Update successful

            // Log update event
            if (array_key_exists(FIELD_HASH, $updateDetails)) {

                // Log password change
                if ($this->logsFunctions->createStoreUserLogs(
                    LOG_TYPE_UPDATE_PASSWORD,
                    $updateDateTime,
                    $userId
                    )
                ) {
                    // Log stored

                    // Get user details
                    $user = $this->getUserByUserId($userId);

                    // Get FullNameOrBusinessName and EmailAddress
                    $fullNameOrBusinessName = $user[FIELD_FULL_NAME_OR_BUSINESS_NAME];
                    $emailAddress = $user[FIELD_EMAIL_ADDRESS];

                    // Notify user of password change on email
                    if ($this->mailFunctions->sendPasswordChangeNotificationMail(
                        $fullNameOrBusinessName,
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
                if ($this->logsFunctions->createStoreUserLogs(
                    LOG_TYPE_UPDATE_PROFILE,
                    $updateDateTime,
                    $userId
                    )
                ) {
                    // Log stored

                    // Check if EmailAddress was updated so as to revoke EmailAddress
                    // verification after updating to a new one for re-verification
                    if (array_key_exists(FIELD_EMAIL_ADDRESS, $updateDetails)) {
                        // EmailAddress was updated

                        // Revoke EmailAddress verification and check for failure
                        if (!$this->mailFunctions->revokeEmailVerification($userId)) {
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
    * Function to update and reset users password
    *
    * @param userId             - UserId
    * @param currentPassword    - Users current password for verification
    * @param newPassword        - Users new password
    *
    * @return boolean           - true/false/0 - password updated/not updated
    * @return null              - on user details fetching failed
    * @return int               - 0 - (on password not verified)
    */
    public function updateUserPassword($userId, $currentPassword, $newPassword)
    {

        // Associative array to store update details
        $updateDetails = array(FIELD_HASH => "");

        // Check if current password is empty
        if (!empty($currentPassword)) {
            // Password update

            // Get users profile details
            $user = $this->getUserByUserId($userId);

            // Check if user details were fetched
            if ($user !== false) {
                // User details fetched

                // Get hash from user details
                $hash = $user[FIELD_HASH];

                // Verify password and check validity
                if (!$this->verifyPassword($currentPassword, $hash)) {
                    // Pasword mismatch

                    return 0; // Return zero
                }
            } else {
                // User details fetching failed

                return null; // Return null
            }
        }

        // Hash new password
        $hash = $this->hashPassword($newPassword);

        // Create updateDetails array and add hash
        $updateDetails = array(FIELD_HASH => $hash);

        // Update hash in database
        if ($this->updateUserProfile($userId, $updateDetails) !== false) {
            // Password updated successfully

            return true; // Return true

        } else {
            // Password update unsuccessful

            return false; // Return false
        }
    }


    /**
    * Function To Encrypt Password
    *
    * @param password   - users password
    *
    * @return string    - Hashed Password
    */
    private function hashPassword($password)
    {

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
    * @param password   - Users password
    * @param hash       - Users password hask
    *
    * @return boolean   - true/false (password verified / not verified)
    */
    public function verifyPassword($password, $hash)
    {

        return password_verify($password, $hash); // Verify password and return boolean
    }
}

// EOF : UserAccountFunctions.php
