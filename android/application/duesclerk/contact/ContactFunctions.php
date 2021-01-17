<?php

/**
* User account functions class
* This class contains all the functions required to manage and process contact
*
* @author David Kariuki (dk)
* @copyright (c) 2020 - 2021 David Kariuki (dk) All Rights Reserved.
*/

// Namespace declaration
namespace duesclerk\contact;

// Enable error reporting
error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_NOTICE|E_STRICT);

// Call project classes
use duesclerk\database\DatabaseConnection;
use duesclerk\constants\Constants;
use duesclerk\src\SharedFunctions;
use duesclerk\src\DateTimeFunctions;


// Class declaration
class ContactFunctions
{

    // Connection status value variable
    private $connectToDB;       // Create DatabaseConnection class object
    private $constants;         // Create Constants class object
    private $sharedFunctions;   // Create SharedFunctions class object
    private $dateTimeFunctions;  // Create DateTimeFunctions class object


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

        $this->constants        = new Constants();       // Initialize constants object
        $this->sharedFunctions  = new SharedFunctions(); // Initialize SharedFunctions class object

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
    * Function to check if contact email address is in contact table.
    *
    * @param contactEmailAddress    - Contact email address
    *
    * @return boolean               - true/false - (if/not found)
    */
    public function isEmailAddressInContactsTable($contactEmailAddress, $contactType)
    {

        // Check for email address in contact table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_EMAIL_ADDRESS)}
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACTS)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_EMAIL_ADDRESS)} = ?
            AND {$this->constants->valueOfConst(KEY_CONTACTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_TYPE)} = ?"
        );
        $stmt->bind_param("ss", $contactEmailAddress, $contactType); // Bind parameters
        $stmt->execute(); // Execute statement
        $stmt->store_result(); // Store result

        // Check if records found
        if ($stmt->num_rows > 0) {
            // Contact email address found

            $stmt->close(); // Close statement

            // Return true
            return true; // Return false

        } else {
            // Contact email address not found

            $stmt->close(); // Close statement

            // Return false
            return false; // Return false
        }
    }

    /**
    * Function to check if contact phone number is in contact table.
    *
    * @param contactPhoneNumber - Contact phone number
    *
    * @return boolean           - true/false - (if/not found)
    */
    public function isPhoneNumberInContactsTable($contactPhoneNumber, $contactType)
    {

        // Check for phone number in contact table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_PHONE_NUMBER)}
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACTS)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_PHONE_NUMBER)} = ?
            AND {$this->constants->valueOfConst(KEY_CONTACTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_TYPE)} = ?"
        );
        $stmt->bind_param("ss", $contactPhoneNumber, $contactType); // Bind parameters
        $stmt->execute(); // Execute statement
        $stmt->store_result(); // Store result

        // Check if records found
        if ($stmt->num_rows > 0) {
            // Contact phone number found

            $stmt->close(); // Close statement

            // Return true
            return true; // Return false

        } else {
            // Contact phone number not found

            $stmt->close(); // Close statement

            // Return false
            return false; // Return false
        }
    }

    /**
    * Function to get contact by contact id
    *
    * @param contactId  - contact id
    *
    * @return array     - Associative array (contact details)
    * @return boolean   - false - (fetch failure)
    */
    public function getContactDetailsByContactId($contactId)
    {

        // Check for contact id in contact table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACT)}.*
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACT)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_ID)}
            = ?"
        );
        $stmt->bind_param("s", $contactId); // Bind parameters

        // Check for query execution
        if ($stmt->execute()) {
            // Query executed

            $contact = $stmt->get_result()->fetch_assoc(); // Get result array
            $stmt->close(); // Close statement

            // Initialize contacts debts total amount to empty
            $contact[FIELD_DEBTS_TOTAL_AMOUNT] = "";

            return $contact; // Return contact details array

        } else {
            // Contact not found

            $stmt->close(); // Close statement

            return false; // Return false
        }
    }

    /**
    * Function to get contact by contact phone number
    *
    * @param contactPhoneNumber - contact phone number
    *
    * @return array             - Associative array (contact details)
    * @return boolean           - false - (fetch failure)
    */
    public function getContactDetailsByContactPhoneNumber($contactPhoneNumber)
    {

        // Check for phone number in contact table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACT)}.*
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACT)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_PHONE_NUMBER)}
            = ?"
        );
        $stmt->bind_param("s", $contactPhoneNumber); // Bind parameters

        // Check for query execution
        if ($stmt->execute()) {
            // Query executed

            $contact = $stmt->get_result()->fetch_assoc(); // Get result array
            $stmt->close(); // Close statement


            return $contact; // Return contact details array

        } else {
            // Contact not found

            $stmt->close(); // Close statement

            return false; // Return false
        }
    }

    /**
    * Function to get contact by contact email address
    *
    * @param contactEmailAddress    - contact email address
    *
    * @return array                 - Associative array (contact details)
    * @return boolean               - false - (fetch failure)
    */
    public function getContactDetailsByContactEmailAddress($contactEmailAddress)
    {

        // Check for email address in contact table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACT)}.*
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACT)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_EMAIL_ADDRESS)}
            = ?"
        );
        $stmt->bind_param("s", $contactEmailAddress); // Bind parameters

        // Check for query execution
        if ($stmt->execute()) {
            // Query executed

            $contact = $stmt->get_result()->fetch_assoc(); // Get result array
            $stmt->close(); // Close statement


            return $contact; // Return contact details array

        } else {
            // Contact not found

            $stmt->close(); // Close statement

            return false; // Return false
        }
    }

    /**
    * Function to add user contact to contact table
    *
    * @param userId         - User id for user adding the contact
    * @param contactDetails - Associative array of contacts fields key value pair to be inserted
    *
    * @return array         - Associatve array - (contact details)
    * @return boolean       - false - (on contact adding failure)
    * @return null          - When reqyuired fields are missing
    */
    public function addUsersContact($userId, $contactDetails)
    {

        if (
            array_key_exists(FIELD_CONTACT_FULL_NAME, $contactDetails)
            && array_key_exists(FIELD_CONTACT_PHONE_NUMBER, $contactDetails)
            && array_key_exists(FIELD_CONTACT_TYPE, $contactDetails)
        ) {
            // Required fields set

            // Get contact details from associative array

            $contactFullName       = $contactDetails[FIELD_CONTACT_FULL_NAME];
            $contactPhoneNumber    = $contactDetails[FIELD_CONTACT_PHONE_NUMBER];
            $contactType           = $contactDetails[FIELD_CONTACT_TYPE];
            $contactEmailAddress   = "NULL";
            $contactAddress         = "NULL";

            // Check for contact email address
            if (array_key_exists(FIELD_CONTACT_EMAIL_ADDRESS, $contactDetails)) {
                // Contact email address exists

                $contactEmailAddress = $contactDetails[FIELD_CONTACT_EMAIL_ADDRESS];
            }

            // Check for contact address
            if (array_key_exists(FIELD_CONTACT_ADDRESS, $contactDetails)) {
                // Contact address exists

                $contactAddress = $contactDetails[FIELD_CONTACT_ADDRESS];
            }

            // Generate contact id
            $contactId = $this->sharedFunctions->generateUniqueId(
                "contact",
                TABLE_CONTACTS,
                FIELD_CONTACT_ID,
                LENGTH_TABLE_IDS_LONG
            );

            // Prepare statement
            $stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->constants->valueOfConst(TABLE_CONTACTS)}
                (
                    {$this->constants->valueOfConst(FIELD_CONTACT_ID)},
                    {$this->constants->valueOfConst(FIELD_CONTACT_FULL_NAME)},
                    {$this->constants->valueOfConst(FIELD_CONTACT_PHONE_NUMBER)},
                    {$this->constants->valueOfConst(FIELD_CONTACT_EMAIL_ADDRESS)},
                    {$this->constants->valueOfConst(FIELD_CONTACT_ADDRESS)},
                    {$this->constants->valueOfConst(FIELD_CONTACT_TYPE)},
                    {$this->constants->valueOfConst(FIELD_USER_ID)}
                )
                VALUES ( ?, ?, ?, ?, ?, ?, ?)"
            );

            // Bind parameters
            $stmt->bind_param(
                "sssssss",
                $contactId, $contactFullName, $contactPhoneNumber, $contactEmailAddress, $contactAddress, $contactType, $userId
            );
            $add = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

            // Check for query execution
            if ($add) {
                // Query execution successful

                // Return contact details
                return $this->getContactDetailsByContactPhoneNumber($contactPhoneNumber);

            } else {
                // Query execution failed

                return false; // Return false
            }
        } else {
            // Missing required fields

            return null; // Return null
        }
    }

    /**
    * Function to fetch contact by UserId
    *
    * @param UserId     - UserId to get users contact list
    *
    * @return array     - Associaive array - (contacts)
    * @return boolean   - false - (On contacts fetch failed)
    * @return null      - on fetched array empty
    */
    public function getUserContactsByUserId($userId)
    {

        // Prepare SELECT statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACTS)}.*
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACTS)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACTS)}
            .{$this->constants->valueOfConst(FIELD_USER_ID)} = ?
            ORDER BY {$this->constants->valueOfConst(KEY_CONTACTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_FULL_NAME)} ASC"
        );
        $stmt->bind_param("s", $userId); // Bind parameter
        $stmt->execute(); // Execute statement
        $result = $stmt->get_result(); // Get result
        $stmt->close(); // Close statement

        // Check for query execution
        if ($result) {
            // Query execution successful

            // Create array to store all contact rows
            $contacts = array();

            // Loop through result to get all contact rows
            while ($row = $result->fetch_assoc()) {

                // Initialize contacts debts total amount to empty
                $row[FIELD_DEBTS_TOTAL_AMOUNT] = "";

                $contacts[] = $row; // Add row to array
            }

            // Check array size
            if (sizeof($contacts) > 0) {

                // Return contacts with total debts amount
                return $this->getAllContactsDebtsTotalFromArray($contacts);

            } else {

                return null; // Return null
            }
        } else {
            // Query execution failed

            return false; // Return false
        }
    }

    /**
    * Function to add user contacts debt to debts table
    *
    * @param debtDetails    - Associative array of debts fields key value pair to be inserted
    *
    * @return array         - Associatve array - (debt details)
    * @return boolean       - false - (on contact adding failure)
    * @return null          - When required fields are missing
    */
    public function addContactsDebt($debtDetails)
    {
        // Check for UserId and ContactId
        if (array_key_exists(FIELD_USER_ID, $debtDetails)
        && array_key_exists(FIELD_CONTACT_ID, $debtDetails)
        && array_key_exists(FIELD_DEBT_AMOUNT, $debtDetails)
        && array_key_exists(FIELD_DEBT_DATE_ISSUED, $debtDetails)
        && array_key_exists(FIELD_DEBT_DATE_DUE, $debtDetails)
        && array_key_exists(FIELD_CONTACT_TYPE, $debtDetails)) {
            // Required fields set

            // Generate debt id
            $debtId = $this->sharedFunctions->generateUniqueId(
                "debt",
                TABLE_DEBTS,
                FIELD_DEBT_ID,
                LENGTH_TABLE_IDS_LONG
            );

            // Get debt details from associative array
            $debtAmount = $debtDetails[FIELD_DEBT_AMOUNT]; // Get debt amount

            // Get debt date issued
            $debtDateIssued = $this->dateTimeFunctions->convertDateTimeFromFormat(
                $debtDetails[FIELD_DEBT_DATE_ISSUED],
                FORMAT_DATE_FULL,
                FORMAT_DATE_SHORT
            );

            // Get debt date due
            $debtDateDue        = $this->dateTimeFunctions->convertDateTimeFromFormat(
                $debtDetails[FIELD_DEBT_DATE_DUE],
                FORMAT_DATE_FULL,
                FORMAT_DATE_SHORT
            );

            $debtDescription    = ""; // Debt description

            // Check for debt description
            if (array_key_exists(FIELD_DEBT_DESCRIPTION, $debtDetails)) {

                // Get debt description
                $debtDescription = $debtDetails[FIELD_DEBT_DESCRIPTION];
            }

            // Get contact details and UserId from associative array
            $contactId      = $debtDetails[FIELD_CONTACT_ID];   // Get contact id
            $contactType    = $debtDetails[FIELD_CONTACT_TYPE]; // Get contact type
            $userId         = $debtDetails[FIELD_USER_ID];      // Get UserId

            // Prepare INSERT statement
            $stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->constants->valueOfConst(TABLE_DEBTS)}
                (
                    {$this->constants->valueOfConst(FIELD_DEBT_ID)},
                    {$this->constants->valueOfConst(FIELD_DEBT_AMOUNT)},
                    {$this->constants->valueOfConst(FIELD_DEBT_DATE_ISSUED)},
                    {$this->constants->valueOfConst(FIELD_DEBT_DATE_DUE)},
                    {$this->constants->valueOfConst(FIELD_DEBT_DESCRIPTION)},
                    {$this->constants->valueOfConst(FIELD_CONTACT_ID)},
                    {$this->constants->valueOfConst(FIELD_CONTACT_TYPE)},
                    {$this->constants->valueOfConst(FIELD_USER_ID)}
                )
                VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            // Bind parameters
            $stmt->bind_param(
                "ssssssss",
                $debtId, $debtAmount, $debtDateIssued, $debtDateDue, $debtDescription,
                $contactId, $contactType, $userId
            );

            $add = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

            if ($add) {
                // Query execution successful

                return $this->getDebtDetailsByDebtId($debtId); // Return debt details

            } else {
                // Debt insertion failed

                return null;
            }
        } else {
            // Missing required fields

            return 0; // Return zero
        }
    }

    /**
    * Function to get a debts details
    *
    * @param DebtId - Debts id
    *
    * @return array - Associative array - (debt details)
    */
    private function getDebtDetailsByDebtId($debtId)
    {

        // Check for debt id in debts table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_DEBT)}.*
            FROM {$this->constants->valueOfConst(TABLE_DEBTS)}
            AS {$this->constants->valueOfConst(KEY_DEBT)}
            WHERE {$this->constants->valueOfConst(KEY_DEBT)}
            .{$this->constants->valueOfConst(FIELD_DEBT_ID)}
            = ?"
        );
        $stmt->bind_param("s", $debtId); // Bind parameters

        // Check for query execution
        if ($stmt->execute()) {
            // Query executed

            $debt = $stmt->get_result()->fetch_assoc(); // Get result array
            $stmt->close(); // Close statement

            return $debt; // Return debt details array

        } else {
            // Debt not found

            $stmt->close(); // Close statement

            return false; // Return false
        }
    }

    /**
    * Function to get an array of a contacts debts
    *
    * @param ContactId      - Contact id
    * @param ContactType    - Contact type
    * @param UserId         - UserId to get user contact debt list
    *
    * @return array         - Associaive array - (debts)
    * @return boolean       - false - (On debts fetch failed)
    * @return null          - on fetched array empty
    */
    public function getContactsDebts($contactId, $contactType, $userId)
    {

        // Prepare SELECT statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_DEBTS)}.*
            FROM {$this->constants->valueOfConst(TABLE_DEBTS)}
            AS {$this->constants->valueOfConst(KEY_DEBTS)}
            WHERE {$this->constants->valueOfConst(KEY_DEBTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_ID)} = ?
            AND {$this->constants->valueOfConst(KEY_DEBTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_TYPE)} = ?
            AND {$this->constants->valueOfConst(KEY_DEBTS)}
            .{$this->constants->valueOfConst(FIELD_USER_ID)} = ?
            ORDER BY {$this->constants->valueOfConst(KEY_DEBTS)}
            .{$this->constants->valueOfConst(FIELD_DEBT_ORDER)} DESC"
        );

        $stmt->bind_param("sss", $contactId, $contactType, $userId); // Bind parameter
        $stmt->execute(); // Execute statement
        $result = $stmt->get_result(); // Get result
        $stmt->close(); // Close statement

        // Check for query execution
        if ($result) {
            // Query execution successful

            // Create array to store all contact rows
            $debts = array();

            // Loop through result to get all contact rows
            while ($row = $result->fetch_assoc()) {

                // Get debt date issued and date due
                $debtDateIssued = $row[FIELD_DEBT_DATE_ISSUED];
                $debtDateDue    = $row[FIELD_DEBT_DATE_DUE];

                // Convert debt date issued time format to users local time format
                $readableDebtDateIssued = $this->dateTimeFunctions->convertDateFormat(
                    $debtDateIssued,
                    FORMAT_DATE_FULL
                );

                // Convert debt date due time format to users local time format
                $readableDebtDateDue = $this->dateTimeFunctions->convertDateFormat(
                    $debtDateDue,
                    FORMAT_DATE_FULL
                );

                // Update rows debt dates to readable time format
                $row[FIELD_DEBT_DATE_ISSUED]    = $readableDebtDateIssued; // Update date issued
                $row[FIELD_DEBT_DATE_DUE]       = $readableDebtDateDue; // Update date due

                $debts[] = $row; // Add row to array
            }

            // Check array size
            if (sizeof($debts) > 0) {

                // Get total debts amount
                $totalDebtsAmount = $this->getDebtsTotalAmountFromArray($debts);

                // Debts data associative array to hold debts array and debts total amount
                $debtsData = array(
                    KEY_DEBTS => array(),           // Set to array
                    FIELD_DEBTS_TOTAL_AMOUNT => 0   // Initialize to 0
                );

                $debtsData[KEY_DEBTS] = $debts; // Add debts to debts data array

                // Add total debts amount to debts data array
                $debtsData[FIELD_DEBTS_TOTAL_AMOUNT] = $totalDebtsAmount;

                return $debtsData; // Return contacts

            } else {

                return null; // Return null
            }
        } else {
            // Query execution failed

            return false; // Return false
        }
    }

    /**
    * Function to get debts total amount for a all contacts
    *
    * @param contacts   - All users contacts
    *
    * @return array     - Associative array - (Users contacts with total debts amount)
    * @return int       - 0 - (If array count is 0)
    * @return null      - on fetched array empty
    */
    private function getAllContactsDebtsTotalFromArray($contacts)
    {
        // Check if passed parameter is array
        if (is_array($contacts)) {
            // Passed parameter is array

            if (sizeof($contacts) > 0) {

                // Loop through all contacts debts
                foreach($contacts as $key => $contact) {

                    $contactId      = "";
                    $contactType    = "";
                    $userId         = "";

                    // Loop through single debt to get debt details
                    foreach ($contact as $key => $contactDetails) {

                        $contactId      = $contact[FIELD_CONTACT_ID];
                        $contactType    = $contact[FIELD_CONTACT_TYPE];
                        $userId         = $contact[FIELD_USER_ID];
                    }

                    $contactsDebts = $this->getContactsDebts($contactId, $contactType, $userId);

                    $debtsTotalAmount = $contactsDebts[FIELD_DEBTS_TOTAL_AMOUNT];

                    if ($debtsTotalAmount != null) {

                        $contact[FIELD_DEBTS_TOTAL_AMOUNT] = $debtsTotalAmount;
                    } else {

                        $contact[FIELD_DEBTS_TOTAL_AMOUNT] = "";
                    }

                    $contactsWithTotalDebts[] = $contact;
                }

                return $contactsWithTotalDebts;
            } else {

                return 0; // If array count is 0 (no record found)
            }
        } else {

            return null; // If passed parameter is not an array
        }
    }

    /**
    * Function to get debts total amount for a single contact
    *
    * @param debts          - All contacts debts
    *
    * @return int           - Total debts amount
    * @return int           - 0 - (If array count is 0)
    * @return null          - on fetched array empty
    */
    private function getDebtsTotalAmountFromArray($debts)
    {
        // Check if passed parameter is array
        if (is_array($debts)){

            // Check array length
            if (sizeof($debts) > 0) {

                $totalDebtsAmount = 0; // Variable to hold total debts amount

                // Loop through all contacts debts
                foreach($debts as $key => $debt) {

                    // Loop through single debt to get debt details
                    foreach ($debt as $key => $debtDetails) {

                        // Check if current key in loop is amounts field
                        if ($key == FIELD_DEBT_AMOUNT) {

                            // Increment total value with current debt amount value
                            $totalDebtsAmount += $debt[$key];
                        }
                    }
                }

                // Round off total debts amount to 2 decimal places
                return number_format((float)$totalDebtsAmount, 2, '.', '');

            } else {

                return 0; // If array count is 0 (no record found)
            }
        } else {

            return null; // If passed parameter is not an array
        }
    }
}

// EOF: ContactFunctions.php
