<?php

   $dot = ".";
   $count = 0;
   for (;;){
       echo $dot;
       $dot .= ".";
       echo  "</br>";
       $count++;
       if ($count == 50){
           $len = strlen($dot);
           
           for ($count = 0; $count <= $len; $count++){
               
               
                $dot = substr($dot, 1);
                echo $dot . "<br>";
           }
           
           $count = 0;
       }
   }
?>