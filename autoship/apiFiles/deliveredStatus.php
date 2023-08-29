<?php

#database details
require ('database.php');

     $LogDate = date("jS  F Y h:i:s A");
echo $timestamp=time();
$timeGapUpd=date(strtotime("-2 days"));

$now = new DateTime();
$begin = new DateTime('20:00:00');
$end = new DateTime('24:00:00');
$begin1 = new DateTime('00:05:00');
$end1 = new DateTime('07:00:00');
$begin2 = new DateTime('07:20:00');
$end2 = new DateTime('14:00:00');

if (($now >= $begin && $now <= $end  ) || ( $now >= $begin1 && $now <= $end1)|| ( $now >= $begin2 && $now <= $end2))
{

# Checking the Id in database Then the loop will be started 
$sql = "SELECT * FROM `warehouse` WHERE `dupStatus` = '1' LIMIT 1";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
         while ($row = mysqli_fetch_array($res)) {
             $projects[] = $row;
         }}}
             foreach ($projects as $row)
             {
            
        
             unset($dbTrackStatus);
             
             $idd=$row[0];
             $dbconsign=$row[1]; 
             $dbTracking=$row[2];   
             $wooId=$row[3];
             $dbTrackStatus=$row[4]; 
            
            
            
             if($dbTrackStatus == "delivered")
             {
        
echo 'dsfaa';
#Checking the status Is not already same 
   $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders/$wooId?consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276&after=2021-11-20T00:00:00&before=2021-12-29T00:00:00&consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_POSTFIELDS =>'{
  "status": "delivered"
}',
  CURLOPT_HTTPHEADER => array(
    'ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125: cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276',
    'Content-Type: application/json'
  ),
));

 $response = curl_exec($curl);

curl_close($curl);
$jsonn=json_decode($response,TRUE);
echo $WooStatus= $jsonn['status'];
#end of Curl


       # Inserting the data in databse------------
 $sqlquery = "UPDATE `warehouse` SET `dupStatus` = '0' WHERE `warehouse`.`woo_id` = '$wooId'";
if ($conn->query($sqlquery) === TRUE) {
   echo "record inserted successfully";
}
    
file_put_contents('res_tracking_log.txt', $LogDate . $WooStatus.'------'.$dbTrackStatus . $wooId. 'Delivered' . PHP_EOL, FILE_APPEND);
if($WooStatus != "completed" || $WooStatus != "delivered" )
{
   #Checking the status Is not already same 
if($WooStatus != $dbTrackStatus)
{

    
#send status to the woocommerce from the fedex

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders/$wooId?consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276&after=2021-11-20T00:00:00&before=2021-12-29T00:00:00&consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
  "status": "'.$dbTrackStatus.'"
}',
  CURLOPT_HTTPHEADER => array(
    'ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125: cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276',
    'Content-Type: application/json'
  ),
));

 $response = curl_exec($curl);

curl_close($curl);

$data=json_decode($response);
echo '<pre>';
//print_r($data);


}


    # Getting the data from the database
    $sql = "SELECT * FROM `warehouse` WHERE `woo_id` = '$wooId'";
if ($res = mysqli_query($conn, $sql)) {
         while ($row = mysqli_fetch_array($res)) {
             
       #woocommerve Status from the Database
       $dbStatus=$row[4];
       
       #woocommerve ID from the Database
       $wooCommerceId=$row[3];
       
        $wooCommerceTimestamp=$row[6];
        

        
        if(empty($wooCommerceTimestamp))
        {
       if($dbStatus == "delivered")
 {
      # Inserting the data in databse------------
$sqlquery = "UPDATE `warehouse` SET `time_update` = '$timestamp' ,`dupStatus` = '2' WHERE `warehouse`.`woo_id` = '$wooCommerceId'";
if ($conn->query($sqlquery) === TRUE) {
   //echo "record inserted successfully";
 }
 } }
       
         }}


# End of the curl 
}}}}