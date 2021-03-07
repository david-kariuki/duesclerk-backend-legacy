<?php

/**
* Date and time functions class
* This class contains all the date, time and TimeZone functions required for time
* and date processing
*
* @author David Kariuki (dk)
* @copyright Copyright (c) 2020 - 2021 David Kariuki (dk) All Rights Reserved.
*/

// Namespace declaration
namespace duesclerk\src;

// Enable error reporting
error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL|E_NOTICE|E_STRICT);

// Call project classes
use duesclerk\constants\Constants;


// Class declaration
class DateTimeFunctions
{

    private $constants; // Create Constants object

    /**
    * Class destructor
    */
    function __construct()
    {
        $this->constants = new Constants(); // Initialize constants object
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
    * @param countryAlpha2  - CountryAlpha2 for getting time zone
    */
    public function getLocalTime($dateTimeStamp, $countryAlpha2)
    {
        // Check if Date and Time stamp and CountryAlpha2 are null
        if ((!is_null($dateTimeStamp)) && (!is_null($countryAlpha2))) {

            // Catch errors
            try {

                // Create date from format
                $dateTime = \DateTime::createFromFormat(
                    FORMAT_DATE_TIME_FULL,      // Set date format
                    $dateTimeStamp,             // Date time stamp
                    new \DateTimeZone('UTC')    // Set time zone to UTC
                );

                // Set dateTimes' objects' time zone to local time zone
                $dateTime->setTimeZone(new \DateTimeZone(
                    $this->getLocalTimezone($countryAlpha2)
                ));

                // Format and return date time object
                return $dateTime->format(FORMAT_DATE_TIME_FULL);
                
            } catch (Exception $e){
            }
        } else {
            return null; // Return null
        }
    }

    /**
    * Function to get user timezone by alpha2
    *
    * @param countryAlpha2  - CountryAlpha2
    *
    * @return timeZone      - Country time zone
    */
    private function getLocalTimezone($countryAlpha2)
    {

        // Check if parameter is null
        if (!is_null($countryAlpha2)) {

            // Create timezone array
            $timeZone = array();

            // Get timezone by CountryAlpha2
            $timeZone = \DateTimeZone::listIdentifiers(
                \DateTimeZone::PER_COUNTRY,
                strtoupper($countryAlpha2)
            );

            // Get timezone in array at current position incase of multiple timezones in array
            $current = current($timeZone);

            return $current; // Return TimeZone

        } else {

            return null; // Return null
        }
    }

    /**
    * Function to get the default time zone numerical date and time
    *
    * @return dateTime - (Default timezone numerical date and time)
    */
    public function getDefaultTimeZoneNumericalDateTime()
    {

        $this->setTimeZoneUTC(); // Set time zone to UTC

        // Return numerical time stamp
        return strtotime(
            date(FORMAT_DATE_TIME_NUMERICAL, time())
        );
    }

    /**
    * Function to get the default time zone textual date and time
    *
    * @param format     - DateTime format
    *
    * @return dateTime  - (Default timezone textual date and time)
    */
    public function getDefaultTimeZoneTextualDateTime($format)
    {

        // Check if parameter is null
        if (!is_null($format)) {

            $this->setTimeZoneUTC(); // Set time zone to UTC

            return date($format); // Return formatted date and time

        } else {

            return null; // Return null
        }
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

        // Check if parameters are null
        if ((!is_null($recentTime)) && (!is_null($oldTime))) {

            // Check if recent time is numeric
            if (!is_numeric($recentTime)) {

                // Convert date time format
                $recentTime = $this->convertDateTimeFormat(
                    $recentTime,
                    FORMAT_DATE_TIME_NUMERICAL
                );
            }

            // Check if old time is numeric
            if (!is_numeric($oldTime)) {

                // Convert date time format
                $oldTime = $this->convertDateTimeFormat(
                    $oldTime,
                    FORMAT_DATE_TIME_NUMERICAL
                );
            }

            // Return time difference
            return (abs($recentTime - $oldTime) / 3600);

        } else {

            return null; // Return null
        }
    }

    /**
    * Function to convert string with both date and time formats
    *
    * @param dateAndTime    - Date and time to be converted
    * @param newFormat      - New date and time format
    *
    * @return DateTime      - Converted date and time
    */
    private function convertDateTimeFormat($dateAndTime, $newFormat)
    {

        // Check if parameters are null
        if ((!is_null($dateAndTime)) && (!is_null($newFormat))) {

            // Create date from format
            $dateTime = \DateTime::createFromFormat(
                FORMAT_DATE_TIME_FULL,      // Initial date format
                $dateAndTime,               // Date time stamp
                new \DateTimeZone('UTC')    // Set time zone to UTC
            );

            // Switch new format
            switch ($newFormat) {

                case FORMAT_DATE_TIME_NUMERICAL:
                return strtotime($dateTime->format(FORMAT_DATE_TIME_NUMERICAL)); // Numerical
                break;

                default:
                return $dateTime->format($newFormat); // Other formats
            }
        } else {

            return null; // Return null
        }
    }

    /**
    * Function to convert string with both date and time formats
    *
    * @param fromFormat     - Initial date format
    * @param dateAndTime    - Date and time to be converted
    * @param newFormat      - New date and time format
    *
    * @return DateTime      - Converted date and time
    */
    public function convertDateTimeFromFormat($dateAndTime, $fromFormat, $newFormat)
    {

        // Check if parameters are null
        if ((!is_null($dateAndTime)) && (!is_null($fromFormat)) && (!is_null($newFormat))) {

            // Create date from format
            $dateTime = \DateTime::createFromFormat(
                $fromFormat,                // Initial date format
                $dateAndTime,               // Date time stamp
                new \DateTimeZone('UTC')    // Set time zone to UTC
            );

            // Switch new format
            switch ($newFormat) {

                case FORMAT_DATE_TIME_NUMERICAL:
                return strtotime($dateTime->format(FORMAT_DATE_TIME_NUMERICAL)); // Numerical date

                case FORMAT_DATE_FULL:
                return strtotime($dateTime->format(FORMAT_DATE_FULL)); // Full date

                default:
                return $dateTime->format($newFormat); // Other formats
            }
        } else {

            return null; // Return null
        }
    }

    /**
    * Function to convert date string format
    *
    * @param date           - Date whose format is to be converted
    * @param newFormat      - New date format
    *
    * @return convertedDate - Date converted to the new format
    */
    public function convertDateFormat($date, $newFormat)
    {

        // Check if parameters are null
        if ((!is_null($date)) && (!is_null($newFormat))) {

            $createDate = date_create($date); // Create date from date string
            $formatDate = date_format($createDate, $newFormat); // Format date

            return $formatDate; // Return formatted date
        } else {

            return null; // Return null
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
