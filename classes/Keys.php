<?php

/**
* Keys class
* This class contains all the constants required by all project files
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

// Disable Error Reporting
error_reporting(1);

// Website and domain details
define("PROTOCOL",                          "https://");
define("SUB_DOMAIN",                        "www.");
define("ROOT_DOMAIN",                       "duesclerk.com");
define("ROOT_DOMAIN_WITH_SUB_DOMAIN",       SUB_DOMAIN . ROOT_DOMAIN);
define("WEBSITE_URL",                       PROTOCOL . ROOT_DOMAIN_WITH_SUB_DOMAIN);
define("COMPANY_NAME",                      "DuesClerk");


// Database fields keys
define("FIELD_USER_ID",                   "UserId");
define("FIELD_FIRST_NAME",                  "FirstName");
define("FIELD_LAST_NAME",                   "LastName");
define("FIELD_EMAIL_ADDRESS",               "EmailAddress");
define("FIELD_COUNTRY_ID",                  "CountryId");
define("FIELD_COUNTRY_NAME",                "CountryName");
define("FIELD_COUNTRY_CODE",                "CountryCode");
define("FIELD_COUNTRY_ALPHA2",              "CountryAlpha2");
define("FIELD_COUNTRY_ALPHA3",              "CountryAlpha3");
define("FIELD_COUNTRY_FLAG",                "CountryFlag");
define("FIELD_PASSWORD",                    "Password");
define("FIELD_NEW_PASSWORD",                "NewPassword");
define("FIELD_HASH",                        "Hash");
define("FIELD_BUSINESS_NAME",               "BusinessName");
define("FIELD_ACCOUNT_TYPE",                "AccountType");
define("FIELD_NEW_ACCOUNT_TYPE",            "NewAccountType");
define("FIELD_SIGN_UP_DATE_TIME",           "SignUpDateTime");
define("FIELD_EMAIL_VERIFIED",              "EmailVerified");
define("FIELD_VERIFICATION_CODE",           "VerificationCode");
define("FIELD_UPDATE_DATE_TIME",            "UpdateDateTime");
define("FIELD_CODE_REQUEST_TIME",           "CodeRequestTime");


// Table Names
define("TABLE_USERS",                     "Users");
define("TABLE_EMAIL_VERIFICATION",          "EmailVerification");
define("TABLE_USER_LOGS",                 "UserLogs");
define("TABLE_COUNTRIES",                   "Countries");

// Country keys
define("KEY_COUNTRY",                       "Country");
define("KEY_COUNTRY_DATA",                  "CountryData");

// Account type keys
define("KEY_ACCOUNT_TYPE_PERSONAL",         "AccountTypePersonal");
define("KEY_ACCOUNT_TYPE_BUSINESS",         "AccountTypeBusiness");

// SignUp and SignIn Keys
define("KEY_SIGN_UP",                       "SignUp");
define("KEY_SIGN_IN",                       "SignIn");


// Error and success message keys
define("KEY_ERROR",                         "Error");
define("KEY_ERROR_MESSAGE",                 "ErrorMessage");
define("KEY_SUCCESS_MESSAGE",               "SuccessMessage");


// User profile keys
define("KEY_USER",                          "User");
define("KEY_UPDATE_PROFILE",                "UpdateProfile");

// Password reset
define("KEY_PASSWORD_RESET",                "PasswordReset");


// Email verification keys
define("FIELD_VERIFICATION_TYPE",               "VerificationType");
define("KEY_VERIFICATION_TYPE_EMAIL_ACCOUNT",   "VerificationEmailAccount");
define("KEY_VERIFICATION_TYPE_PASSWORD_RESET",  "VerificationPasswordReset");
define("KEY_VERIFICATION_CODE_EXPIRY_TIME",     1); // 1 hour
define("KEY_EMAIL_VERIFICATION",                "EmailVerification");
define("KEY_SEND_VERIFICATION_CODE",            "SendVerificationCode");


// Expressions (preg match)
define("EXPRESSION_NAMES",                  "/^[A-Za-z .'-]+$/");


// Field lengths
define("LENGTH_MIN_SINGLE_NAME",            1);
define("LENGTH_MIN_PASSWORD",               8);
define("LENGTH_MAX_EMAIL_ADDRESS",          320);
define("LENGTH_TABLE_IDS",                  15);
define("LENGTH_VERIFICATION_CODE",          6);


// Logs keys
define("LOG_TYPE_SIGN_UP",                  "LogTypeSignUp");
define("LOG_TYPE_SIGN_IN",                  "LogTypeSignIn");
define("LOG_TYPE_SIGN_OUT",                 "LogTypeSignOut");
define("LOG_TYPE_UPDATE_PROFILE",           "LogTypeUpdateProfile");
define("LOG_TYPE_UPDATE_PASSWORD",          "LogTypeUpdatePassword");

// Date formats
define("FORMAT_DATE_TIME_FULL",             "l d, F Y H:i:s");
define("FORMAT_DATE_TIME_NUMERICAL",        "m/d/Y h:i:s a");

// File types
define("FILE_TYPE_PNG",                     ".png");


class Keys
{

    /**
    * Class constructor
    */
    function __construct()
    {

    }


    /**
    * Class destructor
    */
    function __destruct()
    {

    }


    /**
    * Function to return constant value within SQL statements
    *
    * @param constant - Constants value
    */
    public function valueOfConst($constant)
    {

        if (!empty($constant)) {
            // Constant not empty

            return $constant; // Return constant

        } else {
            // Constant empty

            // Throw exception
            throw new Exception(
                'Method '.__METHOD__.' failed : The required constant is null or undefined'
            );
        }
    }
}

// EOF: Keys.php
