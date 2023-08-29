<?php

#include zones
require('TurnOn.php');
    
   echo $statusVal=$_REQUEST["status"];
   
   
   
    $sqlquery = "UPDATE `zohoSwitch` SET `value` = '$statusVal' WHERE `zohoSwitch`.`id` = 0";
if ($conn->query($sqlquery) === TRUE) {
    echo "record inserted successfully";
    file_put_contents("status_updated.txt","record inserted successfully");
}
    
