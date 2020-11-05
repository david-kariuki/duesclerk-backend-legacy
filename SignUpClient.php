<?php
// Signup client

// Enable error reporting
error_reporting(1);

// Call required classes
require_once 'classes/FieldKeys.php';
require_once 'classes/ClientAccountFunctions.php';

// Create Classes Objects
$clientAccountFunctions = new ClientAccountFunctions();
$fieldKeys            = new FieldKeys();

// Create Json response array and initialize error to FALSE
$response       = array($fieldKeys->keyError => false);
$signUpDetails  = array(

    $fieldKeys->keyFirstName => "",
    $fieldKeys->keyLastName => "",
    $fieldKeys->keyGender => "",
    $fieldKeys->keyBusinessName => "",
    $fieldKeys->keyCityName => "",
    $fieldKeys->keyPhoneNumber => "",
    $fieldKeys->keyEmailAddress => "",
    $fieldKeys->keyCountryCode => "",
    $fieldKeys->keyCountryAlpha2 => "",
    $fieldKeys->keyPassword => "",
    $fieldKeys->keyAccountType => ""
);


// Check for set POST params
if (
    isset($_POST[$fieldKeys->keyFirstName])     ||
    isset($_POST[$fieldKeys->keyLastName])      ||
    isset($_POST[$fieldKeys->keyGender])        ||
    isset($_POST[$fieldKeys->keyBusinessName])  ||
    isset($_POST[$fieldKeys->keyCityName])      ||
    isset($_POST[$fieldKeys->keyPhoneNumber])   ||
    isset($_POST[$fieldKeys->keyEmailAddress])  ||
    isset($_POST[$fieldKeys->keyCountryCode])   ||
    isset($_POST[$fieldKeys->keyCountryAlpha2]) ||
    isset($_POST[$fieldKeys->keyPassword])      ||
    isset($_POST[$fieldKeys->keyAccountType])
) {

    // Get Vales From POST
    $firstName      = $_POST[$fieldKeys->keyFirstName]      ? $_POST[$fieldKeys->keyFirstName]      : '';
    $lastName       = $_POST[$fieldKeys->keyLastName]       ? $_POST[$fieldKeys->keyLastName]       : '';
    $phoneNumber    = $_POST[$fieldKeys->keyPhoneNumber]    ? $_POST[$fieldKeys->keyPhoneNumber]    : '';
    $emailAddress   = $_POST[$fieldKeys->keyEmailAddress]   ? $_POST[$fieldKeys->keyEmailAddress]   : '';
    $countryCode    = $_POST[$fieldKeys->keyCountryCode]    ? $_POST[$fieldKeys->keyCountryCode]    : '';
    $countryAlpha2  = $_POST[$fieldKeys->keyCountryAlpha2]  ? $_POST[$fieldKeys->keyCountryAlpha2]  : '';
    $password       = $_POST[$fieldKeys->keyPassword]       ? $_POST[$fieldKeys->keyPassword]       : '';
    $gender         = $_POST[$fieldKeys->keyGender]         ? $_POST[$fieldKeys->keyGender]         : '';
    $businessName   = $_POST[$fieldKeys->keyBusinessName]   ? $_POST[$fieldKeys->keyBusinessName]   : '';
    $cityName       = $_POST[$fieldKeys->keyCityName]       ? $_POST[$fieldKeys->keyCityName]       : '';
    $accountType    = $_POST[$fieldKeys->keyAccountType]    ? $_POST[$fieldKeys->keyAccountType]    : '';


    // Check If A Client With The Same PhoneNumber Exists
    if ($clientAccountFunctions->isPhoneNumberInClientsTable($phoneNumber)) {
        // Phone Number Exists

        // Set response error to true
        $response[$fieldKeys->keyError]         = true;
        $response[$fieldKeys->keySignUp]        = $fieldKeys->keyPhoneNumber;
        $response[$fieldKeys->keyErrorMessage]  = "An account with that phone number already exists!";

        // Encode and echo Json response
        echo json_encode($response);

        /**
        * Check Email Address Validity
        * Check The Maximum Allowed Length Of The Email Address (total length in RFC_3696 is 320 characters)
        * The local part of the email address—your username—must not exceed 64 characters.
        * The domain name is limited to 255 characters.
        */
    } else if ((!filter_var($emailAddress, FILTER_VALIDATE_EMAIL))
    || (strlen($emailAddress) > $fieldKeys->emailMaxLength)) {
        // Invalid Email

        // Set response error to true
        $response[$fieldKeys->keyError]         = true;
        $response[$fieldKeys->keySignUp]        = $fieldKeys->keyEmailAddress;
        $response[$fieldKeys->keyErrorMessage]  = "The email address you entered is invalid!";

        // Encode and echo Json response
        echo json_encode($response);


    } else if ($clientAccountFunctions->isEmailAddressInClientsTable($emailAddress)) {
        // Email Address Exists

        // Set response error to true
        $response[$fieldKeys->keyError]         = true;
        $response[$fieldKeys->keySignUp]                      = $fieldKeys->keyEmailAddress;
        $response[$fieldKeys->keyErrorMessage]  = "An account with that email address already exists!";

        // Encode and echo Json response
        echo json_encode($response);

        // Check Password Length
    } else if (strlen($password) < $fieldKeys->passwordMinLength) {
        // Password Too Short

        // Set response error to true
        $response[$fieldKeys->keyError]         = true;
        $response[$fieldKeys->keySignUp]        = $fieldKeys->keyPassword;
        $response[$fieldKeys->keyErrorMessage]  = 'Passwords should be 8 characters or longer!';

        // Return Respons
        echo json_encode($response);

    } else {

        // Check if account type is personal so as to validate names expressions
        if ($accountType == $fieldKeys->keyAccountTypePersonal) {

            // Check if first name is alphabetical
            if (!preg_match($fieldKeys->namesExpressionPregMatch, $firstName)) {
                // Invalid first name

                // Set response error to true
                $response[$fieldKeys->keyError]         = true;
                $response[$fieldKeys->keyErrorMessage]  = 'The first name you entered does not appear to be valid!';

                // Encode and echo Json response
                echo json_encode($response);

                // Check If last name is alphabetical
            } else if (!preg_match($fieldKeys->namesExpressionPregMatch, $lastName)) {
                // Invalid last name

                // Set response error to true
                $response[$fieldKeys->keyError]         = true;
                $response[$fieldKeys->keyErrorMessage]  = 'The last name you entered does not appear to be valid!';

                // Encode and echo Json response
                echo json_encode($response);

                // Check First Name Length
            } else if (strlen($firstName) < $fieldKeys->fnameMinLength) {
                // Firstname too Short

                // Set response error to true
                $response[$fieldKeys->keyError]         = true;
                $response[$fieldKeys->keySignUp]        = $fieldKeys->keyFirstName;
                $response[$fieldKeys->keyErrorMessage]  = 'The first name you entered is too short!';

                // Encode and echo Json response
                echo json_encode($response);

                // Check Last Name Length
            } else if (strlen($lastName) < $fieldKeys->lnameMinLength) {
                // Lastname Too Short

                // Set response error to true
                $response[$fieldKeys->keyError]         = true;
                $response[$fieldKeys->keySignUp]        = $fieldKeys->keyLastName;
                $response[$fieldKeys->keyErrorMessage]  = 'The last name you entered is too short!';

                // Encode and echo Json response
                echo json_encode($response);

            }

        }

        //Check for required data and add to signup details array
        if (isset($_POST[$fieldKeys->keyFirstName])) {
            $signUpDetails[$fieldKeys->keyFirstName]= $firstName;
        }

        if (isset($_POST[$fieldKeys->keyLastName])) {
            $signUpDetails[$fieldKeys->keyLastName] = $lastName;
        }

        if (isset($_POST[$fieldKeys->keyPhoneNumber])) {
            $signUpDetails[$fieldKeys->keyPhoneNumber] = $phoneNumber;
        }

        if (isset($_POST[$fieldKeys->keyEmailAddress])) {
            $signUpDetails[$fieldKeys->keyEmailAddress] = $emailAddress;
        }

        if (isset($_POST[$fieldKeys->keyCountryCode])) {
            $signUpDetails[$fieldKeys->keyCountryCode] = $countryCode;
        }

        if (isset($_POST[$fieldKeys->keyCountryAlpha2])) {
            $signUpDetails[$fieldKeys->keyCountryAlpha2] = $countryAlpha2;
        }
        if (isset($_POST[$fieldKeys->keyPassword])) {
            $signUpDetails[$fieldKeys->keyPassword] = $password;
        }

        if (isset($_POST[$fieldKeys->keyGender])) {
            $signUpDetails[$fieldKeys->keyGender] = $gender;
        }

        if (isset($_POST[$fieldKeys->keyBusinessName])) {
            $signUpDetails[$fieldKeys->keyBusinessName] = $businessName;
        }

        if (isset($_POST[$fieldKeys->keyCityName])) {
            $signUpDetails[$fieldKeys->keyCityName] = $cityName;
        }

        if (isset($_POST[$fieldKeys->keyAccountType])) {
            $signUpDetails[$fieldKeys->keyAccountType] = $accountType;
        }


        // Signup user
        $signupClient = $clientAccountFunctions->signUpClient($signUpDetails);

        // Check If Client Was Signed Up
        if ($signupClient) {
            // Client Signed Up

            // Add Client Details Json Response Array
            $response[$fieldKeys->keySignUp][$fieldKeys->keyClientId]       = $signupClient[$fieldKeys->keyClientId];
            $response[$fieldKeys->keySignUp][$fieldKeys->keyEmailAddress]
            = $signupClient[$fieldKeys->keyEmailAddress];
            $response[$fieldKeys->keySignUp][$fieldKeys->keyPassword]       = $password;

            // Add success message
            if ($accountType == $fieldKeys->keyAccountTypePersonal) {
                $response[$fieldKeys->keySuccessMessage]
                = "Welcome to " . $fieldKeys->companyName . ", " . $firstName . " " . $lastName . ".";

            } else if ($accountType == $fieldKeys->keyAccountTypeBusiness) {
                $response[$fieldKeys->keySuccessMessage]
                = "Welcome to " . $fieldKeys->companyName . ", " . $businessName . ".";
            }


            // Encode and echo Json response
            echo json_encode($response);

        } else {
            // Signup Failed

            // Set response error to true
            $response[$fieldKeys->keyError]         = true;
            $response[$fieldKeys->keyErrorMessage]  = "Something went terribly wrong!";

            // Encode and echo Json response
            echo json_encode($response);
        }
    }
} else {
    // Missing params

    // Set response error to true
    $response[$fieldKeys->keyError]        = true;
    $response[$fieldKeys->keyErrorMessage] = "Something went terribly wrong!";

    // Encode and echo json response
    echo json_encode($response);
}

?>
