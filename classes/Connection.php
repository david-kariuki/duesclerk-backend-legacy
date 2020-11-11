<?php

//    Copyright (c) 2020 by David Kariuki (dk).
//    All Rights Reserved.


class Connection {

    // Connection Status Value Variable
    private $connectToDB;

    // Connecting To Database
    public function Connect() {

        //Call Configuration File
        require_once 'Configuration.php';

        // Connecting To MYSQL Database
        $this->connectToDB = new mysqli(HOST, USERNAME, PASSWORD, DATABASE_NAME);

        // Return Database Handler
        return $this->connectToDB;
    }
}

?>
