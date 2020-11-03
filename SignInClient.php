<?php

// User Signin

// Enable Error Reporting
error_reporting(1);

// Call Required Functions Classes
require_once 'classes/UserAccountFunctions.php';
require_once 'classes/FieldKeys.php';


// Create Classes Objects
$userAccountFunctions = new UserAccountFunctions();
$fieldKeys						= new FieldKeys();

// Create Json Response Array And Initialize Error To FALSE
$response = array($fieldKeys->keyError => false);

// Receive Email Address And Password
if (isset($_POST[$fieldKeys->keyUsername]) && isset($_POST[$fieldKeys->keyPassword])) {

    // Get Values From POST
		$username = $_POST[$fieldKeys->keyUsername]	? $_POST[$fieldKeys->keyUsername] : '';
    $password = $_POST[$fieldKeys->keyPassword] ? $_POST[$fieldKeys->keyPassword]	: '';

    // Get user by email address and password
    $getUser = $userAccountFunctions->getUserByUsernameAndPassword($username, $password);

    // Check if user was found
    if ($getUser !== false) {
        // User found

        // Set response error to false
        $response[$fieldKeys->keyError] = false;

        // Add User Details To Response Array
        $response[$fieldKeys->keyUser][$fieldKeys->keyUserId]   		= $getUser[$fieldKeys->keyUserId];
				$response[$fieldKeys->keyUser][$fieldKeys->keyFirstName]		= $getUser[$fieldKeys->keyFirstName];
				$response[$fieldKeys->keyUser][$fieldKeys->keyLastName]			= $getUser[$fieldKeys->keyLastName];
				$response[$fieldKeys->keyUser][$fieldKeys->keyUsername]			= $getUser[$fieldKeys->keyUsername];
				$response[$fieldKeys->keyUser][$fieldKeys->keyEmailAddress]	= $getUser[$fieldKeys->keyEmailAddress];
        $response[$fieldKeys->keyUser][$fieldKeys->keyPassword] 		= $password;

        // Encode and echo Json response
        echo json_encode($response);

    } else {
      // User Not Found

      // Check For Wrong Password (Credentials Mismatch)
  		if ($userAccountFunctions->isUsernameInUsersTable($username)) {
          // User with the emailAddress exists in the database

          // Set response error to true
          $response[$fieldKeys->keyError]        = true;
          $response[$fieldKeys->keyErrorMessage] = "Incorrect password for @" . $username;

          // Encode and echo Json response
          echo json_encode($response);

  		} else {
    			// User not found

          // Set response error to true
    			$response[$fieldKeys->keyError]         = true;
    			$response[$fieldKeys->keyErrorMessage]  = "We didn't find a user with that username!";

          // Encode and echo Json response
    			echo json_encode($response);
  		}
    }
} else {
    // Mising Fields

    // Set response error to true
    $response[$fieldKeys->keyError]          = true;
    $response[$fieldKeys->keyErrorMessage]   = "Something went terribly wrong!";

    //Return Response
    echo json_encode($response);
}

?>
