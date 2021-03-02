<?php

/**
* Debts functions class
* This class contains all the functions required to manage and process debts
*
* @author David Kariuki (dk)
* @copyright (c) 2020 - 2021 David Kariuki (dk) All Rights Reserved.
*/

// Namespace declaration
namespace duesclerk\debt;

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
class DebtFunctions
{

    // Connection status value variable
    private $connectToDB;       // Create DatabaseConnection class object
    private $constants;         // Create Constants class object
    private $sharedFunctions;   // Create SharedFunctions class object
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
            $debtDateDue = $this->dateTimeFunctions->convertDateTimeFromFormat(
                $debtDetails[FIELD_DEBT_DATE_DUE],
                FORMAT_DATE_FULL,
                FORMAT_DATE_SHORT
            );

            $debtDescription = ""; // Debt description

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
    public function getContactsDebtsData($contactId, $contactType, $userId)
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
                $totalDebtsAmount =
                $this->getSingleContactDebtsTotalFromArray($debts);

                // Debts data associative array to hold debts array and debts total amount
                $debtsData = array(
                    KEY_DEBTS => array(),           // Set to array
                    KEY_DEBTS_TOTAL_AMOUNT => 0   // Initialize to 0
                );

                $debtsData[KEY_DEBTS] = $debts; // Add debts to debts data array

                // Add total debts amount to debts data array
                $debtsData[KEY_DEBTS_TOTAL_AMOUNT] = $totalDebtsAmount;

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

            $debtDetails = $stmt->get_result()->fetch_assoc(); // Get result array
            $stmt->close(); // Close statement

