<?php

//    Copyright (c) 2020 by David Kariuki (dk).
//    All Rights Reserved.


class DatabaseConnection
{

    // Connection status value variable
    private $connectToDB;


    /**
    * Class destructor
    */
    function __construct()
    {}


    /**
    * Class destructor
    */
    function __destruct()
    {}


    // Connecting to database
    public function Connect()
    {

        // Call configuration file
        require_once 'configs/DatabaseConfiguration.php';

        // Connecting to MYSQL database
        $this->connectToDB = new mysqli(HOST, HOST_USERNAME, HOST_PASSWORD, DATABASE_NAME);

        // Return database handler
        return $this->connectToDB;
    }
}

?>
