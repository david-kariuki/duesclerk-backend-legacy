<?php

/**
* User account functions class
* This class contains all the functions required to manage and process contacts
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
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
    public function isEmailAddressInContactsTable($contactEmailAddress)
    {

        // Check for email address in contacts table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_EMAIL_ADDRESS)}
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACT)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_EMAIL_ADDRESS)} = ?"
        );
        $stmt->bind_param("s", $contactEmailAddress); // Bind parameters
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
    public function isPhoneNumberInContactsTable($contactPhoneNumber)
    {

        // Check for phone number in contacts table
        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "SELECT {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_PHONE_NUMBER)}
            FROM {$this->constants->valueOfConst(TABLE_CONTACTS)}
            AS {$this->constants->valueOfConst(KEY_CONTACT)}
            WHERE {$this->constants->valueOfConst(KEY_CONTACT)}
            .{$this->constants->valueOfConst(FIELD_CONTACTS_PHONE_NUMBER)} = ?"
        );
        $stmt->bind_param("s", $contactPhoneNumber); // Bind parameters
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
    public function getContactByPhoneNumber($contactPhoneNumber)
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
    public function getContactByEmailAddress($contactEmailAddress)
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

        // Get contact details from associative array
        $contactFullName        = $contactDetails[FIELD_CONTACTS_FULL_NAME];
        $contactPhoneNumber     = $contactDetails[FIELD_CONTACTS_PHONE_NUMBER];
        $contactType            = $contactDetails[FIELD_CONTACTS_TYPE];
        $contactEmailAddress    = "";
        $contactAddress         = "";

        if (
            array_key_exists(FIELD_CONTACTS_FULL_NAME, $contactDetails)
            && array_key_exists(FIELD_CONTACTS_PHONE_NUMBER, $contactDetails)
            && array_key_exists(FIELD_CONTACTS_TYPE, $contactDetails)
        ) {
            // Required fields set

            // Check for contact email address
            if (array_key_exists(FIELD_CONTACTS_EMAIL_ADDRESS, $contactDetails)) {
                // Contact email address exists

                $contactEmailAddress = $contactDetails[FIELD_CONTACTS_EMAIL_ADDRESS];
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
                $contactId, $contactFullName, $contactPhoneNumber, $contactEmailAddress, $contactAddress, $contactType, $userId
            );
            $add = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

            // Check for query execution
            if ($add) {
                // Querry xecution successful

                // Retrun contact details
                return $this->getContactByPhoneNumber($contactPhoneNumber);

            } else {
                // Query execution failed

                return false; // Return false
            }
        } else {
            // Missing required fields

            return null; // Return null
        }
    }
}

// EOF: ContactFunctions
