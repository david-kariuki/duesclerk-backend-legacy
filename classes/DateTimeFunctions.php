<?php

/**
* Date and time functions class
* This class contains all the date, time and TimeZone functions required for time
* and date processing
*
* @author David Kariuki (dk)
* @copyright (c) 2020 David Kariuki (dk) All Rights Reserved.
*/

// Enable error reporting
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
    * @param dateTimeStamp  - Date and time stamp
    * @param countryAlpha2  - Country alpha2 for getting time zone
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
        $dateTime->setTimeZone(new DateTimeZone($this->getLocalTimezone($countryAlpha2)));

        // Format and return date time object
        return $dateTime->format(FORMAT_DATE_TIME_FULL);
    }


    /**
    * Function to get user timezone by alpha2
    *
    * @param countryAlpha2  - Country's alpha2
    *
    * @return timeZone      - Country's time zone
    */
    private function getLocalTimezone($countryAlpha2)
    {

        // Create timezone array
        $timeZone = array();

        // Get timezone by country alpha2
        $timeZone = \DateTimeZone::listIdentifiers(
            \DateTimeZone::PER_COUNTRY,
            strtoupper($countryAlpha2)
        );

        // Get timezone in array at current position incase of multiple timezones in array
        $current = current($timeZone);

        // Return TimeZone
        return $current;
    }


    /**
    * Function to get the default time zone numerical date and time
    *
    * @return dateTime - (Default timezone numerical date and time)
    */
    public function getDefaultTimeZoneNumericalDateTime()
    {

        $this->setTimeZoneUTC(); // Set time zone to UTC
        return strtotime(
            date(FORMAT_DATE_TIME_NUMERICAL, time())
        ); // Return numerical time stamp
    }


    /**
    * Function to get the default time zone textual date and time
    *
    * @return dateTime - (Default timezone textual date and time)
    */
    public function getDefaultTimeZoneTextualDateTime()
    {

        $this->setTimeZoneUTC(); // Set time zone to UTC

        return date(FORMAT_DATE_TIME_FULL); // Return full date and time
    }


    /**
    * Function to get time difference
    *
    * @param recentTime - Recent / new time
    * @param oldTime    - Old time
    *
    * @return integer   - Time difference
    */
    public function getNumericalTimeDifferenceInHours($recentTime, $oldTime)
    {

        // Check if recent time is numeric
        if (!is_numeric($recentTime)) {

            // Convert date time format
            $recentTime = $this->convertDateTimeFormat($recentTime, FORMAT_DATE_TIME_NUMERICAL);
        }

        // Check if old time is numeric
        if (!is_numeric($oldTime)) {

            // Convert date time format
            $oldTime = $this->convertDateTimeFormat($oldTime, FORMAT_DATE_TIME_NUMERICAL);
        }

        // Return time difference
        return (abs($recentTime - $oldTime) / 3600);
    }


    /**
    * Function to convert date and time formats
    *
    * @param dateAndTime    - Date and time to be converted
    * @param newFormat      - New date and time format
    *
    * @return DateTime      - Converted date and time
    */
    private function convertDateTimeFormat($dateAndTime, $newFormat)
    {

        // Create date from format
        $dateTime = DateTime::createFromFormat(
            FORMAT_DATE_TIME_FULL,  // Set date format
            $dateAndTime,           // Date time stamp
            new DateTimeZone('UTC') // Set time zone to UTC
        );

        // Switch new format
        switch ($newFormat) {

            case FORMAT_DATE_TIME_NUMERICAL:
            return strtotime($dateTime->format(FORMAT_DATE_TIME_NUMERICAL)); // Numerical

            default:
            return $dateTime->format($newFormat); // Other formats
        }
    }


    /**
    * Function to set TimeZone to UTC
    */
    private function setTimeZoneUTC()
    {

        date_default_timezone_set('UTC'); // Set time zone to UTC
    }

}


// EOF : DateTimeFunctions.php
