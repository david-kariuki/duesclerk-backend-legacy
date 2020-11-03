<?php

//DataKeys Class

// Disable Error Reporting
error_reporting(1);

class FieldKeys {

    // Website Details
    private $protocol;
    private $domainFull;
    public $domainNoWW;
    public $websiteUrl;
    public $siteName;

    // Website Folders
    public $androidFolderUrl;
    public $androidImagesFolderUrl;
    public $sharedResourcesUrl;
    public $sharedImagesFolderUrl;
    public $profilePicturesUrl;

    // Input Fields Json Keys
    public $keyUserId;
    public $keyFirstName;
    public $keyLastName;
    public $keyUsername;
    public $keyEmailAddress;
    public $keyPassword;
    public $keyCountryName;
    public $keyCountryCode;
    public $keyCountryAlpha2;
    public $keyCountryAlpha3;
    public $keyCountryFlag;
    public $keyPhoneNumber;
    public $keyDateOfBirth;
    public $keyGender;
    public $keySignupDate;
    public $keyUpdateDate;
    public $keyAccountStatus;
    public $keyEmailVerification;
    public $keyVerificationCode;
    public $keyProfilePictureName;
    public $keyProfilePictureDate;
    public $keyProfilePictureFileType;
    public $keyProfilePictureUrl;

    // User Profile
    public $keyUser;
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
    public $keyTableUsers;
    public $keyTableEmailVerification;
    public $keyTableUserLogs;
    public $keyTableCountries;
    public $keyTableProfilePictures;
    public $keyTableSessions;
    public $tableIdsLength;


    // User Acoount Actions
    public $logTypeSignup;
    public $logTypeSignin;
    public $logTypeSignout;


    // Mail Config
    public $mailHost;
    public $SMTPSecure;
    public $mailPort;


    // Email Verification
    public $tagUserAccountEmailVerf;
		public $tagPasswordResetEmailVerf;
    public $verificationCodeExpiryTime;
    public $verificationCodeLength;


  function __construct(){

    // Website Details
    $this->protocol                               = "https://";
    $this->domainFull                             = "www.duesclerk.com/";
    $this->domainNoWW                             = "duesclerk.com";
    $this->websiteUrl                             = $this->protocol . $this->domainFull;
    $this->siteName                               = "DuesClerk";


    // Website Folders
    $this->androidFolderUrl                       = $this->websiteUrl . "android/";
    $this->androidImagesFolderUrl                 = $this->androidFolderUrl . "images/";
    $this->sharedResourcesUrl                     = $this->websiteUrl . "shared_res/";
    $this->sharedImagesFolderUrl                  = $this->sharedResourcesUrl . "shared_images/";
    $this->profilePicturesUrl                     = $this->sharedImagesFolderUrl . "profile_pictures/";


    // Input Fields Json Keys
    $this->keyUserId                              = "UserId";
    $this->keyFirstName                           = "FirstName";
    $this->keyLastName                            = "LastName";
    $this->keyUsername                            = "Username";
    $this->keyEmailAddress                        = "EmailAddress";
    $this->keyPassword                            = "Password";
    $this->keyCountryName                         = "CountryName";
    $this->keyCountryCode                         = "CountryCode";
    $this->keyCountryAlpha2                       = "CountryAlpha2";
    $this->keyCountryAlpha3                       = "CountryAlpha3";
    $this->keyCountryFlag                         = "CountryFlag";
    $this->keyPhoneNumber                         = "PhoneNumber";
    $this->keyDateOfBirth                         = "DateOfBirth";
    $this->keyGender                              = "Gender";
    $this->keySignupDate                          = "SignupDate";
    $this->keyUpdateDate                          = "UpdateDate";
    $this->keyAccountStatus                       = "AccountStatus";
    $this->keyEmailVerification                   = "EmailVerification";
    $this->keyVerificationCode                    = "VerificationCode";
    $this->keyProfilePictureName                  = "ProfilePictureName";
    $this->keyProfilePictureDate                  = "ProfilePictureDate";
    $this->keyProfilePictureFileType              = "ProfilePictureFileType";
    $this->keyProfilePictureUrl                   = "ProfilePictureUrl";


    // Shared JSON keys
    $this->keyError                               = "error";
    $this->keyErrorMessage                        = "error_message";
    $this->keySuccessMessage                      = "success_message";
    $this->keyStatus                              = "status";
    $this->keyRequestTime                         = "requestTime";
    $this->namesExpressionPregMatch               = "/^[A-Za-z .'-]+$/";
    $this->fnameMinLength                         = 1;
    $this->lnameMinLength                         = 1;
    $this->passwordMinLength                      = 8;
    $this->emailMaxLength                         = 320;


    // User Profile
    $this->keyUser                                = "user";
    $this->defaultProfilePicture                  = "defaultProfilePicture.png";


    $this->keyCountriesData                       = "CountriesData";


    // Initializing Table Names
		$this->keyTableUsers							            = "Users";
		$this->keyTableEmailVerification	            = "EmailVerification";
		$this->keyTableUserLogs						            = "UserLogs";
    $this->keyTableCountries                      = "Countries";
    $this->keyTableProfilePictures                = "ProfilePictures";
    $this->keyTableSessions                       = "UserSessions";
    $this->tableIdsLength                         = 15;


    $this->logTypeSignup                         = "logTypeSignup";
    $this->logTypeSignin                         = "logTypeSignin";
    $this->logTypeSignout                        = "logTypeSignout";


    // Mail Config
    $this->mailHost                               = "mail." . $this->domainNoWW . ";" . "webdisk." . $this->domainNoWW;  // Specify main and backup SMTP servers
    $this->SMTPSecure                             = 'tls';
    $this->mailPort                               = 25;


    // Email Verification
    $this->tagUserAccountEmailVerf		            = "UserAccountEmailVerification";
		$this->tagPasswordResetEmailVerf              = "PasswordResetEmailVerification";
    $this->verificationCodeExpiryTime             = 1;
    $this->verificationCodeLength                 = 6;

  }

  function __destruct(){}
}
?>
