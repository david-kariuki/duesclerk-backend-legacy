<?php

//    Copyright (c) 2020 by David Kariuki (dk).
//    All Rights Reserved.


// FieldKeys Class

// Disable Error Reporting
error_reporting(1);

class FieldKeys {

    // Website Details
    private $protocol;
    private $domainFull;
    public $domainNoWW;
    public $websiteUrl;
    public $companyName;

    // Website Folders
    public $androidFolderUrl;
    public $androidImagesFolderUrl;
    public $sharedResourcesUrl;
    public $sharedImagesFolderUrl;
    public $profilePicturesUrl;

    // Input Fields Json Keys
    public $keyClientId;
    public $keyFirstName;
    public $keyLastName;
    public $keyPhoneNumber;
    public $keyEmailAddress;
    public $keyCountryName;
    public $keyCountryCode;
    public $keyCountryAlpha2;
    public $keyCountryAlpha3;
    public $keyCountryFlag;
    public $keyPassword;
    public $keyHash;
    public $keyGender;
    public $keyBusinessName;
    public $keyCityName;
    public $keyAccountType;
    public $keyAccountTypePersonal;
    public $keyAccountTypeBusiness;
    public $keyEmailVerified;
    public $keyVerificationCode;
    public $keySignUpDateTime;
    public $keySignUp;
    public $keySignIn;

    public $keyProfilePictureName;
    public $keyProfilePictureDate;
    public $keyProfilePictureFileType;
    public $keyProfilePictureUrl;

    // User Profile
    public $keyClient;
    public $keyUpdateProfile;
    public $defaultProfilePicture;

    // Shared JSON Response Keys
    public $keyError;
    public $keyErrorMessage;
    public $keySuccessMessage;
    public $keyStatus;
    public $keyRequestTime;
    public $namesExpressionPregMatch;
    public $fnameMinLength;
    public $lnameMinLength;
    public $passwordMinLength;
    public $emailMaxLength;

    public $keyCountriesData;


    // Table Names
    public $keyTableClients;
    public $keyTableEmailVerification;
    public $keyTableUserLogs;
    public $keyTableCountries;
    public $keyTableProfilePictures;
    public $keyTableSessions;
    public $tableIdsLength;


    // User Acoount Actions
    public $logTypeSignUp;
    public $logTypeSignIn;
    public $logTypeSignOut;


    // Mail Config
    public $mailHost;
    public $SMTPSecure;
    public $mailPort;


    // Email Verification
    public $tagUserAccountEmailVerf;
    public $tagPasswordResetEmailVerf;
    public $verificationCodeExpiryTime;
    public $verificationCodeLength;


    function __construct() {

        // Website Details
        $this->protocol                     = "https://";
        $this->domainFull                   = "www.duesclerk.com/";
        $this->domainNoWW                   = "duesclerk.com";
        $this->websiteUrl                   = $this->protocol . $this->domainFull;
        $this->companyName                  = "DuesClerk";


        // Website Folders
        $this->androidFolderUrl             = $this->websiteUrl . "andr/";
        $this->androidImagesFolderUrl       = $this->androidFolderUrl . "images/";
        $this->sharedResourcesUrl           = $this->websiteUrl . "shared_res/";
        $this->sharedImagesFolderUrl        = $this->sharedResourcesUrl . "shared_images/";
        $this->profilePicturesUrl           = $this->sharedImagesFolderUrl . "profile_pictures/";


        // Client/User Fields Json Keys
        $this->keyClientId                  = "ClientId";
        $this->keyFirstName                 = "FirstName";
        $this->keyLastName                  = "LastName";
        $this->keyPhoneNumber               = "PhoneNumber";
        $this->keyEmailAddress              = "EmailAddress";
        $this->keyCountryName               = "CountryName";
        $this->keyCountryCode               = "CountryCode";
        $this->keyCountryAlpha2             = "CountryAlpha2";
        $this->keyCountryAlpha3             = "CountryAlpha3";
        $this->keyCountryFlag               = "CountryFlag";
        $this->keyPassword                  = "Password";
        $this->keyHash                      = "Hash";
        $this->keyGender                    = "Gender";
        $this->keyBusinessName              = "BusinessName";
        $this->keyCityName                  = "CityName";
        $this->keyAccountType               = "AccountType";
        $this->keyAccountTypePersonal       = "AccountTypePersonal";
        $this->keyAccountTypeBusiness       = "AccountTypeBusiness";
        $this->keyEmailVerified             = "EmailVerified";
        $this->keyVerificationCode          = "VerificationCode";
        $this->keySignUpDateTime            = "SignUpDateTime";
        $this->keySignUp                    = "SignUp";
        $this->keySignIn                    = "SignIn";

        $this->keyUpdateDate                = "UpdateDate";
        $this->keyProfilePictureName        = "ProfilePictureName";
        $this->keyProfilePictureDate        = "ProfilePictureDate";
        $this->keyProfilePictureFileType    = "ProfilePictureFileType";
        $this->keyProfilePictureUrl         = "ProfilePictureUrl";


        // SignUp and SignIn Keys
        $this->keySignUpDetails             = "SignUpDetails";
        $this->keySignInDetails             = "SignInDetails";


        // Shared JSON keys
        $this->keyError                     = "Error";
        $this->keyErrorMessage              = "ErrorMessage";
        $this->keySuccessMessage            = "SuccessMessage";
        $this->keyStatus                    = "Status";
        $this->keyRequestTime               = "RequestTime";
        $this->namesExpressionPregMatch     = "/^[A-Za-z .'-]+$/";
        $this->fnameMinLength               = 1;
        $this->lnameMinLength               = 1;
        $this->passwordMinLength            = 8;
        $this->emailMaxLength               = 320;


        // User Profile
        $this->keyClient                    = "Client";
        $this->keyUpdateProfile             = "UpdateProfile";
        $this->defaultProfilePicture        = "defaultProfilePicture.png";


        $this->keyCountriesData             = "CountriesData";


        // Table Names
        $this->keyTableClients				= "Clients";
        $this->keyTableEmailVerification	= "EmailVerification";
        $this->keyTableClientLogs			= "ClientLogs";
        $this->keyTableCountries            = "Countries";
        $this->keyTableProfilePictures      = "ProfilePictures";
        $this->keyTableSessions             = "UserSessions";
        $this->tableIdsLength               = 15;

        $this->logTypeSignUp                = "logTypeSignUp";
        $this->logTypeSignIn                = "logTypeSignIn";
        $this->logTypeSignOut               = "logTypeSignOut";


        // Mail Config Keys
        $this->mailHost                     = "mail." . $this->domainNoWW . ";" . "webdisk." . $this->domainNoWW;  // Specify main and backup SMTP servers
        $this->SMTPSecure                   = 'tls';
        $this->mailPort                     = 25;


        // Email Verification Keys
        $this->tagUserAccountEmailVerf      = "UserAccountEmailVerification";
        $this->tagPasswordResetEmailVerf    = "PasswordResetEmailVerification";
        $this->verificationCodeExpiryTime   = 1;
        $this->verificationCodeLength       = 6;

    }

    function __destruct() {}
    }

    ?>
