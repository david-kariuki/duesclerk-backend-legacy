<?php
    // Mail functions class


    // Disable error reporting
    error_reporting(1);

    // Import PHPMailer classes into the global namespace
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    error_reporting(1);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL|E_NOTICE|E_STRICT);

    class MailFunctions
    {

        // Connection status value variable
        private $connectToDB;

        // Create required class objects
        private $keys;
        private $phpMailer;

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
            require_once 'Connection.php';      // Connection class
            require_once 'Keys.php';            // Keys class
            require_once 'MailFunctions.php';   // Mail functions class


            // Import PHPMailer classes
            require 'PHPMailer-6.1.8/src/Exception.php';    // PHPMailer exception class
            require 'PHPMailer-6.1.8/src/PHPMailer.php';    // PHPMailer class
            require 'PHPMailer-6.1.8/src/SMTP.php';         // PHPMailer SMTP class

            // Creating objects of the required Classes
            $connection 		= new Connection();

            // Initializing objects
            $this->connectToDB	= $connection->Connect(); // Conection object
            $this->keys   	= new Keys(); // Keys object

            // Create JSON response array and initialize error to false
            $this->response  = array($this->keys->keyError => false);

            /**
            * Initializing PHPMailer. Passing true enables exceptions
            */
            $this->phpMailer = new PHPMailer();

            try {

                // Set mail SMTP options
                $this->phpMailer->SMTPOptions = array('ssl' => array(   'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true));

                // Server settings
                $this->phpMailer->SMTPDebug  = 0; // Disable verbose
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

                $this->phpMailer->isSMTP(); // Set mailer to use SMTP

                // Specify main and backup SMTP servers
                $this->phpMailer->Host = $this->keys->mailHost;
                $this->phpMailer->SMTPAuth = true; // Enable SMTP authentication

                // SMTP client name and password
                $this->phpMailer->Username = 'noreply@' . $this->keys->domainNoWW;
                $this->phpMailer->Password = '4Ms0G2+SiUzy!4';


                // Enable TLS encryption, `ssl` also accepted
                $this->phpMailer->SMTPSecure = $this->keys->SMTPSecure;

                // TCP port to connect to. the port for TLS is 587, for SSL is 465 and non-secure
                // is 25
                $this->phpMailer->Port = $this->keys->mailPort;

                // Recipients
                $this->phpMailer->setFrom(
                    $this->keys->mailAddressNoReply,
                    $this->keys->companyName
                );
                $this->phpMailer->addReplyTo(
                    $this->keys->mailAddressInfo,
                    'Info Team -> (' . $this->keys->companyName . ')'
                );
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
                                . $this->keys->mailAddressInfo . '</h3>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <h3 style = "margin-start:10px;"> '
                                . $this->keys->mailAddressSupport .'</h3>
                            </td>
                        </tr>
                    </table>
                    <br>
                </div>';

            } catch (\Exception $e) {

                // Set response error to true
                $response[$this->keys->keyError] = true;

                // echo 'Message could not be sent. Mailer Error: ', $phpMailer->ErrorInfo;
                $response[$this->keys->keyErrorMessage] = "Something went terribly wrong!";

                // Return response
                echo json_encode($response);
            }

        }


        /**
        * Class destructor
        */
        function __destruct()
        {

    		// Close database connection
    		mysqli_close($this->connectToDB);
        }


    /**
    * Function to send email verification for password reset
    *
    * @param firstName              - Clients first name
    * @param emailAddress           - Clients email address
    * @param verificationCode       - Generated verification code
    * @return boolean               - true/false - if/not mail sent
    */
    public function sendUserAccountEmailVerificationMail(
        $firstName,
        $emailAddress,
        $verificationCode
        ) {

        // Set email subject
        $this->phpMailer->Subject = 'User Verification';

        // Attach file client_email_verification.png, and later link to it using identfier emailVerificationImg
        $this->phpMailer->AddEmbeddedImage(
            'images/php_mailer_images/client_email_verification.png',
            'emailVerificationImg',
            'images/php_mailer_images/client_email_verification.png'
        );

        $this->phpMailer->Body = '
        <html>
            <head>' . $this->mailCSSStyle . '</head>
            <body>
                <div style="overflow-x:auto;">
                    <table align = "center" id = "table">
                        <tr>
                            <th>
                                <h1><b>' . $this->keys->companyName . '</b></h1>
                                <br>
                                <img src="cid:emailVerificationImg" alt = "loading..."/>
                                </th>
                        </tr>
                        <tr>
                            <td>
                                <div id = "message">
                                    <h2> Hello <b>' . $firstName .'</b>,</h2>
                                    <h2> To get started on ' . $this->keys->companyName . ', kindly verify your email address.<br>Your email verification code is: </h2><h1><u>' . $verificationCode . '</u></h1><h2> Enter the code on our website or android app when queried to verify your email address and continue enjoying our amazing lessons.<b>This code will expire after ' .
                                        $this->keys->verificationCodeExpiryTime . ' hour.</b>
                                    </h2>
                                    <br>
                                    <h2> <b>P.S.</b> We would also love hearing from you and helping you with any issues or complaints you migh have. Please reply to this email if you have any questions.
                                    </h2>
                                    <h3>Kind Regards,<br><b> ' . $this->keys->companyName . ' Team.</b>
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
         ' . $this->keys->companyName . ', kindly verify your email address. Your email verification code is ' . $verificationCode . '. Enter the code on our website or android app when queried to verify your email address and contine enjoying our amazing lessons. P.S. We would also love hearing from you and helping you with any issues or complaints you migh have. Please reply to this email if you have any questions.
        Kind Regards, '  . $this->keys->companyName . ' Team';

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
        * @return boolean           - true/false    - if/not mail sent
        */
        public function sendPasswordResetEmailVerificationMail(
            $firstName,
            $emailAddress,
            $verificationCode
        ) {

            // Set email subject
            $this->phpMailer->Subject = 'Password Reset Email Verification';

            // Attach file password_reset_email_verification.png, and later link to it using identfier
            // passwordResetEmailVerificationImg
            $this->phpMailer->AddEmbeddedImage(
                'images/php_mailer_images/password_reset_email_verification.png',
                'passwordResetEmailVerificationImg',
                'images/php_mailer_images/password_reset_email_verification.png'
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
                                    <h1><b>' . $this->keys->companyName . '</b></h1>
                                    <br>
                                    <img src="cid:passwordResetEmailVerificationImg" alt = "loading..."/>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <div id = "message">
                                        <h2> Hello <b>' . $firstName .'</b>,</h2>
                                        <h2> To reset your account password, kindly verify your email address. Your email verification code is:
                                        </h2>
                                        <h1><u>' . $verificationCode . '</u></h1>
                                        <h2> Enter the code on our website or android app when queried to verify your email address and reset your password. <b>This code will expire after '
                                        . $this->keys->verificationCodeExpiryTime . ' hour.</b>
                                        </h2>
                                        <h2> If you did not request a password reset please ignore this email or reply to let us know.</h2>
                                        <h2>
                                            <b>P.S.</b> We would also love hearing from you and helping you with any issues or complaints you migh have. Please reply to this email if you have any questions.
                                        </h2>
                                        <h3>We cannot wait to have you back! <br>Kind Regards,<br><b> ' . $this->keys->companyName . ' Team.</b>
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
            We cannot wait to have you back! Kind Regards, ' . $this->keys->companyName . ' Team.';

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
        * @return boolean       - true/false - if/not mail sent
        */
        public function sendPasswordChangeNotificationMail($firstName, $emailAddress)
        {

            // Set email subject
            $this->phpMailer->Subject = 'Account Password Change';

            // Attach file client_email_verification.png, and later link to it using identfier emailVerificationImg
            // $this->phpMailer->AddEmbeddedImage('images/php_mailer_images/client_email_verification.png',
            //                                      'emailVerificationImg',
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
                                    <h1><b>' . $this->keys->companyName . '</b></h1>
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
                                        <h3> Kind Regards,<b> ' . $this->keys->companyName
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
            Kind Regards, ' . $this->keys->companyName . ' Team';

            $this->phpMailer->addAddress($emailAddress, $firstName); // Add a recipient
            // $this->phpMailer->addAddress('ellen@example.com'); // Name is optional

            // Attachments
            // $this->phpMailer->addAttachment('/tmp/static_images/client_email_verification.png',
            //                                  'client_email_verification.png');    // Optional name
            // $this->phpMailer->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            // $this->phpMailer->addAttachment('/var/tmp/file.tar.gz');         // Add attachments

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
        * @param clientId   - Clients id
        * @return boolean   - true/false - if/not revoked
        */
        public function revokeEmailVerification($clientId)
        {

            $emailVerified = "false";
            $stmt = $this->connectToDB->prepare(
                "UPDATE {$this->keys->tableClients} SET {$this->keys->fieldEmailVerified} = ?
                 WHERE {$this->keys->tableClients}.{$this->keys->fieldClientId} = ?"
            );
            $stmt->bind_param("ss", $emailVerified, $clientId);
            $execute = $stmt->execute(); // Execute statement
            $stmt->close(); // Close statement

            // Check for query execution
            if ($execute) {
                // Query executed

                // Check if EmailVerified value is false
                $stmt = $this->connectToDB->prepare(
                    "SELECT * FROM {$this->keys->tableClients} AS {$this->keys->keyClient}
                     WHERE {$this->keys->keyClient}.{$this->keys->fieldClientId} = ?
                     AND {$this->keys->keyClient}.{$this->keys->fieldEmailVerified} = ?"
                );
                $stmt->bind_param("ss", $clientId, $emailVerified); // Bind parameters
                $stmt->execute(); // Execute statement
                $stmt->store_result();

                // Check if record was found
                if ($stmt->num_rows > 0) {
                    // Record found thus email verification revoked

                    return true; // Return true

                } else {
                    // Record not found thus email verification not revoked

                    return false; // Return false
                }
            } else {
                // Query execution failed

                return null; // Return null
            }
        }

    }
    ?>
