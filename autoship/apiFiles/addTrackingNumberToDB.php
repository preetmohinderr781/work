<?php

require ('database.php');

$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret 
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
 $timestamp=time();

echo '<pre>';

$sql = "SELECT * FROM `warehouse` ";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
         while ($row = mysqli_fetch_array($res)) {
             $projects[] = $row;
         }
             foreach ($projects as $row)
             {
           echo '<pre>';
             print_r($row);
             $idd=$row[0];
             $dbconsign=$row[1]; 
             $dbTracking=$row[2];   
             $wooId=$row[3];
             $dbTrackStatus=$row[4]; 
                    if(empty($dbTracking))
                    {
                        #OPEN4x APP KEY 
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret 
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();

 #Get list inbound api request headers
                			 $jsonDataGetInbound="{
                	
                	'consignment_no': '$dbconsign',
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
     echo "<pre>";
            //print_r($InBoundListData);
           foreach($DataArrayInBoundList as $DataArrayInBound)
        {
          $shipping_no=$DataArrayInBound->shipping_no;
       
         $conSignment_no=$DataArrayInBound->consignment_no;
        }
        if(!empty($dbconsign)){
        if(!empty($shipping_no)){
            $sqlquery = "UPDATE `warehouse` SET `ship_no` = '$shipping_no' WHERE `warehouse`.`id` = $idd";
if ($conn->query($sqlquery) === TRUE) {
    echo "record inserted successfully";
}}
           
}
                    }



}
        
    }
    
}











