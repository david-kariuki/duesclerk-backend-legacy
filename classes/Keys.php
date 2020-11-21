<?php

//    Copyright (c) 2020 by David Kariuki (dk).
//    All Rights Reserved.


// Keys Class

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
    define("FIELD_CLIENT_ID",                   "ClientId");
    define("FIELD_FIRST_NAME",                  "FirstName");
    define("FIELD_LAST_NAME",                   "LastName");
    define("FIELD_PHONE_NUMBER",                "PhoneNumber");
    define("FIELD_EMAIL_ADDRESS",               "EmailAddress");
    define("FIELD_COUNTRY_ID",                  "CountryId");
    define("FIELD_COUNTRY_NAME",                "CountryName");
    define("FIELD_COUNTRY_CODE",                "CountryCode");
    define("FIELD_COUNTRY_ALPHA2",              "CountryAlpha2");
    define("FIELD_COUNTRY_ALPHA3",              "CountryAlpha3");
    define("FIELD_COUNTRY_FLAG",                "CountryFlag");
    define("FIELD_PASSWORD",                    "Password");
    define("FIELD_HASH",                        "Hash");
    define("FIELD_GENDER",                      "Gender");
    define("FIELD_BUSINESS_NAME",               "BusinessName");
    define("FIELD_CITY_NAME",                   "CityName");
    define("FIELD_ACCOUNT_TYPE",                "AccountType");
    define("FIELD_SIGN_UP_DATE_TIME",           "SignUpDateTime");
    define("FIELD_EMAIL_VERIFIED",              "EmailVerified");
    define("FIELD_VERIFICATION_CODE",           "VerificationCode");
    define("FIELD_UPDATE_DATE_TIME",            "UpdateDateTime");
    define("FIELD_REQUEST_NAME",                "RequestTime");


    // Table Names
    define("TABLE_CLIENTS",                     "Clients");
    define("TABLE_EMAIL_VERIFICATION",          "EmailVerification");
    define("TABLE_CLIENT_LOGS",                 "ClientLogs");
    define("TABLE_COUNTRIES",                   "Countries");

    // Country keys
    define("KEY_COUNTRY",                       "Country");
    define("KEY_COUNTRY_DATA",                  "CountryData");

    // Account types keys
    define("KEY_ACCOUNT_TYPE_PERSONAL",         "AccountTypePersonal");
    define("KEY_ACCOUNT_TYPE_BUSINESS",         "AccountTypeBusiness");

    // SignUp and SignIn Keys
    define("KEY_SIGN_UP",                       "SignUp");
    define("KEY_SIGN_IN",                       "SignIn");


    // Error and success message keys
    define("KEY_ERROR",                         "Error");
    define("KEY_ERROR_MESSAGE",                 "ErrorMessage");
    define("KEY_SUCCESSS_MESSAGE",              "SuccessMessage");


    // Client profile keys
    define("KEY_CLIENT",                        "Client");
    define("KEY_UPDATE_PROFILE",                "UpdateProfile");


    // Email verification keys
    define("KEY_ACCOUNT_EMAIL_VERIFICATION",    "AccountEmailVerification");
    define("KEY_PASSWORD_RESET_EMAIL_VERIFICATION", "PasswordResetEmailVerification");
    define("KEY_VERIFICATION_CODE_EXPIRY_TIME",  1);


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
    define("LOG_TYPE_UPDATE",                   "LogTypeUpdate");

    // Date formats
    define("FORMAT_DATE_TIME_FULL",             "l d, F Y H:i:s");


    ?>
