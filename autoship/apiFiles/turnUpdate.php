<?php

#include zones
require('TurnOn.php');
    
   echo $statusVal=$_REQUEST["status"];
   
   
   
    $sqlquery = "UPDATE `switch` SET `value` = '$statusVal' WHERE `switch`.`id` = 0";
if ($conn->query($sqlquery) === TRUE) {
    echo "record inserted successfully";
    file_put_contents("status_updated.txt","record inserted successfully");
}
    
