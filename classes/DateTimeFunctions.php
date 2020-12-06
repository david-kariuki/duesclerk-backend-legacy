<?php

/**
* Date and time functions class
* This class contains all the date, time and TimeZone functions required for time
* and date processing
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_NOTICE|E_STRICT);

class DateTimeFunctions
{

    /**
    * Class destructor
    */
    function __construct()
    {
        // Call required functions classes
        require_once 'Keys.php'; // Call keys file
    }


    /**
    * Class destructor
    */
    function __destruct()
    {

    }


    /**
    * Function to get local time from default time zone time
    * This function is used to convert UTC to local time and display it correctly
    *
    * @param dateTimeStamp - Date and time stamp
    * @param countryAlpha2 -Country alpha2 for getting time zone
    */
    public function getLocalTime($dateTimeStamp, $countryAlpha2)
    {
        // Create date from format
        $dateTime = DateTime::createFromFormat(
            FORMAT_DATE_TIME_FULL,      // Set date format
            $dateTimeStamp,             // Date time stamp
            new DateTimeZone('UTC')     // Set time zone to UTC
        );

        // Set dateTimes' objects' time zone to local time zone
        $dateTime->setTimeZone(
            new DateTimeZone(
                $this->getLocalTimezone($countryAlpha2)
            )
        );

        // Format and return date time object
        return $dateTime->format(FORMAT_DATE_TIME_FULL);
    }


    /**
    * Function to get client timezone by alpha2
    *
    * @param countryAlpha2 - country alpha2
    *
    * @return timeZone
    */
    private function getLocalTimezone($countryAlpha2)
    {

        // Create timezone array
        $timeZone = array();

        // Get timezone by country alpha2
        $timeZone = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, strtoupper($countryAlpha2));

        // Get timezone in array at current position incase of multiple timezones in array
        $current = current($timeZone);

        // Return TimeZone
        return $current;
    }


    /**
    * Function to get the default time zone date and time
    *
    * @return dateTime - (Default timezone date and time)
    */
    public function getDefaultTimeZoneDateTime()
    {

        date_default_timezone_set('UTC'); // Set time zone to UTC

        return date(FORMAT_DATE_TIME_FULL); // Return full date and time
    }

}


// EOF : DateTimeFunctions.php
