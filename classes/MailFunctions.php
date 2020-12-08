<?php

/**
* Mail functions class
* This class contains all the mailing functions required throught the project
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

// Disable error reporting
error_reporting(1);

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


// Import PHPMailer classes
require 'PHPMailer-6.1.8/src/PHPMailer.php';    // PHPMailer class
require 'PHPMailer-6.1.8/src/Exception.php';    // PHPMailer exception class
require 'PHPMailer-6.1.8/src/SMTP.php';         // PHPMailer SMTP class

error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_NOTICE|E_STRICT);

class MailFunctions
{

    // Connection status value variable
    private $connectToDB;

    // Create required class objects
    private $phpMailer;
    private $keys;
    private $dateTimeFunctions;

    // Shared variables
    private $mailCSSStyle;
    private $mailSupportDIV;
    private $response;


    /**
    * Class constructor
    */
    function __construct()
    {

        // Call required functions classes
        require_once 'DatabaseConnection.php';          // Database connection file
        require_once 'configs/MailConfiguration.php';   // Mail configuration file
        require_once 'Keys.php';                        // Keys file
        require_once 'DateTimeFunctions.php';           // DateTimeFunctions file
        require_once 'Paths.php';                       // Paths file

        // Creating objects of the required Classes

        // Initialize database connection class instance
        $connectionInstance = DatabaseConnection::getConnectionInstance();

        // Initialize connection object
        $this->connectToDB  = $connectionInstance->getDatabaseConnection();

        $this->keys         = new Keys(); // Keys class object
        $this->dateTimeFunctions = new DateTimeFunctions(); // DateTimeFunctions class object

        // Create JSON response array and initialize error to false
        $this->response = array(KEY_ERROR => false);

        // Initializing PHPMailer
        $this->phpMailer = new PHPMailer(); // Passing true enables exceptions

        try {

            // Set mail SMTP options
            $this->phpMailer->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => VERIFY_PEER,
                    'verify_peer_name' => VERIFY_PEER_NAME,
                    'allow_self_signed' => ALLOW_SELF_SIGNED
                )
            );

            // Server settings
            $this->phpMailer->SMTPDebug  = SMTP::DEBUG_SERVER; // Disable verbose
            // = 0 => off (for production use, No debug messages)
            // = 1 => client messages
            // = 2 => client and server messages
            // = SMTP::DEBUG_OFF; - as 0, Disable debugging (you can also leave this out completely,
            // 0 is the default).
            // = SMTP::DEBUG_CLIENT; - Output messages sent by the client.

            //As 1, plus responses received from the server (most  useful setting).
            // = SMTP::DEBUG_SERVER;

            // As 2, plus more information about the initial connection - this level can help
            // diagnose STARTTLS failures.
            // = SMTP::DEBUG_CONNECTION;

            // As 3, plus even lower-level information, very verbose, don't use for
            // debugging SMTP, only low-level problems
            // = SMTP::DEBUG_LOWLEVEL;

            //$this->phpMailer->isSMTP(); // Set mailer to use SMTP

            // Specify main and backup SMTP servers
            $this->phpMailer->Host = MAIL_HOST;
            $this->phpMailer->SMTPAuth = SMTP_AUTH; // Enable SMTP authentication

            // Enable TLS encryption, `ssl` also accepted
            $this->phpMailer->SMTPSecure = "tls";

            // TCP port to connect to. the port for TLS is 587, for SSL is 465 and non-secure
            // is 25
            $this->phpMailer->Port = MAIL_PORT;

            // $this->phpMailer->addCC('cc@example.com');
            // $this->phpMailer->addBCC('bcc@example.com');

            // Content
            $this->phpMailer->isHTML(true);   // Set email format to HTML

            // Mail CSS styling
            $this->mailCSSStyle = '
            <style>

            #table{
                table-layout: auto;
                width: 65%; padding: 3px;
                border-spacing: 15px 25px;
                empty-cells: hide;
                border: 1px solid #1565C0;
                border-collapse: collapse;
                border-style: solid dotted;
            }

            #tableSupport{
                table-layout: auto;
                border-spacing: 15px 5px;
                width: 40%;
                empty-cells: hide;
                border: 1px solid #9E9E9E;

                border-style: solid dotted;
            }

            #table th{
                background-color: #1565C0;
                color: #FFFFFF;
                border-bottom: 1px solid #ddd;
            }

            #table td{
                border: none;
                background-color: #FFFFFF;
            }

            #tableSupport td{
                border: none;
            }

            #tableSupport tr{
                background-color: #FFFFFF;
            }

            #table tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            #table img{
                display:block;
                width:100%;
            }

            #message{
                background-color: #ffffff;
                margin-top: 30px;
                margin-bottom: 10px;
                margin-right: 10px;
                margin-left: 20px;
            }

            #message h2{
                color: #000000;
                font-weight: normal;
                text-align: justify;
            }

            #message h3{
                color: #000000;
                font-weight: normal;
                text-align: justify;
            }

            #support{
                background-color: #FAFAFA;
                margin-top: 10px;
                margin-bottom: 10px;
                margin-right: 10px;
                margin-left: 15px;
            }

            #support h2{
                color: #000000;
                font-weight: normal;
                text-align: justify;
            }

            #support h3{
                color: #000000;
                font-weight: normal;
                text-align: justify;
            }

            #supportShadeGrey{
                background-color: #BDBDBD;
                margin-right: 10px;
                margin-left: 10px;
                margin-top: 10px;
            }

            #message h1{
                color: #1565C0;
                font-weight: 900;
                text-shadow: 3px 2px red;
            }
            </style>';

            // Support section DIV
            $this->mailSupportDIV = '
            <div id = "support">
            <br>
            <h2><b>Support:</b></h2>
            <h3>For any support with respect to your relationship with us you can always contact us directly using
            the Information below.</h3><br>
            <table align = "start" id = "tableSupport">
            <tr>
            <td id = "supportShadeGrey">
            <h3 style = "margin-end:10px;">Emails</h3>
            </td>
            <td>
            <h3 style = "margin-start:10px;"> '
            . EMAIL_ADDRESS_INFO_USERNAME . '</h3>
            </td>
            </tr>
            <tr>
            <td></td>
            <td>
            <h3 style = "margin-start:10px;"> '
            . EMAIL_ADDRESS_SUPPORT_USERNAME .'</h3>
            </td>
            </tr>
            </table>
            <br>
            </div>';

        } catch (\Exception $e) {

            // Set response error to true
            $response[KEY_ERROR] = true;

            // echo 'Message could not be sent. Mailer Error: ', $phpMailer->ErrorInfo;
            $response[KEY_ERRORMessage] = "Something went terribly wrong!";

            // Return response
            echo json_encode($response);
        }

    }


    /**
    * Class destructor
    */
    function __destruct()
    {

    }


    /**
    * Function to set SMTP client username and password for authentication
    *
    * @param emailUsername
    */
    private function configureSMTPUsernameAndPassword($emailUsername)
    {

        // Switch email usernames
        switch($emailUsername){

            case EMAIL_ADDRESS_INFO_USERNAME:
            // SMTP client(INFO) name and password

            $this->phpMailer->Username = EMAIL_ADDRESS_INFO_USERNAME;
            $this->phpMailer->Password = EMAIL_ADDRESS_INFO_PASSWORD;

            // Sender
            $this->phpMailer->setFrom(
                EMAIL_ADDRESS_INFO_USERNAME,
                COMPANY_NAME
            );

            $this->phpMailer->addReplyTo(
                EMAIL_ADDRESS_INFO_USERNAME,
                'Info Team -> (' . COMPANY_NAME . ')'
            );

            break;

            case EMAIL_ADDRESS_SUPPORT_USERNAME:
            // SMTP client(SUPPORT) name and password

            $this->phpMailer->Username = EMAIL_ADDRESS_SUPPORT_USERNAME;
            $this->phpMailer->Password = EMAIL_ADDRESS_SUPPORT_PASSWORD;

            // Sender
            $this->phpMailer->setFrom(
                EMAIL_ADDRESS_SUPPORT_USERNAME,
                COMPANY_NAME
            );

            $this->phpMailer->addReplyTo(
                EMAIL_ADDRESS_SUPPORT_USERNAME,
                'Support Team -> (' . COMPANY_NAME . ')'
            );

            break;

            case EMAIL_ADDRESS_NO_REPLY_USERNAME:
            // SMTP client(NO REPLY) name and password

            $this->phpMailer->Username = EMAIL_ADDRESS_NO_REPLY_USERNAME;
            $this->phpMailer->Password = EMAIL_ADDRESS_NO_REPLY_PASSWORD;

            // Sender
            $this->phpMailer->setFrom(
                EMAIL_ADDRESS_NO_REPLY_USERNAME,
                COMPANY_NAME
            );

            break;

            default:
            // SMTP client(NO REPLY) name and password

            $this->phpMailer->Username = EMAIL_ADDRESS_NO_REPLY_USERNAME;
            $this->phpMailer->Password = EMAIL_ADDRESS_NO_REPLY_PASSWORD;

            // Sender
            $this->phpMailer->setFrom(
                EMAIL_ADDRESS_NO_REPLY_USERNAME,
                COMPANY_NAME
            );
            break;
        }
    }


    /**
    * Function to send email verification for password reset
    *
    * @param firstName              - Clients first name
    * @param emailAddress           - Clients email address
    * @param verificationCode       - Generated verification code
    *
    * @return boolean               - true/false - (if/not mail sent)
    */
    public function sendClientEmailAccountVerificationCodeMail(
        $firstName,
        $emailAddress,
        $verificationCode
    ) {

        // Configure SMTP email and password
        $this->configureSMTPUsernameAndPassword(EMAIL_ADDRESS_NO_REPLY_USERNAME);

        // Set email subject
        $this->phpMailer->Subject = 'User Verification';

        // Attach file client_email_verification.png, and later link to it using identfier emailVerificationImg
        $this->phpMailer->AddEmbeddedImage(
            PATH_IMAGE_ACCOUNT_EMAIL_VERIFICATION,
            'emailVerificationImg',
            'account_email_verification.png'
        );

        $this->phpMailer->Body = '
        <html>
        <head>' . $this->mailCSSStyle . '</head>
        <body>
        <div style="overflow-x:auto;">
        <table align = "center" id = "table">
        <tr>
        <th>
        <h1><b>' . COMPANY_NAME . '</b></h1>
        <br>
        <img src="cid:emailVerificationImg" alt = "Loading image..."/>
        </th>
        </tr>
        <tr>
        <td>
        <div id = "message">
        <h2> Hello <b>' . $firstName .'</b>,</h2>
        <h2> To get started on ' . COMPANY_NAME . ', kindly verify your email address.<br>Your email verification code is: </h2><h1><u>' . $verificationCode . '</u></h1><h2> Enter the code on our website or android app when queried to verify your email address and continue enjoying our amazing features.<b>This code will expire after ' .
        KEY_VERIFICATION_CODE_EXPIRY_TIME . ' hour.</b>
        </h2>
        <br>
        <h2> <b>P.S.</b> We would also love hearing from you and helping you with any issues or complaints you migh have. Please reply to this email if you have any questions.
        </h2>
        <h3>Kind Regards,<br><b> ' . COMPANY_NAME . ' Team.</b>
        </h3>
        </div>' . $this->mailSupportDIV .
        '</td>
        </tr>
        <tr>
        <td>
        <h3 style = "margin-right: 20px; margin-left: 20px;"><i>Cheers!</i></h3>
        </td>
        </tr>
        </table>
        </div>
        </body>
        </html>';

        // This is the body in plain text for non-HTML mail clients
        $this->phpMailer->AltBody = 'Hello ' . $firstName .', to get started on
        ' . COMPANY_NAME . ', kindly verify your email address. Your email verification code is ' . $verificationCode . '. Enter the code on our website or android app when queried to verify your email address and contine enjoying our amazing features. P.S. We would also love hearing from you and helping you with any issues or complaints you migh have. Please reply to this email if you have any questions.
        Kind Regards, '  . COMPANY_NAME . ' Team';

        $this->phpMailer->addAddress($emailAddress, $firstName);     // Add a recipient
        // $this->phpMailer->addAddress('ellen@example.com');         // Name is optional

        // Attachments
        // $this->phpMailer->addAttachment('/tmp/static_images/client_email_verification.png',
        //                                 'client_email_verification.png'); // Optional name
        // $this->phpMailer->addAttachment('/tmp/image.jpg', 'new.jpg');     // Optional name
        // $this->phpMailer->addAttachment('/var/tmp/file.tar.gz');          // Add attachments

        // Send mail and check if it was sent
        if ($this->phpMailer->send()) {
            // Mail Sent

            // Return True
            return true;

        } else {

            // Return False
            return false;
        }
    }


    /**
    * Function to send email verification/ for password reset
    *
    * @param firstName          - Clients first name
    * @param emailAddress,      - Clients email address
    * @param verificationCode   - Generated verification code
    *
    * @return boolean           - true/false - (if/not mail sent)
    */
    public function sendClientPasswordResetEmailVerificationCodeMail(
        $firstName,
        $emailAddress,
        $verificationCode
    ) {

        // Configure SMTP email and password
        $this->configureSMTPUsernameAndPassword(EMAIL_ADDRESS_NO_REPLY_USERNAME);

        // Set email subject
        $this->phpMailer->Subject = 'Password Reset Email Verification';

        // Attach file password_reset_email_verification.png, and later link to it using identfier
        // passwordResetEmailVerificationImg
        $this->phpMailer->AddEmbeddedImage(
            PATH_IMAGE_PASSWORD_RESET_EMAIL_VERIFICATION,
            'passwordResetEmailVerificationImg',
            'password_reset_email_verification.png'
        );

        // Forgot password body
        $this->phpMailer->Body = '
        <html>
        <head>' . $this->mailCSSStyle . '</head>
        <body>
        <div style="overflow-x:auto;">
        <table align = "center" id = "table">
        <tr>
        <th>
        <h1><b>' . COMPANY_NAME . '</b></h1>
        <br>
        <img src="cid:passwordResetEmailVerificationImg" alt = "Loading image..."/>
        </th>
        </tr>
        <tr>
        <td>
        <div id = "message">
        <h2> Hello <b>' . $firstName .'</b>,</h2>
        <h2> To reset your account password, kindly verify your email address. Your email verification code is: </h2>
        <h1><u>' . $verificationCode . '</u></h1>
        <h2> Enter the code on our website or android app when queried to verify your email address and reset your password. <b>This code will expire after '
        . KEY_VERIFICATION_CODE_EXPIRY_TIME . ' hour.</b>
        </h2>
        <h2> If you did not request a password reset please ignore this email or reply to let us know.</h2>
        <h2>
        <b>P.S.</b> We would also love hearing from you and helping you with any issues or complaints you migh have. Please reply to this email if you have any questions.
        </h2>
        <h3>We cannot wait to have you back! <br>Kind Regards,<br><b> ' . COMPANY_NAME . ' Team.</b>
        </h3>
        </div>' . $this->mailSupportDIV .'
        </td>
        </tr>
        <tr>
        <td>
        <h3 style = "margin-right: 20px; margin-left: 20px;"><i>Cheers!</i></h3>
        </td>
        </tr>
        </table>
        </div>
        </body>
        </html>';

        // This is the body in plain text for non-HTML mail clients
        $this->phpMailer->AltBody = 'Hello ' . $firstName .', to reset your account password, kindly verify your email address. Your email verification code is ' . $verificationCode . '. Enter the code on our website or android app when queried to verify your email address and reset you password.
        If you did not request a password reset please ignore this email or reply to let us know. P.S. We would also love hearing from you and helping you with any issues or complaints you migh have. Please reply to this email if you have any questions.
        We cannot wait to have you back! Kind Regards, ' . COMPANY_NAME . ' Team.';

        $this->phpMailer->addAddress($emailAddress, $firstName);     // Add a recipient
        // $this->phpMailer->addAddress('ellen@example.com');         // Name is optional

        // Attachments
        // $this->phpMailer->addAttachment('/tmp/static_images/client_email_verification.png',
        //                                  'client_email_verification.png');  // Optional name
        // $this->phpMailer->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        // $this->phpMailer->addAttachment('/var/tmp/file.tar.gz');         // Add attachments

        // Send mail and check if it was sent
        if ($sendMail = $this->phpMailer->send()) {
            // Mail sent

            // Return true
            return true;

        } else {

            // Return false
            return false;
        }
    }


    /**
    * Function to notify Client of password reset event by mail
    *
    * @param firstName      - Clients firt name
    * @param emailAddress   - Clients email address
    *
    * @return boolean       - (true/false - if/not mail sent)
    */
    public function sendPasswordChangeNotificationMail($firstName, $emailAddress)
    {

        // Configure SMTP email and password
        $this->configureSMTPUsernameAndPassword(EMAIL_ADDRESS_INFO_USERNAME);

        // Set email subject
        $this->phpMailer->Subject = 'Account Password Change';

        // Attach file client_email_verification.png, and later link to it using identfier passwordChangeImg
        // $this->phpMailer->AddEmbeddedImage('images/php_mailer_images/client_email_verification.png',
        //                                      'passwordChangeImg',
        //                                      'images/php_mailer_images/client_email_verification.png');

        $this->phpMailer->Body = '
        <html>
        <head>' . $this->mailCSSStyle . '</head>
        <body>
        <div style="overflow-x:auto;">
        <table align = "center" id = "table">
        <tr>
        <th>
        <br>
        <h1><b>' . COMPANY_NAME . '</b></h1>
        <br>
        </th>
        </tr>
        <tr>
        <td>
        <div id = "message">
        <h2> Hello <b>' . $firstName .'</b>,</h2>
        <h2> Your account password has been changed successfully. If this was you, then you can safely ignore this email.
        </h2>
        <h2> <b>Didn\'t request a new password?</b><br>If you didn\'t change your account password, please let us know immediately by replying to this email.
        </h2>
        <br>
        <h2><b>P.S.</b> We would also love hearing from you and helping you with any issues or complaints you migh have. Please reply to this email if you '. 'have any questions.
        </h2>
        <h3> Kind Regards,<b> ' . COMPANY_NAME
        . ' Team</b></h3>
        </div>'. $this->mailSupportDIV .'
        </td>
        </tr>
        <tr>
        <td>
        <h3 style = "margin-right: 20px; margin-left: 20px;"><i>Cheers!</i></h3>
        </td>
        </tr>
        </table>
        </div>
        </body>
        </html>';

        // This is the body in plain text for non-HTML mail clients
        $this->phpMailer->AltBody = 'Hello ' . $firstName .', Your account password has been changed successfully. If this was you, then you can safely ignore this email. If you didn\'t change your account password, please let us know immediately by replying to this email. P.S.
        We would also love hearing from you and helping you with any issues or complaints you migh have. Please reply to this email if you have any questions.
        Kind Regards, ' . COMPANY_NAME . ' Team';

        $this->phpMailer->addAddress($emailAddress, $firstName); // Add a recipient
        // $this->phpMailer->addAddress('ellen@example.com'); // Name is optional

        // Attachments
        // $this->phpMailer->addAttachment('/tmp/static_images/client_email_verification.png',
        //                                  'client_email_verification.png'); // Optional name
        // $this->phpMailer->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name
        // $this->phpMailer->addAttachment('/var/tmp/file.tar.gz');   // Add attachments

        // Send mail and check if it was sent
        if ($this->phpMailer->send()) {
            // Mail sent

            // Return true
            return true;

        } else {

            // Return false
            return false;
        }
    }


    /**
    * Function to revoke email verification on email update
    *
    * @param ClientId   - Clients id
    *
    * @return boolean   - true/false - (if/not revoked)
    */
    public function revokeEmailVerification($clientId)
    {

        $emailVerified = "FALSE"; // Email verified new value

        // Prepare statement
        $stmt = $this->connectToDB->prepare(
            "UPDATE {$this->keys->valueOfConst(TABLE_CLIENTS)}
            SET {$this->keys->valueOfConst(FIELD_EMAIL_VERIFIED)} = ?
            WHERE {$this->keys->valueOfConst(TABLE_CLIENTS)}
            .{$this->keys->valueOfConst(FIELD_CLIENT_ID)}
            = ?"
        );

        $stmt->bind_param("ss", $emailVerified, $clientId); // Bind parameters
        $execute = $stmt->execute(); // Execute statement
        $stmt->close(); // Close statement

        // Check for query execution
        if ($execute) {
            // Query executed

            // Check if EmailVerified value is false
            // Prepare statement
            $stmt = $this->connectToDB->prepare(
                "SELECT * FROM {$this->keys->valueOfConst(TABLE_CLIENTS)}
                AS {$this->keys->valueOfConst(KEY_CLIENT)}
                WHERE {$this->keys->valueOfConst(KEY_CLIENT)}
                .{$this->keys->valueOfConst(FIELD_CLIENT_ID)} = ?
                AND {$this->keys->valueOfConst(KEY_CLIENT)}
                .{$this->keys->valueOfConst(FIELD_EMAIL_VERIFIED)}
                = ?"
            );
            $stmt->bind_param("ss", $clientId, $emailVerified); // Bind parameters
            $stmt->execute(); // Execute statement
            $stmt->store_result(); // Store resultt

            // Check if record was found
            if ($stmt->num_rows > 0) {
                // Record found thus email verification revoked

                $stmt->close(); // Close statement

                // Delete any existing email verification records for the changed email
                // address from the email verificatio table

                // Pass ClientId without VerificationType
                if ($this->deleteEmailVerificationDetails($clientId, "") !== false) {
                    // Existing email record deletion failed

                    return true; // Return true

                } else {

                    return false; // Return false
                }
            } else {
                // Record not found thus email verification not revoked

                $stmt->close(); // Close statement

                return false; // Return false
            }
        } else {
            // Query execution failed

            return false; // Return false
        }
    }


    /**
    * Function to delete email verification details for ClientId based on verification type.
    *
    * @param ClientId - Clients id
    * @param VerificationType - Verification code type
    *
    * @return boolean - true/false (on record deletion / failure)
    * @return null - on record not found
    */
    public function deleteEmailVerificationDetails($clientId, $verificationType)
    {

        // Select params with ClientId only
        $selectParams = ", {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
        .{$this->keys->valueOfConst(FIELD_CLIENT_ID)} = ?";

        // Delete params with ClientId only
        $deleteParams = ", {$this->keys->valueOfConst(TABLE_EMAIL_VERIFICATION)}
        .{$this->keys->valueOfConst(FIELD_CLIENT_ID)} = ?";

        $bindParamTypes = ""; // Bind param values
        $bindParamValues = array(); // Bind param value array

        $bindParamValues[0] = $clientId; // Add ClientId to bind param array

        if (!empty($verificationType)) {

            // Select params with ClientId and VerificationType
            $selectParams .= ", {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
            .{$this->keys->valueOfConst(FIELD_VERIFICATION_TYPE)} = ?";

            // Delete params with ClientId and VerificationType
            $deleteParams .= ", {$this->keys->valueOfConst(TABLE_EMAIL_VERIFICATION)}
            .{$this->keys->valueOfConst(FIELD_VERIFICATION_TYPE)} = ?";

            $bindParamValues[1] = $verificationType; // Add VerificationType to bind param array

            // Get bind param types
            $bindParamTypes = str_repeat("s", count($bindParamValues));
        }

        // Select command
        $selectCommand = "SELECT {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}.*
        FROM {$this->keys->valueOfConst(TABLE_EMAIL_VERIFICATION)}
        AS {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
        WHERE {$selectParams}";

        // Delete command
        $deleteCommand = "DELETE FROM {$this->keys->valueOfConst(TABLE_EMAIL_VERIFICATION)}
        WHERE {$deleteParams}";

        // Remove the comma after WHERE keyword
        $selectCommand = str_replace("WHERE ,", "WHERE", $selectCommand);
        $deleteCommand = str_replace("WHERE ,", "WHERE", $deleteCommand);

        // Replace remaining commas with AND keyword
        $selectCommand = str_replace(", ", " AND ", $selectCommand);
        $deleteCommand = str_replace(", ", " AND ", $deleteCommand);

        $stmt = $this->connectToDB->prepare($selectCommand); // Prepare SELECT statement
        $stmt->bind_param($bindParamTypes, ...$bindParamValues); // Bind parameters
        $stmt->execute(); // Execute statement
        $stmt->store_result(); // Store result

        // Chec for record
        if ($stmt->num_rows > 0) {
            // Record found

            $stmt->close(); // Close statement

            $stmt = $this->connectToDB->prepare($deleteCommand); // Prepare DELETE statement
            $stmt->bind_param($bindParamTypes, ...$bindParamValues); // Bind ClientId parameter
            $delete = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

            // Check for query execution
            if ($delete) {
                // Record deleted

                return true; // Return true

            } else {
                // Record not deleted

                return false; // Return false
            }
        } else {
            // Record found

            $stmt->close(); // Close statement

            return null; // Return null
        }
    }


    /**
    * Function to check if client had requested for a verification code earlier
    *
    * @param ClientId - Clients Id
    * @param VerificationType - Verification code type
    *
    * @return array - Associative array (verification details for client if found)
    * @return boolean - false - (if client verification details not found)
    */
    public function checkForVerificationRequestRecord($clientId, $verificationType)
    {

        // Check if A Previous Verification Record Exists
        $selectStatement = "SELECT {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}.*
        FROM {$this->keys->valueOfConst(TABLE_EMAIL_VERIFICATION)}
        AS {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
        WHERE {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
        .{$this->keys->valueOfConst(FIELD_CLIENT_ID)} = ?
        AND {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
        .{$this->keys->valueOfConst(FIELD_VERIFICATION_TYPE)} = ?";

        $stmt = $this->connectToDB->prepare($selectStatement); // Prepare statement
        $stmt->bind_param("ss", $clientId, $verificationType); // Bind parameters
        $stmt->execute(); // Execute statement
        $stmt->store_result(); // Store statement execution result

        // Check For Query Execution
        if ($stmt->num_rows > 0) {
            // Record found

            $stmt->close(); // Close statement

            $stmt = $this->connectToDB->prepare($selectStatement); // Prepare statement
            $stmt->bind_param("ss", $clientId, $verificationType); // Bind parameters

            // Check for query execution
            if ($stmt->execute()) {
                // SQL statement executed

                $check = $stmt->get_result()->fetch_assoc(); // Get result array
                $stmt->close(); // Close statement

                // Return associative array
                return $check;
            }
        } else {
            // Record Not Found

            $stmt->close(); // Close Statement

            // Return False
            return false;
        }
    }


    /**
    * Function generate, store to database and return verification code
    *
    * @param ClientId - Clients Id
    * @param EmailAddress - Clients emailAddress
    * @param VerificationType - Associative array - (verification code type)
    *
    * @return array - verification details
    * @return boolean - false - (Verification code generation and storing failed)
    */
    public function generateNewEmailVerificationCode($clientId, $emailAddress, $verificationType)
    {

        // Get current date and time
        $requestTimeStamp = $this->dateTimeFunctions->getDefaultTimeZoneNumericalDateTime();

        // Generate A 15 Character VerificationId
        $verificationId = substr( "ver" . md5(mt_rand()), 0, LENGTH_TABLE_IDS);

        $codeIdentifier = ""; // Code type identifier

        // Check for verification code type
        if ($verificationType == KEY_VERIFICATION_TYPE_EMAIL_ACCOUNT) {
            // Client email account verification

            $codeIdentifier = "ua"; // User account

        } else if ($verificationType == KEY_VERIFICATION_TYPE_PASSWORD_RESET) {
            // Password reset email verfication

            $codeIdentifier = "pr"; // User account
        }

        // Generate a 6 character unique code
        $verificationCode 	= substr(
            $codeIdentifier . md5(mt_rand()),
            0,
            LENGTH_VERIFICATION_CODE
        );

        // Get current date and time
        $currentNumericalTimeStamp 	=
        $this->dateTimeFunctions->getDefaultTimeZoneNumericalDateTime();

        // Insert Verification Details
        $stmt = $this->connectToDB->prepare(
            "INSERT INTO {$this->keys->valueOfConst(TABLE_EMAIL_VERIFICATION)}
            (VerificationId, ClientId, EmailAddress, VerificationCode, CodeRequestTime, VerificationType) VALUES(?, ?, ?, ?, ?, ?)"
        );

        // Bind paramaters
        $stmt->bind_param(
            "ssssss",
            $verificationId,
            $clientId,
            $emailAddress,
            $verificationCode,
            $currentNumericalTimeStamp,
            $verificationType
        );

        $result = $stmt->execute(); // Execute statement
        $stmt->close(); // Close statement

        // Check For Query Execution
        if ($result){

            // Get Email Verification Details
            $stmt = $this->connectToDB->prepare(
                "SELECT * FROM {$this->keys->valueOfConst(TABLE_EMAIL_VERIFICATION)}
                AS {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
                WHERE {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
                .{$this->keys->valueOfConst(FIELD_CLIENT_ID)} = ?
                AND {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
                .{$this->keys->valueOfConst(FIELD_EMAIL_ADDRESS)} = ?
                AND {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
                .{$this->keys->valueOfConst(FIELD_VERIFICATION_TYPE)} = ?"
            );
            $stmt->bind_param("sss", $clientId, $emailAddress, $verificationType);
            $stmt->execute(); // Execute statement
            $verificationDetails = $stmt->get_result()->fetch_assoc(); // Get result array
            $stmt->close(); // Close statement

            // Return associative array
            return $verificationDetails;

        } else {
            // Verification code generation and storing failed

            // Return False
            return false;
        }
    }


    /**
    * Function to verify email verification code
    *
    * @param ClientId - Clients Id
    * @param VerificationCode - Clients verification code
    * @param VerificationType - Clients verification code type
    *
    * @return boolean - true/false - (Record found / not found)
    */
    public function verifyEmaiVerificationCode($clientId, $verificationType, $verificationCode)
    {
        // Get Email Verification Details
        $stmt = $this->connectToDB->prepare(
            "SELECT * FROM {$this->keys->valueOfConst(TABLE_EMAIL_VERIFICATION)}
            AS {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
            WHERE {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
            .{$this->keys->valueOfConst(FIELD_CLIENT_ID)} = ?
            AND {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
            .{$this->keys->valueOfConst(FIELD_VERIFICATION_TYPE)} = ?
            AND {$this->keys->valueOfConst(KEY_EMAIL_VERIFICATION)}
            .{$this->keys->valueOfConst(FIELD_VERIFICATION_CODE)} = ?"
        );
        $stmt->bind_param("sss", $clientId, $verificationType, $verificationCode);
        $stmt->execute(); // Execute statement
        $stmt->store_result(); // Store statement execution result

        // Check if record found
        if ($stmt->num_rows > 0) {
            // Record found

            $stmt->close(); // Close statement

            return true; // Return true

        } else {
            // Record not found

            $stmt->close(); // Close statement

            return false; // Return false
        }
    }

}

// EOF: MailFunctions.php