            return $debtDetails; // Return debt details array

        } else {
            // Debt not found

            $stmt->close(); // Close statement

            return false; // Return false
        }
    }

    /**
    * Function to get single contact debts total amount
    *
    * @param debts  - All contacts debts
    *
    * @return int   - Total debts amount
    * @return int   - 0 - (If array count is 0)
    * @return null  - on fetched array empty
    */
    public function getSingleContactDebtsTotalFromArray($debts)
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
                return $this->sharedFunctions->roundOffFloat($totalDebtsAmount, 2);

            } else {

                return 0; // If array count is 0 (no record found)
            }
        } else {

            return null; // If passed parameter is not an array
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
    public function getContactsWithTheirDebtsTotalFromArray($contacts)
    {

        // Check if passed parameter is array
        if (is_array($contacts)) {
            // Passed parameter is array

            // Check array size
            if (sizeof($contacts) > 0) {

                // Loop through all contacts debts
                foreach($contacts as $key => $contact) {

                    $contactId      = ""; // Contact id
                    $contactType    = ""; // Contact type
                    $userId         = ""; // User id

                    // Loop through single debt to get debt details
                    foreach ($contact as $key => $contactDetails) {

                        $contactId      = $contact[FIELD_CONTACT_ID]; // Set contact id
                        $contactType    = $contact[FIELD_CONTACT_TYPE]; // Set contact type
                        $userId         = $contact[FIELD_USER_ID]; // Set user id
                    }

                    // Get all debts for contact
                    $contactsDebtsData = $this->getContactsDebtsData(
                        $contactId,
                        $contactType,
                        $userId
                    );

                    // Get debts total for single contact from contacts debts
                    $debtsTotalAmount = $contactsDebtsData[KEY_DEBTS_TOTAL_AMOUNT];

                    // Check if total amount is null
                    if ($debtsTotalAmount != null) {

                        // Add debts total amount to contact
                        $contact[KEY_DEBTS_TOTAL_AMOUNT] = $debtsTotalAmount;

                    } else {

                        // Set amount to null
                        $contact[KEY_DEBTS_TOTAL_AMOUNT] = "0.00";


                    }

                    // Check if debts total amount is greater than 0
                    if ($debtsTotalAmount > 0) {

                        // Get contacts debts
                        $debts = $contactsDebtsData[KEY_DEBTS];

                        // Add number of debts to contact
                        $contact[KEY_CONTACTS_NUMBER_OF_DEBTS] = sizeof($debts);

                    } else {

                        // Add number of debts to contact
                        $contact[KEY_CONTACTS_NUMBER_OF_DEBTS] = "0";
                    }

                    // Add contact to array
                    $contactsWithTheirTotalDebtsAmount[] = $contact;
                }

                // Return each contacts with its total debt
                return $contactsWithTheirTotalDebtsAmount;

            } else {

                return 0; // If array count is 0 (no record found)
            }
        } else {

            return null; // If passed parameter is not an array
        }
    }

    /**
    * Function to sum up debts totals for each contacts
    *
    * @param contacts   - Contacts to loop through
    *
    * @return array     - allContactsDebtsTotal
    */
    public function getContactsDebtsTotalSumForAllUserContacts($contacts)
    {

        // Check if passed parameter is array
        if (is_array($contacts)) {
            // Passed parameter is array

            // Check array size
            if (sizeof($contacts) > 0) {

                // Variable to hold debts total for PeopleOwingMe contacts
                $allPeopleOwingMeContactsDebtsTotal = 0; // Variable

                // Variable to hold debts total for PeopleIOwe contacts
                $allPeopleIOweContactsDebtsTotal = 0;

                // All contacts debts totals array for both PeopleOwingMe
                // and PeopleIOwe contacts
                $allContactsDebtsTotal = array(
                    KEY_CONTACTS_DEBTS_TOTAL_PEOPLE_OWING_ME    => 0,
                    KEY_CONTACTS_DEBTS_TOTAL_PEOPLE_I_OWE       => 0
                );

                // Get all contacts with their debts totals
                $contactsWithTheirDebtsTotal = $this->getContactsWithTheirDebtsTotalFromArray(
                    $contacts
                );

                // Loop through contact
                foreach ($contactsWithTheirDebtsTotal as $contact) {

                    // Get contacts total
                    $contactTotalDebtsAmount    = $contact[KEY_DEBTS_TOTAL_AMOUNT];
                    $contactType                = $contact[FIELD_CONTACT_TYPE];

                    // Check contact type
                    if ($contactType == KEY_CONTACT_TYPE_PEOPLE_OWING_ME) {

                        // Add contacts debts total to all contacts debts total
                        $allPeopleOwingMeContactsDebtsTotal += $contactTotalDebtsAmount;

                    } else if ($contactType == KEY_CONTACT_TYPE_PEOPLE_I_OWE) {

                        // Add contacts debts total to all contacts debts total
                        $allPeopleIOweContactsDebtsTotal += $contactTotalDebtsAmount;
                    }

                }

                // Round off PeopleOwingMe totals to 2 decimal places
                $allPeopleOwingMeContactsDebtsTotal = $this->sharedFunctions->roundOffFloat(
                    $allPeopleOwingMeContactsDebtsTotal,
                    2
                );

                // Round off PeopleIOwe totals to 2 decimal places
                $allPeopleIOweContactsDebtsTotal = $this->sharedFunctions->roundOffFloat(
                    $allPeopleIOweContactsDebtsTotal,
                    2
                );

                // Add all PeopleOwingMe contacts debts totals to array
                $allContactsDebtsTotal[KEY_CONTACTS_DEBTS_TOTAL_PEOPLE_OWING_ME] = $allPeopleOwingMeContactsDebtsTotal;

                // Add all PeopleIOwe contacts debts totals to array
                $allContactsDebtsTotal[KEY_CONTACTS_DEBTS_TOTAL_PEOPLE_I_OWE] = $allPeopleIOweContactsDebtsTotal;

                // Return all contacts debts totals array
                return $allContactsDebtsTotal;

            } else {

                return 0; // If array count is 0 (no record found)
            }
        } else {

            return null; // If passed parameter is not an array
        }
    }

    /**
    * Function to delete contacts debt
    *
    * @param contactId  - Contacts id
    * @param userId     - Users id
    *
    * @return boolean   - (Delete success / failure)
    */
    public function deleteAllDebtsForContact($contactId, $userId)
    {

        // Prepare DELETE statement
        $stmt = $this->connectToDB->prepare(
            "DELETE FROM {$this->constants->valueOfConst(TABLE_DEBTS)}
            WHERE {$this->constants->valueOfConst(TABLE_DEBTS)}
            .{$this->constants->valueOfConst(FIELD_CONTACT_ID)} = ?
            AND {$this->constants->valueOfConst(TABLE_DEBTS)}
            .{$this->constants->valueOfConst(FIELD_USER_ID)} = ?"
        );

        $stmt->bind_param("ss", $contactId, $userId); // Bind parameters
        $deleted = $stmt->execute(); // Execute statememt
        $stmt->close(); // Close statement

        return $deleted; // Return deletion status
    }

    /**
    * Function to delete one or more contacts debts
    *
    * @param debtIds    - Single or multiple debt ids for debts to be deleted
    * @param contactId  - Contacts id
    *
    * @return boolean   - (Debt deletion successful / failed)
    */
    public function deleteContactsDebts($debtsIds, $contactId)
    {

        // Check if variable is array
        if (is_array($debtsIds)) {
            // Variable is array

            // Sanitize array elements
            $debtsIds = $this->sharedFunctions->sanitizeArrayElements($debtsIds);

            // Loop through array to get contact ids
            foreach($debtsIds as $debtId) {

                // Get debt by debt id
                $debt = $this->getDebtDetailsByDebtId($debtId);

                // Check if debt found
                if ($debt !== false) {
                    // Debt exists

                    // Get debts history - Coming soon
                    // $contactsDebts = array($this->debtFunctions->getContactsDebtsData(
                    //     $debtId,
                    //     $contact[FIELD_CONTACT_TYPE],
                    //     $userId
                    // ));
                    //
                    // // Check debts history size
                    // if (sizeof($contactsDebts) > 0) {
                    //     // Debts exist for contact
                    //
                    //     // Delete debts for contacts
                    //     if (!$this->debtFunctions->deleteAllDebtsForContact($debtId, $userId)) {
                    //         // Contacts debts not deleted
                    //
                    //         return 0; // Return 0
                    //     }
                    // }

                    // Prepare DELETE statement to delete debt from debts table
                    $stmt = $this->connectToDB->prepare(
                        "DELETE FROM {$this->constants->valueOfConst(TABLE_DEBTS)}
                        WHERE {$this->constants->valueOfConst(TABLE_DEBTS)}
                        .{$this->constants->valueOfConst(FIELD_DEBT_ID)} = ?
                        AND {$this->constants->valueOfConst(TABLE_DEBTS)}
                        .{$this->constants->valueOfConst(FIELD_CONTACT_ID)} = ?"
                    );

                    $stmt->bind_param("ss", $debtId, $contactId); // Bind parameters
                    $deleted = $stmt->execute(); // Execute statement
                    $stmt->close(); // Close statement

                } else {

                    return null; // Return null
                }
            }

            return $deleted; // Return deletion status
        }
    }
}
