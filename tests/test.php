<?php


$myarray  = array(

    "FirstName" => "David",
    "LastName" => "Kariuki",
    "Gender" => "Male",
    "BusinessName" => "",
    "CityName" => "",
    "PhoneNumber" => "+254700619045",
    "EmailAddress" => "dkaris.k@gmail.com",
    "CountryCode" => "254",
    "CountryAlpha2" => "KE",
    "Password" => "password",
    "AccountType" => "AccountTypePersonal"
);

foreach ($myarray as $key=>$value) {
    if (is_null($value) || $value == '')
        unset($myarray[$key]);
}


?>
