<?php
// Signup user

// Enable error reporting
error_reporting(1);

// Call required classes
require_once 'classes/FieldKeys.php';
require_once 'classes/UserAccountFunctions.php';

// Create Classes Objects
$userAccountFunctions = new UserAccountFunctions();
$fieldKeys            = new FieldKeys();

// Json Keys
$fileType        = 'image';
$namesExpressionPregMatch = $fieldKeys->namesExpressionPregMatch;

$fnameMinLength     = $fieldKeys->fnameMinLength;
$lnameMinLength     = $fieldKeys->lnameMinLength;
$passwordMinLength  = $fieldKeys->passwordMinLength;
$emailMaxLength     = $fieldKeys->emailMaxLength;
$target_dir         = "../shared_res/img/profile_pictures/";

// Create Json response array and initialize error to FALSE
$response = array($fieldKeys->keyError => false);

$supportedExtensions = array("jpeg","jpg","png");
$uploadFileSize = (1048576 * 2);


// Check for set POST params
 if (isset($_FILES[$fileType]['name']) || (isset($_POST[$fieldKeys->keyFirstName]) && isset($_POST[$fieldKeys->keyLastName]) && isset($_POST[$fieldKeys->keyUsername]) && isset($_POST[$fieldKeys->keyEmailAddress]) && isset($_POST[$fieldKeys->keyPassword]) &&
        isset($_POST[$fieldKeys->keyCountryAlpha2]) && isset($_POST[$fieldKeys->keyPhoneNumber]) && isset($_POST[$fieldKeys->keyDateOfBirth]) && isset($_POST[$fieldKeys->keyGender]))){

    // Get Vales From POST
    $firstName        = $_POST[$fieldKeys->keyFirstName]      ? $_POST[$fieldKeys->keyFirstName]     : '';
    $lastName 		    = $_POST[$fieldKeys->keyLastName]       ? $_POST[$fieldKeys->keyLastName]      : '';
    $username 		    = $_POST[$fieldKeys->keyUsername]       ? $_POST[$fieldKeys->keyUsername]      : '';
    $emailAddress 		= $_POST[$fieldKeys->keyEmailAddress]   ? $_POST[$fieldKeys->keyEmailAddress]  : '';
    $password 		    = $_POST[$fieldKeys->keyPassword]       ? $_POST[$fieldKeys->keyPassword]      : '';
    $countryAlpha2    = $_POST[$fieldKeys->keyCountryAlpha2]  ? $_POST[$fieldKeys->keyCountryAlpha2] : '';
    $phoneNumber      = $_POST[$fieldKeys->keyPhoneNumber]    ? $_POST[$fieldKeys->keyPhoneNumber]   : '';
    $dateOfBirth	    = $_POST[$fieldKeys->keyDateOfBirth]    ? $_POST[$fieldKeys->keyDateOfBirth]   : '';
    $gender 			    = $_POST[$fieldKeys->keyGender]         ? $_POST[$fieldKeys->keyGender]        : '';


    // Check if first name is alphabetical
    if (!preg_match($namesExpressionPregMatch, $firstName)){
      // Invalid first name

      // Set response error to true
      $response[$fieldKeys->keyError]          = true;
      $response[$fieldKeys->keyErrorMessage]   = 'The first name you entered does not appear to be valid!';

      // Encode and echo Json response
      echo json_encode($response);

      // Check If last name is alphabetical
    } else if (!preg_match($namesExpressionPregMatch, $lastName)){
      // Invalid Lastname

      // Set response error to true
      $response[$fieldKeys->keyError]          = true;
      $response[$fieldKeys->keyErrorMessage]   = 'The last name you entered does not appear to be valid!';

      // Encode and echo Json response
      echo json_encode($response);

      // Check First Name Length
    } else if (strlen($firstName) < $fnameMinLength){
      // Firstname Too Short

      // Set response error to true
      $response[$fieldKeys->keyError]          = true;
      $response["field"]                      = $fieldKeys->keyFirstName;
      $response[$fieldKeys->keyErrorMessage]   = 'The first name you entered is too short!';

      // Encode and echo Json response
      echo json_encode($response);

      // Check Last Name Length
    } else if (strlen($lastName) < $lnameMinLength){
      // Lastname Too Short

      // Set response error to true
      $response[$fieldKeys->keyError]          = true;
      $response["field"]                      = $fieldKeys->keyLastName;
      $response[$fieldKeys->keyErrorMessage]   = 'The last name you entered is too short!';

      // Encode and echo Json response
      echo json_encode($response);

    } else if ($userAccountFunctions->isUsernameInUsersTable($username)) {
        // Email Address Exists

        // Set response error to true
        $response[$fieldKeys->keyError]          = true;
        $response["field"]                      = $fieldKeys->keyUsername;
        $response[$fieldKeys->keyErrorMessage]  = "Sorry! That username is taken! Please try a different one.";

        // Encode and echo Json response
        echo json_encode($response);

      /**
      *Check Email Address Validity
      *Check The Maximum Allowed Length Of The Email Address (total length in RFC_3696 is 320 characters)
      *The local part of the email address—your username—must not exceed 64 characters.
      *The domain name is limited to 255 characters.
      */
    } else if ((!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) || (strlen($emailAddress) > $emailMaxLength)){
      // Invalid Email

      // Set response error to true
      $response[$fieldKeys->keyError]         = true;
      $response["field"]                      = $fieldKeys->keyEmailAddress;
      $response[$fieldKeys->keyErrorMessage]  = "The email address you entered is invalid!";

      // Encode and echo Json response
      echo json_encode($response);


    } else if ($userAccountFunctions->isEmailAddressInUsersTable($emailAddress)) {
  		// Email Address Exists

      // Set response error to true
  		$response[$fieldKeys->keyError]          = true;
      $response["field"]                      = $fieldKeys->keyEmailAddress;
  		$response[$fieldKeys->keyErrorMessage]  = "An account with that email address already exists! Please try a different one.";

      // Encode and echo Json response
      echo json_encode($response);

      // Check Password Length
    } else if (strlen($password) < $passwordMinLength) {
      // Password Too Short

      // Set response error to true
      $response[$fieldKeys->keyError]          = true;
      $response["field"]                      = $fieldKeys->keyPassword;
      $response[$fieldKeys->keyErrorMessage]   = 'Passwords should be 8 characters or longer!';

      // Return Respons
      echo json_encode($response);

      // Check If A User With The Same PhoneNumber Exists
  	} else if ($userAccountFunctions->isPhoneNumberInUsersTable($phoneNumber)) {
      // Phone Number Exists

      // Set response error to true
      $response[$fieldKeys->keyError]            = true;
      $response["field"]                      = $fieldKeys->keyPhoneNumber;
      $response[$fieldKeys->keyErrorMessage]    = "An account with that phone number already exists! Please try a different one.";

      // Encode and echo Json response
      echo json_encode($response);

    } else {

      // Signup user
      $signup = $userAccountFunctions->signupUser($firstName, $lastName, $username, $emailAddress, $password, $countryAlpha2, $phoneNumber, $dateOfBirth, $gender);

      // Check If User Was Signed Up
      if ($signup) {
          // User Signed Up

        // Check if profile picture was attached
        if (!isset($_FILES[$fileType]['name'])){

          // Set Response Error To False
          $response[$fieldKeys->keyError] = false;

        } else {
          // Upload profile picture

          // Get filename
          $filename = $_FILES[$fileType]["name"];

          // Concat path with baseName
          $target_file = $target_dir . basename($filename);

          // Get file type extension
          $imageExtension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

          // Check image size by ImageInfo parameter
          $check = getimagesize($_FILES[$fileType]["tmp_name"]);
          if ($check !== false) {

            //$response[$fieldKeys->keyError] = true;
            //$response[$fieldKeys->keyErrorMessage] = "File is an image - " . $check["mime"] . ".";
          } else {

              // Set response error to true
              $response[$fieldKeys->keyError]        = true;
              $response[$fieldKeys->keyErrorMessage] = "The file you selected is not an image!";

              // Encode and echo json response
              echo json_encode($response);
          }

          // Check file size
          // MAX is 2MB
          if ($_FILES[$fileType]["size"] > $uploadFileSize) {

            // Set response error to true
            $response[$fieldKeys->keyError] = true;
            $response[$fieldKeys->keyErrorMessage] =  "The profile picture you selected is too large, maximum size allowed is " . round(abs($uploadFileSize / 1000000)) . "mb!";

            // Encode and echo json response
            echo json_encode($response);

            // Allow specific file formats
          } else if(in_array($imageExtension, $extensions) === false){

            // Set response error to true
            $response[$fieldKeys->keyError] = true;
            $response[$fieldKeys->keyErrorMessage] = "Sorry, only JPG, JPEG, PNG & GIF files are supported.";

            // Encode and echo json response
            echo json_encode($response);

          } else {
            // Profile picture is OK

            // Create File Name
            $uniqueName = $userAccountFunctions->generateUniqueId("pp", $fieldKeys->keyTableProfilePictures, "ProfilePictureName");

            $profilePictureName  = $uniqueName . "." . $imageExtension;
            $pathFileName = strtolower($target_dir . $profilePictureName);

            try {

              // Uploaded image and check response
              if (move_uploaded_file($_FILES[$fileType]["tmp_name"], $pathFileName)) {
                // Profile Picture Uploaded

                // Get UserId and SignupDate
                $userId     = $signup[$fieldKeys->keyUserId];
                $signupDate = $signup[$fieldKeys->keySignupDate];

                // Update Profile Picture Path In Database
                $update = $userAccountFunctions->updateProfilePicture($userId, $uniqueName, $signupDate, $imageExtension);

                if (false !== $update){
                    // Profile Path Update In Database

                    // Set Response Error To False
                    $response[$fieldKeys->keyError] = false;

                    // Add profile picture Json Response Array
                    $response[$fieldKeys->keyUser][$fieldKeys->keyProfilePictureName] = $update[$fieldKeys->keyProfilePictureName] . "." . $update[$fieldKeys->keyProfilePictureFileType];

                } else {
                  // Database Not Updated

                  // Delete Uploaded Profile Picture
                  unlink($pathFileName);

                  // Set response error to true
                  $response[$fieldKeys->keyError]        = true;
                  $response[$fieldKeys->keyErrorMessage] = "Profile picture update failed!";

                  // Encode and echo json response
                  echo json_encode($response);
                }
              } else {
                // Upload Failed

                // Set response error to true
                $response[$fieldKeys->keyError]        = true;
                $response[$fieldKeys->keyErrorMessage] = "Profile picture upload failed!";

                // Encode and echo json response
                echo json_encode($response);
              }
            } catch (Exception $e) {
              // Exception occurred.

              // Set response error to true
              $response[$fieldKeys->keyError]      = true;
              $response['profile_picture_upload_error']  = $e->getMessage();
            }
          }
        }

        // Add User Details Json Response Array
        $response[$fieldKeys->keyUser][$fieldKeys->keyUserId] 			= $signup[$fieldKeys->keyUserId];
        $response[$fieldKeys->keyUser][$fieldKeys->keyFirstName]    = $signup[$fieldKeys->keyFirstName];
        $response[$fieldKeys->keyUser][$fieldKeys->keyLastName]     = $signup[$fieldKeys->keyLastName];
        $response[$fieldKeys->keyUser][$fieldKeys->keyUsername]     = $signup[$fieldKeys->keyUsername];
        $response[$fieldKeys->keyUser][$fieldKeys->keyEmailAddress] = $signup[$fieldKeys->keyEmailAddress];
        $response[$fieldKeys->keyUser][$fieldKeys->keyPassword]     = $password;

        // Add success message
        $response[$fieldKeys->keySuccessMessage]                    = "Signup successfull!";

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
