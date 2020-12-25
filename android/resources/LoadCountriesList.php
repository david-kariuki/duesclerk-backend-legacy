<?php
// Load Countries List

// Enable Error Reporting
error_reporting(1);

// Call autoloader fie
require_once $_SERVER["DOCUMENT_ROOT"] . "/android/vendor/autoload.php";


// Create Classes Objects
$listGridFunctions    = new ListGridFunctions();
$keys            = new Keys();

// Count Rows Affected
$count = 0;

// Show Message
echo "<hr><br> Inserting Countries Data<br><br>";


// Empty Countries Data Table
$clearTable = $listGridFunctions->emptyCountriesTable();

// Check if countries table is empty
if (($clearTable == true) || ($clearTable == null)) {
  // Table Cleared Or Was Initially Empty

    // Show message
    echo "Table Emptied.</br></br>";

    // Get countries data xml file
    $countriesDataXml = simplexml_load_file("xml/countries_data.xml") or die("Error: Cannot create object");
    //print_r($countriesDataXml);

    // Check if xml was read
    if ($countriesDataXml) {

      // Show message
      echo "Reading xml</br></br>";

      // Loop through array
      foreach ($countriesDataXml->children() as $countries) {

        $countryId      = $countries->$keys->fieldCountryId;
        $countryName    = $countries->$keys->fieldCountryName;
        $countryCode    = $countries->$keys->fieldCountryCode;
        $countryAlpha2  = $countries->$keys->fieldCountryAlpha2;
        $countryAlpha3  = $countries->$keys->fieldCountryAlpha3;
        $countryFlag    = $countries->$keys->fieldCountryFlag;

        if (strlen($countryId) == 0 ) {

           // Echo Error Message
           echo "Country Id is null <br/>";

        } else if (strlen($countryName) == 0 ) {

           // Echo Error Message
           echo "Country Name is null <br/>";
        } else if (strlen($countryCode) == 0 ) {

           // Echo Error Message
           echo "Country Code is null <br/>";
         } else if (strlen($countryAlpha2) == 0 ) {

           // Echo Error Message
           echo "Country Iso2 is null <br/>";
         } else if (strlen($countryAlpha3) == 0 ) {

           // Echo Error Message
           echo "Country Iso3 is null <br/>";
         } else if (strlen($countryFlag) == 0 ) {

           // Echo Error Message
           echo "Country Flag is null <br/>";
         } else {

           // echo '<br/>ok';

           // Insert Into Countries Data Table
           $loadCountriesData = $listGridFunctions->loadCountriesTable($countryId, $countryName, $countryCode, $countryAlpha2, $countryAlpha3, $countryFlag);

            // Check If Data Was Inserted Into Countries Data Table
            if (true == $loadCountriesData) {
              // Inserted

              // Increment Data Items Count
              $count++;

              // Show Inserted Data
              echo "[" . $count . "] true : [INSERTED] : " . $countryId . '.</br>';
              echo "[" . $count . "] true : [INSERTED] : " . $countryName . '.</br>';
              echo "[" . $count . "] true : [INSERTED] : " . $countryCode . '.</br>';
              echo "[" . $count . "] true : [INSERTED] : " . $countryAlpha2 . '.</br>';
              echo "[" . $count . "] true : [INSERTED] : " . $countryAlpha3 . '.</br>';
              echo "[" . $count . "] true : [INSERTED] : " . $countryFlag . '.</br>';

              echo "<br><br>";

              $countryId      = null;
              $countryName    = null;
              $countryCode    = null;
              $countryAlpha2  = null;
              $countryAlpha3  = null;
              $countryFlag    = null;

            } else if (false == $loadCountriesData) {
              // Countries Data Not Inserted

              // Show Error Message
              echo "false: Countries data not inserted!" . '</br>';

              // Exit Script
              exit;
            } else {

              // Show Error Message
              echo "false: Something went terribly wrong!" . '</br>';
            }
        }
      }
    }

    // Show Rows Affected
    echo "<br/>" . $count . " rows affected.<br><br>";

    // Exit Script
    exit;
} else if ($clearTable == false) {
  // Table Emptying Failed

  // Show Error Message
  echo "Something went terribly wrong!";

  // Exit Script
  exit;
}

?>
