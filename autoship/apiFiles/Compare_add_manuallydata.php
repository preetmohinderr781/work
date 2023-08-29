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



     # Checking the Id in database Then the loop will be started 
$sql = "SELECT * FROM `warehouse`";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
         while ($row = mysqli_fetch_array($res)) {
        
        $allData[]=$row;
            }}}
            
            foreach($allData as $sleted)
            {
                $wooIdFromDb[]=$sleted['woo_id'];
            }
         

     






                            #Get list inbound api request headers
                            			 $jsonDataGetInbound="{
                            	'page_no': 1,
                            	'page_size': 50
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
                   // print_r($DataArrayInBoundList); 
                    
                    foreach($DataArrayInBoundList as $key)
                    {
                        
                     $warehouseWooID[]=  $key->sales_no;
                    // echo '<br>';
                    }
                    
                      
                    
                    
                    ###########################################################
                    
                    
                    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://www.promowares.com/wp-json/wc/v3/orders?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276&page=1&per_page=30',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125: cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276',
    'Content-Type: application/json',
    'Cookie: PHPSESSID=cc5a0f2a06d08939a61b0cd3006fb5f6; designer_session_id=cc5a0f2a06d08939a61b0cd3006fb5f6'
  ),
));

$response = curl_exec($curl);

curl_close($curl);

$WooData=json_decode($response);

//print_r($WooData);
foreach($WooData as $WooKey)
{
    $wooId[]  = $WooKey->id;

}                 
$result=(array_intersect($warehouseWooID,$wooId));
                
               
    
$finalWooIds=array_diff($result,$wooIdFromDb);

 print_r($finalWooIds);    
          
           echo '<b>';
                //   print_r($finalWooIds);
                    echo '</b>';        
                    
                    foreach($finalWooIds as $finalWooId)
                    {
                       
                        if(!in_array($finalWooId,$wooIdFromDb))
                        {
                            #insert data to database
                               $sqlquery = "INSERT INTO warehouse VALUES 
    (NULL, '', '','$finalWooId','processing','$timestamp','','')";
if ($conn->query($sqlquery) === TRUE) {
    echo "record inserted successfully";
}




                        }
                        
                        
                    }
                    
                    