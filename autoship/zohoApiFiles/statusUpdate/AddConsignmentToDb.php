<?php

require ('../database.php');

$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret 
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();

 $timeGap=date(strtotime("-15 days"));
 
 
 # Checking the Id in database Then the loop will be started 
$sql = "SELECT * FROM `zohoWarehouse` WHERE date BETWEEN $timeGap AND $timestamp ";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
         while ($row = mysqli_fetch_array($res)) {
             $projects[] = $row;
         }}}
             foreach ($projects as $row)
             {
           echo '<pre>';
            // print_r($row);

             
            $idd=$row['id'];
             $dbconsign=$row['con_no'];
             $dbTracking=$row['ship_no'];
        echo     $zoho_id=$row['zoho_id'];
             $dbTrackStatus=$row['status'];
             $website_order_id=$row['website_order_id'];
             
             if(empty($dbconsign))
             {
                 
                
                            #Get list inbound api request headers
                            	echo		 $jsonDataGetInbound="{
                            	'sales_no': '$website_order_id',
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
            		
            		//print_r($InBoundListData);
                    $DataArrayInBoundList =$InBoundListData->data->data;
                    //print_r($DataArrayInBoundList); 
                    foreach($DataArrayInBoundList as $keyy)
        {
            
        echo   $consignment=$keyy->consignment_no;
           $customer_code=$keyy->customer_code;
           $logistics_product_code=$keyy->logistics_product_code;
           $shipping_no=$keyy->shipping_no;
            $status=$keyy->status;
            
            if($logistics_product_code == "F134")
        {
            $logisticShipping="FedEx Ground";
        }
        elseif($logistics_product_code == "F132")
        {
            $logisticShipping="FedEx";
        }
        elseif($logistics_product_code == "F126")
        {
            $logisticShipping="UPS";
        }
        elseif($logistics_product_code == "F123")
        {
            $logisticShipping="UPS";
        }
        elseif($logistics_product_code == "F124")
        {
            $logisticShipping="UPS";
        }
        

            
            if($status != "X")
            {
                
                echo 'fsdfasfsdfsfasdfafsdfsa';
                
        
    if(!empty($consignment)){   
   
        # Inserting the data in databse------------
$sqlquery = "UPDATE `zohoWarehouse` SET `con_no`= '$consignment' WHERE `zohoWarehouse`.`zoho_id` = '$zoho_id'";
if ($conn->query($sqlquery) === TRUE) {
    echo "record inserted successfully";

if($dbTrackStatus == "processing" || $dbTrackStatus == "Initiated") 
{

            
if($status == 'S' || $status == 'P' || $status == 'C' )
{
    

    #send status to the woocommerce from the fedex
    
    
    
    ########################################################
    
    $curl = curl_init();
curl_setopt_array($curl, array(
CURLOPT_URL => 'https://accounts.zoho.com/oauth/v2/token',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'POST',
CURLOPT_POSTFIELDS => array('refresh_token' => '1000.116fa7ce162f67da31d5d4f25c2137f5.ffcf6a4620418b17dea9a4983e4cc054','client_id' => '1000.N2N8F5GVM3ZGDKSGEJ6647G228R9VU','client_secret' => 'd1d2e2ab662f5ca43839bae7a822b9cb7a37f11aa8','grant_type' => 'refresh_token'),
CURLOPT_HTTPHEADER => array(
'Cookie: 3e285c6f31=7165323247d658488913e2292ba83474; _zcsr_tmp=306069e5-9daa-4c2d-af69-23aa9e9eaf9e; iamcsr=306069e5-9daa-4c2d-af69-23aa9e9eaf9e'
),
));
#Excecute Curl
$response = curl_exec($curl);
#decode responsep
$data = json_decode($response, true);
# Store access token in variable
 $token=$data['access_token'];
    
    $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Sales_Orders/$zoho_id",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'PUT',
                  CURLOPT_POSTFIELDS =>'{
                  "data": [
                    {
                      "field55": "Shipping Label Created",
                      "field": "'.$shipping_no.'",
                      "Carrier_Company": "'.$logisticShipping.'",
                      "Status": "Product Ready to Ship",
                      
                    }
                          ],
                   "trigger": [
                    "approval",
                    "workflow"
                  ]
                }',
                  CURLOPT_HTTPHEADER => array(
                    "Authorization: Zoho-oauthtoken $token",
                    'Content-Type: application/json',
                    'Cookie: 1a99390653=56bdb8954b9cdff9a2ee611ff070c4d2; 1ccad04dca=ac2c79d538938da07c169c4202fd521e; JSESSIONID=663E0CF2D7DF26F31C2AFF255ACDD228; _zcsr_tmp=f5ecce93-2893-44d9-b1c7-c0d36742629b; crmcsr=f5ecce93-2893-44d9-b1c7-c0d36742629b'
                  ),
                ));
                
             echo   $response = curl_exec($curl);
                
    ########################################################

 $LogDate=date("jS  F Y h:i:s A");
 file_put_contents('statusChange_log.txt',$LogDate."---".$zoho_id."---".$website_order_id."---".$response.PHP_EOL,FILE_APPEND);
    
# End of the curl 

}
    
}


}
}
                 
        }    
             }
             }
             }