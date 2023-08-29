<?php
require ('database.php');
#include Function File
require ('function.php');
$timestamp=time();


# Getting the Recent order details and shipments

//$sqlquery = "DELETE FROM `wooFunctionLabel` WHERE `wooFunctionLabel`.`status` = '0'";
//$res = mysqli_query($conn, $sqlquery); 


$sql = "SELECT * FROM `wooFunctionLabel`WHERE `status` = '1' LIMIT 1";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
        while ($roww = mysqli_fetch_array($res)) {
            #Array to store the whole values
             $projectt[] = $roww;
         }}}
             foreach ($projectt as $row)
             {
                        # DB ID
                         $idd=$row['id'];
                         # Consignment number
                         $consignment=$row['con_no']; 
                         #woocommerce ID
                         $wooId=$row['wooId']; 
                         # Status 
                         $status=$row['status']; 
                         $timestampdb=$row['timestamp'];
                      
                      if($timestamp >= $timestampdb)
                      {
                          statusChange($consignment, $wooId);
                     
          
          
            
            $sqlDBupd = "UPDATE `wooFunctionLabel` SET status = '0' WHERE `wooFunctionLabel`.`id` = $idd";
            $res = mysqli_query($conn, $sqlDBupd); 
        }   } 