<?php

/**
* User account functions class
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
use duesclerk\configs\Constants;
use duesclerk\src\SharedFunctions;


// Class declaration
class ContactFunctions
{

    // Connection status value variable
    private $connectToDB;       // Create DatabaseConnection class object
    private $constants;         // Create Constants class object
    private $sharedFunctions;   // Create SharedFunctions class object


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
        $this->sharedFunctions  = new SharedFunctions(); // Initialize SharedFunctions class object
    }


    /**
    * Class destructor
    */
    function __destruct()
    {

    }


    /**
    * Function to check if contact email address is in contacts table.
    *
    * @param contactEmailAddress    - Contact email address
    *
    * @return boolean               - true/false - (if/not found)
    */
    public function isEmailAddressInContactsTable($contactsEmailAddress, $contactsType)
    {

        // Check for email address in contacts table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_EMAIL_ADDRESS)}
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACT)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_EMAIL_ADDRESS)} = ?
            AND {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_TYPE)} = ?"
        );
        $stmt->bind_param("ss", $contactsEmailAddress, $contactsType); // Bind parameters
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
    * Function to check if contact phone number is in contacts table.
    *
    * @param contactPhoneNumber - Contact phone number
    *
    * @return boolean           - true/false - (if/not found)
    */
    public function isPhoneNumberInContactsTable($contactsPhoneNumber, $contactsType)
    {

        // Check for phone number in contacts table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_PHONE_NUMBER)}
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACT)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_PHONE_NUMBER)} = ?
            AND {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_TYPE)} = ?"
        );
        $stmt->bind_param("ss", $contactsPhoneNumber, $contactsType); // Bind parameters
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
    * Function to get contact by contact phone number
    *
    * @param contactPhoneNumber - contact phone number
    *
    * @return array             - Associative array (contact details)
    * @return boolean           - false - (fetch failure)
    */
    public function getContactByPhoneNumber($contactsPhoneNumber)
    {

        // Check for phone number in contacts table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACT)}.*
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACT)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_PHONE_NUMBER)}
            = ?"
        );
        $stmt->bind_param("s", $contactsPhoneNumber); // Bind parameters

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
    public function getContactByEmailAddress($contactsEmailAddress)
    {

        // Check for email address in contacts table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACT)}.*
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACT)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_EMAIL_ADDRESS)}
            = ?"
        );
        $stmt->bind_param("s", $contactsEmailAddress); // Bind parameters

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
    * Function to insert contact to contacts table
    *
    * @param userId         - User id for user adding the contact
    * @param contactDetails - Associative array of contact fields key value pair to be inserted
    *
    * @return array         - Associatve array - (contact details)
    * @return boolean       - false - (on contact adding failure)
    */
    public function addContact($userId, $contactDetails)
    {

        if (
            array_key_exists(FIELD_CONTACTS_FULL_NAME, $contactDetails)
            && array_key_exists(FIELD_CONTACTS_PHONE_NUMBER, $contactDetails)
            && array_key_exists(FIELD_CONTACTS_TYPE, $contactDetails)
        ) {
            // Required fields set

            // Get contact details from associative array

            $contactsFullName        = $contactDetails[FIELD_CONTACTS_FULL_NAME];
            $contactsPhoneNumber     = $contactDetails[FIELD_CONTACTS_PHONE_NUMBER];
            $contactsType            = $contactDetails[FIELD_CONTACTS_TYPE];
            $contactsEmailAddress    = "NULL";
            $contactAddress         = "NULL";

            // Check for contact email address
            if (array_key_exists(FIELD_CONTACTS_EMAIL_ADDRESS, $contactDetails)) {
                // Contact email address exists

                $contactsEmailAddress = $contactDetails[FIELD_CONTACTS_EMAIL_ADDRESS];
            }

            // Check for contact address
            if (array_key_exists(FIELD_CONTACTS_ADDRESS, $contactDetails)) {
                // Contact address exists

                $contactAddress = $contactDetails[FIELD_CONTACTS_ADDRESS];
            }

            // Generate contact id
            $contactId = $this->sharedFunctions->generateUniqueId(
                "contact",
                TABLE_CONTACTS,
                FIELD_CONTACTS_ID,
                LENGTH_TABLE_IDS_LONG
            );

            // Prepare statement
            $stmt = $this->connectToDB->prepare(
                "INSERT INTO {$this->constants->valueOfConst(TABLE_CONTACTS)}
                (
                    {$this->constants->valueOfConst(FIELD_CONTACTS_ID)},
                    {$this->constants->valueOfConst(FIELD_CONTACTS_FULL_NAME)},
                    {$this->constants->valueOfConst(FIELD_CONTACTS_PHONE_NUMBER)},
                    {$this->constants->valueOfConst(FIELD_CONTACTS_EMAIL_ADDRESS)},
                    {$this->constants->valueOfConst(FIELD_CONTACTS_ADDRESS)},
                    {$this->constants->valueOfConst(FIELD_CONTACTS_TYPE)},
                    {$this->constants->valueOfConst(FIELD_USER_ID)}
                )
                VALUES ( ?, ?, ?, ?, ?, ?, ?)"
            );

            // Bind parameters
            $stmt->bind_param(
                "sssssss",
                $contactId, $contactsFullName, $contactsPhoneNumber, $contactsEmailAddress, $contactAddress, $contactsType, $userId
            );
            $add = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

            // Check for query execution
            if ($add) {
                // Querry xecution successful

                // Retrun contact details
                return $this->getContactByPhoneNumber($contactsPhoneNumber);

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
    * Function to fetch contacts by UserId
    *
    * @param UserId - UserId to get users contact list
    *
    * @return array - Associaive array - (contacts)
    * @return boolean - false - (On contacts fetch failed)
    */
    public function getContactsByUserId($userId){

        // Prepare select statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACTS)}.*
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACTS)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACTS)}
            .{$this->constants->valueOfConst(FIELD_USER_ID)} = ?
            ORDER BY {$this->constants->valueOfConst(KEY_CONTACTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_FULL_NAME)} ASC"
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

                $contacts[] = $row; // Add row to array
            }

            return $contacts; // Return contacts

        } else {
            // Query execution failed

            return false; // Return false
        }
    }
}

// EOF: ContactFunctions
