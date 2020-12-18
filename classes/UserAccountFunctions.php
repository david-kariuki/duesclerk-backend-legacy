<?php

/**
* User account functions class
* This class contains all the functions required to process a users account
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/


// Enable error reporting
error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_NOTICE|E_STRICT);

class UserAccountFunctions
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
    * Check if email address is in users table.
    *
    * @param emailAddress   - Users email address
    *
    * @return boolean       - true/false - (if/not found)
    */
    public function isEmailAddressInUsersTable($emailAddress)
    {

        // Check for email address in users table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->valueOfConst(KEY_USER)}
            .{$this->keys->valueOfConst(FIELD_EMAIL_ADDRESS)}
            FROM {$this->keys->valueOfConst(TABLE_USERS)}
            AS {$this->keys->valueOfConst(KEY_USER)}
            WHERE {$this->keys->valueOfConst(KEY_USER)}
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
    * Function to get user by email address and password
    *
    * @param emailAddress   - users email address
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
            "SELECT {$this->keys->valueOfConst(KEY_USER)}.*,
            {$this->keys->valueOfConst(KEY_COUNTRY)}.*
            FROM {$this->keys->valueOfConst(TABLE_USERS)}
            AS {$this->keys->valueOfConst(KEY_USER)}
            LEFT OUTER JOIN {$this->keys->valueOfConst(TABLE_COUNTRIES)}
            AS {$this->keys->valueOfConst(KEY_COUNTRY)}
            ON {$this->keys->valueOfConst(KEY_COUNTRY)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            = {$this->keys->valueOfConst(KEY_USER)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            AND {$this->keys->valueOfConst(KEY_COUNTRY)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_CODE)}
            = {$this->keys->valueOfConst(KEY_USER)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_CODE)}
            WHERE {$this->keys->valueOfConst(KEY_USER)}
            .{$this->keys->valueOfConst(FIELD_EMAIL_ADDRESS)}
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
                $user[FIELD_SIGN_UP_DATE_TIME] = $this->dateTimeFunctions->getLocalTime($signUpDateTime, $countryAlpha2);

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
    * @param userId     - users id
    *
    * @return array     - Associative array (user details)
    * @return boolean   - false - (fetch failure)
    */
    public function getUserByUserId($userId)
    {

        // Check for email in Table Users
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->valueOfConst(KEY_USER)}.*,
            {$this->keys->valueOfConst(KEY_COUNTRY)}.*
            FROM {$this->keys->valueOfConst(TABLE_USERS)}
            AS {$this->keys->valueOfConst(KEY_USER)}
            LEFT OUTER JOIN {$this->keys->valueOfConst(TABLE_COUNTRIES)}
            AS {$this->keys->valueOfConst(KEY_COUNTRY)}
            ON {$this->keys->valueOfConst(KEY_COUNTRY)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            = {$this->keys->valueOfConst(KEY_USER)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            AND {$this->keys->valueOfConst(KEY_COUNTRY)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_CODE)}
            = {$this->keys->valueOfConst(KEY_USER)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_CODE)}
            WHERE {$this->keys->valueOfConst(KEY_USER)}
            .{$this->keys->valueOfConst(FIELD_USER_ID)}
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
            $user[FIELD_SIGN_UP_DATE_TIME] = $this->dateTimeFunctions->getLocalTime($signUpDateTime, $countryAlpha2);

            return $user; // Return user details array

        } else {
            // User not found

            $stmt->close(); // Close statement

            return false; // Return false
        }
    }


    /**
    * Function to get user by email address
    *
    * @param emailAddress   - users email address
    *
    * @return array         - Associative array (user details)
    * @return boolean       - false - (fetch failure)
    */
    public function getUserByEmailAddress($emailAddress)
    {

        // Check for email in Table Users
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->keys->valueOfConst(KEY_USER)}.*,
            {$this->keys->valueOfConst(KEY_COUNTRY)}.*
            FROM {$this->keys->valueOfConst(TABLE_USERS)}
            AS {$this->keys->valueOfConst(KEY_USER)}
            LEFT OUTER JOIN {$this->keys->valueOfConst(TABLE_COUNTRIES)}
            AS {$this->keys->valueOfConst(KEY_COUNTRY)}
            ON {$this->keys->valueOfConst(KEY_COUNTRY)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            = {$this->keys->valueOfConst(KEY_USER)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_ALPHA2)}
            AND {$this->keys->valueOfConst(KEY_COUNTRY)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_CODE)}
            = {$this->keys->valueOfConst(KEY_USER)}
            .{$this->keys->valueOfConst(FIELD_COUNTRY_CODE)}
            WHERE {$this->keys->valueOfConst(KEY_USER)}
            .{$this->keys->valueOfConst(FIELD_EMAIL_ADDRESS)}
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
            $user[FIELD_SIGN_UP_DATE_TIME] = $this->dateTimeFunctions->getLocalTime($signUpDateTime, $countryAlpha2);

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

        // Get emailAddress, countryCode, countryAlpha2, password,
        // accountType from SignUpDetails array
        $emailAddress     = $signUpDetails[FIELD_EMAIL_ADDRESS];
        $countryCode      = $signUpDetails[FIELD_COUNTRY_CODE];
        $countryAlpha2    = $signUpDetails[FIELD_COUNTRY_ALPHA2];
        $password         = $signUpDetails[FIELD_PASSWORD];
        $accountType      = $signUpDetails[FIELD_ACCOUNT_TYPE];

        // Create userId
        $userId = $this->sharedFunctions->generateUniqueId(
            strtolower(KEY_USER),
            TABLE_USERS,
            FIELD_USER_ID
        );

        // Hash password
        $hash = $this->hashPassword($password);

        // Get account creation date
        $signupDateTime = $this->dateTimeFunctions->getDefaultTimeZoneTextualDateTime();

        $result;
        // Insert into Users
        if ($accountType == KEY_ACCOUNT_TYPE_PERSONAL) {
            // Personal account

            $stmt; // Statement variable

            // Get first name and last name
            $firstName   = $signUpDetails[FIELD_FIRST_NAME];
            $lastName    = $signUpDetails[FIELD_LAST_NAME];

            // Prepare statement
            $stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->keys->valueOfConst(TABLE_USERS)}(`UserId`, `FirstName`, `LastName`, `EmailAddress`, `CountryCode`, `CountryAlpha2`, `Hash`, `AccountType`, `SignUpDateTime`)
                VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            // Bind parameters
            $stmt->bind_param("sssssssss", $userId, $firstName, $lastName, $emailAddress, $countryCode, $countryAlpha2, $hash, $accountType, $signupDateTime);

            $result = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

        } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
            // Business account

            // Get business name
            $businessName    = $signUpDetails[FIELD_BUSINESS_NAME];

            // Prepare statement
            $stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->keys->valueOfConst(TABLE_USERS)}(`UserId`, `EmailAddress`, `CountryCode`, `CountryAlpha2`, `Hash`, `BusinessName`, `AccountType`, `SignUpDateTime`)
                VALUES( ?, ?, ?, ?, ?, ?, ?, ? )"
            );

            // Bind parameters
            $stmt->bind_param(
                "ssssssss",
                $userId, $emailAddress, $countryCode, $countryAlpha2, $hash, $businessName, $accountType, $signupDateTime
            );

            $result = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement
        }

        // Check for query execution
        if ($result) {
            // Signup successful

            // Log signup event
            if ($this->sharedFunctions->createStoreUserLogs(
                LOG_TYPE_SIGN_UP,
                $signupDateTime,
                $userId
                )
            ) {
                // Loging successful

                // Get stored values
                // Prepare statement
                $stmt = $this->connectToDB->prepare(
                    "SELECT * FROM {$this->keys->valueOfConst(TABLE_USERS)}
                    AS {$this->keys->valueOfConst(KEY_USER)}
                    WHERE {$this->keys->valueOfConst(KEY_USER)}
                    .{$this->keys->valueOfConst(FIELD_USER_ID)} = ?
                    AND {$this->keys->valueOfConst(KEY_USER)}
                    .{$this->keys->valueOfConst(FIELD_EMAIL_ADDRESS)} = ?"
                );

                $stmt->bind_param("ss", $userId, $emailAddress); // Bind parameters
                $stmt->execute(); // Execute statement
                $user = $stmt->get_result()->fetch_assoc(); // Get result array
                $stmt->close(); // Close statement

                // Return user details
                return $user;

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
    * @param userId         - users Id
    * @param accountType    - users account type
    * @param updateDetails  - array with associative array of fields key value pair to be updated
    *
    * @return boolean       - true/false (on revokation success / revokation failure)
    * @return boolean       - true/false (on email sent / email not sent)
    * @return null          - on logging failed
    * @return int           - 0 - (on update failure)
    */
    public function updateUserProfile($userId, $accountType, $updateDetails)
    {
        // Get details from array

        // Get emailAddress, countryCode, countryAlpha2, password,
        // accountType from update details array

        $updateParams = ""; // Update params

        $firstName = "";
        $lastName = "";
        $businessName = "";
        $bindParamValues = array();

        // Check for account type
        if (!empty($accountType)) {
            // Insert into users

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


            } else if ($accountType == KEY_ACCOUNT_TYPE_BUSINESS) {
                // Business account

                // Check for business name
                if (array_key_exists(FIELD_BUSINESS_NAME, $updateDetails)) {

                    // Add business name to update params
                    $updateParams .= ", {$this->keys->valueOfConst(FIELD_BUSINESS_NAME)} = ?";
                }
            }
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
        $updateStatement = "UPDATE {$this->keys->valueOfConst(TABLE_USERS)}
        SET {$updateParams} WHERE {$this->keys->valueOfConst(TABLE_USERS)}
        .{$this->keys->valueOfConst(FIELD_USER_ID)} = '$userId'";

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
                if ($this->sharedFunctions->createStoreUserLogs(
                    LOG_TYPE_UPDATE_PASSWORD,
                    $updateDateTime,
                    $userId
                    )
                ) {
                    // Log stored

                    // Get user details
                    $user = $this->getUserByUserId($userId);

                    // Get firsnName and email address
                    $firstName      = $user[FIELD_FIRST_NAME];
                    $emailAddress   = $user[FIELD_EMAIL_ADDRESS];

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
                if ($this->sharedFunctions->createStoreUserLogs(
                    LOG_TYPE_UPDATE_PROFILE,
                    $updateDateTime,
                    $userId
                    )
                ) {
                    // Log stored

                    // Check if email address was updated so as to revoke email address
                    // verification after updating to a new one for re-verification
                    if (array_key_exists(FIELD_EMAIL_ADDRESS, $updateDetails)) {
                        // Email address was updated

                        // Revoke email address verification and check for failure
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
    * @param userId             - Users id
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
        if ($this->updateUserProfile($userId, "", $updateDetails) !== false) {
            // Password updated successfully

            return true; // Return true

        } else {
            // Password update unsuccessful

            return false; // Return false
        }
    }


    /**
    * Function to switch account type
    *
    * @param userId         - Users id
    * @param newAccountType - New account type to switch to
    *
    * @return boolean       - false - (password updated/not updated)
    * @return int           - 0 - (on update failed)
    * @return null          - on logging failed
    */
    public function switchAccountType($switchAccountDetails)
    {

        $newAccountType     = ""; // Account type to switch to
        $userId             = ""; // User id
        $firstName          = ""; // Users first name
        $lastName           = ""; // Users last name
        $businessName       = ""; // Users business name

        // Check for new account type
        if (array_key_exists(FIELD_USER_ID, $switchAccountDetails)
        && array_key_exists(FIELD_NEW_ACCOUNT_TYPE, $switchAccountDetails)) {

            $userId = $switchAccountDetails[FIELD_USER_ID]; // Get userId from associative array

            // Get new account type from associative array
            $newAccountType = $switchAccountDetails[FIELD_NEW_ACCOUNT_TYPE];

            // Check personal account fields
            if (array_key_exists(FIELD_FIRST_NAME, $switchAccountDetails)
            && array_key_exists(FIELD_LAST_NAME, $switchAccountDetails)) {
                // Converting to personal account

                // Get first and last name from associative array
                $firstName  = $switchAccountDetails[FIELD_FIRST_NAME];
                $lastName   = $switchAccountDetails[FIELD_LAST_NAME];

            } else if (array_key_exists(FIELD_BUSINESS_NAME, $switchAccountDetails)) {
                // Converting to business account

                // Get business name from associative array
                $businessName = $switchAccountDetails[FIELD_BUSINESS_NAME];
            }

            // Prepare UPDATE statement
            $stmt = $this->connectToDB->prepare(
                "UPDATE {$this->keys->valueOfConst(TABLE_USERS)}
                SET {$this->keys->valueOfConst(FIELD_FIRST_NAME)} = ?,
                {$this->keys->valueOfConst(FIELD_LAST_NAME)} = ?,
                {$this->keys->valueOfConst(FIELD_BUSINESS_NAME)} = ?,
                {$this->keys->valueOfConst(FIELD_ACCOUNT_TYPE)} = ?
                WHERE {$this->keys->valueOfConst(FIELD_USER_ID)} = ?"
            );

            // Bind parameters
            $stmt->bind_param(
                "sssss",
                $firstName, $lastName, $businessName, $newAccountType, $userId
            );
            $update = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

            // Check for query execution
            if ($update) {
                // Update successful

                // Prepare SELECT statement
                $stmt = $this->connectToDB->prepare(
                    "SELECT {$this->keys->valueOfConst(KEY_USER)}.*
                    FROM {$this->keys->valueOfConst(TABLE_USERS)}
                    AS {$this->keys->valueOfConst(KEY_USER)}
                    WHERE {$this->keys->valueOfConst(KEY_USER)}
                    .{$this->keys->valueOfConst(FIELD_USER_ID)} = ?
                    AND {$this->keys->valueOfConst(KEY_USER)}
                    .{$this->keys->valueOfConst(FIELD_ACCOUNT_TYPE)} = ?"
                );
                $stmt->bind_param("ss", $userId, $newAccountType); // Bind parameters

                // Check for client details
                if ($stmt->execute()) {
                    // Client details fetched

                    $user = $stmt->get_result()->fetch_assoc(); // Get result array
                    $stmt->close(); // Close statement

                    // Get date and time
                    $switchTime = $this->dateTimeFunctions->getDefaultTimeZoneTextualDateTime();

                    // Log signup event
                    if ($this->sharedFunctions->createStoreUserLogs(
                        LOG_TYPE_SWITCH_ACCOUNT_TYPE,
                        $switchTime,
                        $userId
                    )) {
                        // Logging successful

                        return $user; // Return user associative array

                    } else {
                        // Logging failed

                        return null; // Return null on logging failed
                    }
                } else {
                    // Client details not fetched

                    $stmt->close(); // Close statement

                    return false; // Feturn false on fetch failed
                }
            } else {
                // Update failed

                return 0; // Return 0 on update failed
            }
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
