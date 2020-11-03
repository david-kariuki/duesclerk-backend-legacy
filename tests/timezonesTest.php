<?php

if (isset($_GET['countryAlpha2'])){
    
    echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta http-equiv="refresh" content=".1"/></head></html>';
    $countryAlpha2 = $_GET['countryAlpha2'];
    
    // Create timezone array
     $timezone = array();
    
    // Get timezone by country iso 2
     $timezone = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, strtoupper($countryAlpha2));
    
     // Get timezone in array at current position incase of multiple choices
     $current = current($timezone);
    
    // Get Default Set Server TimeZone
	date_default_timezone_set($current);

	// Get current date and time from the server
	$timeStamp 	= date("l") . " " . date('d') . ", " . date('F Y') . " " . date('H:i:s');
	$dots = '...';
	$slash = '/';
	echo '
	    <!doctype html>
	    <html>
	        <head>
	            <title>Timezones Test</title>
	        </head>
	        <body>
	            <table align = "start" style = "border: 2px solid #880e4f; border-collapse: collapse; width: 50%;">
	                <th style = "background:#880e4f; color:#FFFFFF; line-height: 60px; font-weight: 900;"> Timezone test results</th>
	                 <tr style = "background:#880e4f; color:#FFFFFF; line-height: 60px; font-weight: 900;">
	                    <td align="center">No. </td>
	                    <td align="center">Country Alpha 2</td>
	                    <td align="center">Time Zone</td>
	                    <td align="center">Date and Time</td>
	                </tr>';
	$count = 1; 
	
    // Return TimeZone
    if (strtoupper($countryAlpha2) == "US"){
		
		             
        foreach($timezone as $singleTimezone){
            
	        date_default_timezone_set($singleTimezone);
	        $timeStamp 	= date("l") . " " . date('d') . ", " . date('F Y') . " " . date('H:i:s');
			
			echo '
			    <tr style = "color:#000000; line-height: 35px; font-weight: normal;">       
                    <td align="center">' . $count . '</td>
                    <td align="center"> ' . strtoupper($countryAlpha2) . '</td>
                    <td align="center"> ' . json_encode($singleTimezone)  . ' </td>
                    <td align="center"> ' . $timeStamp . '</td>
                </tr>';
			$count++;            
        }
        
        
    } else {
        echo "The time for timezone " . $current . " in country alpha " . $countryAlpha2 . " is " . $timeStamp . "<br><br>";

        echo '
            <tr style = "color:#000000; line-height: 35px; font-weight: normal;">
                <td align="center">' . $count . '</td>
                <td align="center"> ' . strtoupper($countryAlpha2) . '</td>
                <td align="center"> ' . json_encode($current)  . ' </td>
                <td align="center"> ' . $timeStamp . '</td>
            </tr>';
    }
    
    
    echo '</table>
		        </body>
		    </html>        
		';
        	
} else {
    
    echo 'Alpha2 not set';
}
?>