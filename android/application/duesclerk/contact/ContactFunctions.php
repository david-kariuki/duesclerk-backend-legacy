<?php

/**
* Contact functions class
* This class contains all the functions required to manage and process contacts
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
use duesclerk\debt\DebtFunctions;


// Class declaration
class ContactFunctions
{

    // Connection status value variable
    private $connectToDB;       // Create DatabaseConnection class object
    private $constants;         // Create Constants class object
    private $sharedFunctions;   // Create SharedFunctions class object
    private $debtFunctions;     // Create DebtFunctions class object


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
        $this->constants        = new Constants(); // Initialize constants object

        // Initialize SharedFunctions class object
        $this->sharedFunctions  = new SharedFunctions();

        // Initialize DebtFunctions class object
        $this->debtFunctions    = new DebtFunctions();
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
                return $this->debtFunctions->getAllContactsDebtsTotalFromArray($contacts);

            } else {

                return null; // Return null
            }
        } else {
            // Query execution failed

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

            $contactEmailAddress   = "NULL";
            $contactAddress        = "NULL";

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
                $contactId,
                $contactDetails[FIELD_CONTACT_FULL_NAME], $contactDetails[FIELD_CONTACT_PHONE_NUMBER], $contactEmailAddress, $contactAddress, $contactDetails[FIELD_CONTACT_TYPE],
                $userId
            );
            $add = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

            // Check for query execution
            if ($add) {
                // Query execution successful

                // Return contact details
                return $this->getContactDetailsByContactPhoneNumber(
                    $contactDetails[FIELD_CONTACT_PHONE_NUMBER]
                );

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
    * Function to update contact details
    *
    * @param contactDetails - Contact details associative array
    *
    * @return boolean - (Update successfull / failed)
    */
    public function updateContactDetails($contactDetails)
    {
        // Check required fields
        if (array_key_exists(FIELD_USER_ID, $contactDetails)
        && array_key_exists(FIELD_CONTACT_ID, $contactDetails)) {
            // User id and contact id exists

            // Prepare UPDATE statement
            $stmt = $this->connectToDB->prepare(
                "UPDATE {$this->constants->valueOfConst(TABLE_CONTACTS)}
                SET {$this->constants->valueOfConst(FIELD_CONTACT_FULL_NAME)} = ?,
                {$this->constants->valueOfConst(FIELD_CONTACT_PHONE_NUMBER)} = ?,
                {$this->constants->valueOfConst(FIELD_CONTACT_EMAIL_ADDRESS)} = ?,
                {$this->constants->valueOfConst(FIELD_CONTACT_ADDRESS)} = ?
                WHERE {$this->constants->valueOfConst(TABLE_CONTACTS)}
                .{$this->constants->valueOfConst(FIELD_CONTACT_ID)} = ?
                AND {$this->constants->valueOfConst(TABLE_CONTACTS)}
                .{$this->constants->valueOfConst(FIELD_USER_ID)} = ?"
            );

            // Bind parameters
            $stmt->bind_param(
                "ssssss",
                $contactDetails[FIELD_CONTACT_FULL_NAME],
                $contactDetails[FIELD_CONTACT_PHONE_NUMBER],
                $contactDetails[FIELD_CONTACT_EMAIL_ADDRESS],
                $contactDetails[FIELD_CONTACT_ADDRESS],
                $contactDetails[FIELD_CONTACT_ID],
                $contactDetails[FIELD_USER_ID]
            );

            $updated = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

            return $updated; // Return boolean on update success status
        }
    }

    /**
    * Function to delete user contact
    *
    * @param contactIds - Contact ids array
    *
    * @return null      - If contact not found
    * @return boolean   - (Contact deletion status)
    * @return int       - 0 - When debts deletion failed
    */
    public function deleteUserContacts($contactIds, $userId)
    {

        $contactIds = array($contactIds); // Convert passed parameter values into an array

        // Check if variable is array
        if (is_array($contactIds)) {
            // Variable is array

            // Loop through array to get contact ids
            foreach($contactIds as $contactId) {

                // Get contact by contact id
                $contact = $this->getContactDetailsByContactId($contactId);

                // Check if contact found
                if ($contact !== false) {
                    // Contact exists

                    // Get contacts debts
                    $contactsDebts = array($this->debtFunctions->getContactsDebts(
                        $contactId,
                        $contact[FIELD_CONTACT_TYPE],
                        $userId
                    ));

                    // Check contacts debts size
                    if (sizeof($contactsDebts) > 0) {
                        // Debts exist for contact

                        // Delete debts for contacts
                        if (!$this->debtFunctions->deleteAllDebtsForContact($contactId, $userId)) {
                            // Contacts debts not deleted

                            return 0; // Return 0
                        }
                    }

                    // Prepare DELETE statement to delete contact from contacts table
                    $stmt = $this->connectToDB->prepare(
                        "DELETE FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
                        WHERE {$this->constants->valueOfConst(TABLE_CONTACTS)}
                        .{$this->constants->valueOfConst(FIELD_CONTACT_ID)} = ?
                        AND {$this->constants->valueOfConst(TABLE_CONTACTS)}
                        .{$this->constants->valueOfConst(FIELD_USER_ID)} = ?"
                    );

                    $stmt->bind_param("ss", $contactId, $userId); // Bind parameters
                    $deleted = $stmt->execute(); // Execute statement
                    $stmt->close(); // Close statement

                    return $deleted; // Return deletion status

                } else {

                    return null; // Return null
                }
            }
        }
    }
}

// EOF: ContactFunctions.php
