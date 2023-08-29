<?php

require ('database.php');

 $timestamp=time();
$timeGapUpd=date(strtotime("-2 days"));

$now = new DateTime();
$begin = new DateTime('17:00:00');
$end = new DateTime('20:00:00');

if ($now >= $begin && $now <= $end)
{


//$sql = "SELECT * FROM `warehouse`";
$sql = "SELECT * FROM `warehouse`WHERE `dupStatus` = '2' ORDER BY time_update ASC LIMIT 1";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
        while ($roww = mysqli_fetch_array($res)) {
             $projectt[] = $roww;
         }}}
             foreach ($projectt as $row)
             {
             //print_r($projectt);
             $idd=$row[0];
             $dbconsign=$row[1]; 
             $dbTracking=$row[2]; 
             $wooCommerceId=$row[3]; 
             $dbStatus=$row[4];
           
             $dbtime=$row[6];
           
             
             if($dbStatus == "delivered" && $dbtime <= $timeGapUpd)
             {
                 echo 'fsdgdfgsdfd';
                
                echo  $sqlquery = "UPDATE `warehouse` SET status = 'completed', time_update = '$timestamp',`dupStatus` = '0' WHERE `warehouse`.`id` = $idd";
               
                    if ($conn->query($sqlquery) === TRUE) {
                        
                        
                    
                        #send status to the woocommerce from the fedex
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders/$wooCommerceId?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
  "status": "completed"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Cookie: PHPSESSID=062ffc1da5bc7894a034e0e33ca941c4; designer_session_id=062ffc1da5bc7894a034e0e33ca941c4'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
                      
                      
 
}
    }
    }
     $LogDate = date("jS  F Y h:i:s A");
file_put_contents('response1_CompDel.txt', $LogDate . $wooCommerceId  . PHP_EOL, FILE_APPEND);


#Delete the completed Record


$sql = "SELECT * FROM `warehouse`";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
        while ($roww = mysqli_fetch_array($res)) {
             $projectss[] = $roww;
         } 
        
    }
    
}
         
             foreach ($projectss as $row)
             {
             //print_r($projectss);
             $idd=$row[0];
             $consign=$row[1];
             $trackkinngg=$row[2];
            $wooCommerceId=$row['woo_id'];
             $dbStatus=$row[4];
             $dbActualTime=$row[5];
            $dbUPDActualTime=$row[6];
            
             $timeGapUpd_hours=date(strtotime("-3 hours"));
             $timeGapUpdOneDay=date(strtotime("-2 days"));
             if($dbStatus == "completed" && $dbUPDActualTime <= $timeGapUpd_hours || $dbStatus ==  "Cancelled")
            
             {
                 
                  $sqlquery = "DELETE FROM `warehouse` WHERE `warehouse`.`id` = $idd";
                  
                    if ($conn->query($sqlquery) === TRUE) {
                       echo "record deleted successfully";
                       
                    }
             } 
             
             
             elseif($dbStatus == "processing" && $dbActualTime <= $timeGapUpdOneDay)
                
                                 {
                                     if(empty($trackkinngg))
                                     {
                                    
                                     $sqlquery = "DELETE FROM `warehouse` WHERE `warehouse`.`id` = $idd";
                                      
                                        if ($conn->query($sqlquery) === TRUE) {
                                        /*    echo "record deleted successfully";
                                         #change woocommerce status to trash
                                           $curl = curl_init();
                    
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders/$wooCommerceId?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'DELETE',
                      CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                      ),
                    ));
                    
                    $response = curl_exec($curl);
                    
                    curl_close($curl);    */
                                            
                                            
                                            
                                        }
                    
                 
             }
             
                                 }
             }
             
             
             
             
             
}
             
            
