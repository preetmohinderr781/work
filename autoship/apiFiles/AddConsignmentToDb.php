<?php

require ('database.php');

$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret 
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();

 $timeGap=date(strtotime("-15 days"));
 
 
 # Checking the Id in database Then the loop will be started 
$sql = "SELECT * FROM `warehouse` WHERE date BETWEEN $timeGap AND $timestamp ";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
         while ($row = mysqli_fetch_array($res)) {
             $projects[] = $row;
         }}}
             foreach ($projects as $row)
             {
           echo '<pre>';
            // print_r($row);
             $idd=$row[0];
             $dbconsign=$row[1]; 
             $dbTracking=$row[2];   
        echo     $wooId=$row[3];
             $dbTrackStatus=$row[4]; 
             
             if(empty($dbconsign))
             {
                            #Get list inbound api request headers
                            			 $jsonDataGetInbound="{
                            	'sales_no': '$wooId',
                            	'page_no': 1,
                            	'page_size': 10
                            }";
            					# MD5 Encryption for generating the signature
            		$rawSignatureSKU= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.getlisttimestamp'.$timestamp.'v1.0'.$jsonDataGetInbound.'e4b362a3-7977-4699-82f7-31fadd33cf95';
            
                     #md5 encrypt
            		 $signatureSKU=md5($rawSignatureSKU);
            		
            		 #Inventory API
            		$curl = curl_init();
            
            		  curl_setopt_array($curl, array(
            		  CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.outbound.getlist",
            		  CURLOPT_RETURNTRANSFER => true,
            		  CURLOPT_ENCODING => '',
            		  CURLOPT_MAXREDIRS => 10,
            		  CURLOPT_TIMEOUT => 0,
            		  CURLOPT_FOLLOWLOCATION => true,
            		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            		  CURLOPT_CUSTOMREQUEST => 'POST',
            		  CURLOPT_POSTFIELDS =>"$jsonDataGetInbound",
            		  CURLOPT_HTTPHEADER => array(
            			'Content-Type: application/json',
            			'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
            		  ),
            		));
                    
            		#Execute curl
            		$response = curl_exec($curl);
                    
            		#json to array
            		$InBoundListData=json_decode($response);	
                    $DataArrayInBoundList =$InBoundListData->data->data;
                  //  print_r($DataArrayInBoundList); 
                    foreach($DataArrayInBoundList as $keyy)
        {
            
           $consignment=$keyy->consignment_no;
           $customer_code=$keyy->customer_code;
           $shipping_no=$keyy->shipping_no;
        echo    $status=$keyy->status;
            if($status != "X")
            {
        
    if(!empty($consignment)){    
        # Inserting the data in databse------------
$sqlquery = "UPDATE `warehouse` SET `con_no`= '$consignment' WHERE `warehouse`.`woo_id` = '$wooId'";
if ($conn->query($sqlquery) === TRUE) {
    echo "record inserted successfully";

if($dbTrackStatus == "processing" || $dbTrackStatus == "Initiated") 
{
    Echo 'ShippingSucessfulProcessign';
            
if($status == 'S' || $status == 'P' || $status == 'C' )
{
    
    Echo 'ShippingSucessful';
    #send status to the woocommerce from the fedex
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders/$wooId?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
  "status": "shipping-label"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Cookie: PHPSESSID=062ffc1da5bc7894a034e0e33ca941c4; designer_session_id=062ffc1da5bc7894a034e0e33ca941c4'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

 $LogDate=date("jS  F Y h:i:s A");
 file_put_contents('statusChange_log.txt',$LogDate.$wooId.$response.PHP_EOL,FILE_APPEND);
    
# End of the curl 

}
    
}


}
}
                 
        }    
             }
             }
             }