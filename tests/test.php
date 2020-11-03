<?php

require_once '../classes/UserAccountFunctions.php';

$user = new UserAccountFunctions();

$signup = $user->getUserByUsernameAndPassword("flowamz", "password");
echo json_encode($signup);


?>