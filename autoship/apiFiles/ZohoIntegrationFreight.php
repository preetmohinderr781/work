<?php
echo '<pre>';
#Recived Data From Webhook From Zoho CRM
 $json = file_get_contents('php://input');

#Put Data in text file
file_put_contents("WooWareHouseZoho.txt",$json);

#Get Data from the file
$json1 = file_get_contents('WooWareHouseZoho.txt');

#Decode json Response To Array
 $ZohoDataHook=explode("=",$json1);
 $ZohoCrmId=$ZohoDataHook[1];
 
 #method for API
$apiMethod="fu.wms.outbound.getbilling";

#OPEN4x APP KEY 
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret 
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();
 

sleep(30);
echo '<pre>';
#-----------------Creating zoho Auth Token-----------------#
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
#decode response
$data = json_decode($response, true);
# Store access token in variable
 $token=$data['access_token'];
#-----------------------------Curl End-------------------------------#
#-----------------------Get Contacts from zoho-----------------------#
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Sales_Orders/$ZohoCrmId",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Zoho-oauthtoken $token",
    "cache-control: no-cache",
    "postman-token: 44e0e896-a17f-9226-ea35-b317d4184ab9"
  ),
));
#Execute Curl
$response = curl_exec($curl);
#Decode response
$ZohoData = json_decode($response,true);

//print_r($ZohoData);
#Fields to match WooID
 $WooField=$ZohoData['data'][0]['field54'];

#Status from Zoho
 $StatusZoho=$ZohoData['data'][0]['field55'];


#Get Consignment Using API From Warehouse
 #Get list inbound api request headers
                			 $jsonDataGetInbound="{
                	
                	'sales_no': '$WooField',
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
       // print_r($DataArrayInBoundList);
        
        foreach($DataArrayInBoundList as $DataArrayInBound)
        {
       
        echo $conSignment_no=$DataArrayInBound->consignment_no;
        echo '<br>';
        
        

#-------------------------GetBillingFromWarehouse-------------------------#

#

		 #inventory api request headers
			 $jsonDataBilling="{
	'consignment_no': '$conSignment_no'
}";
			
		# MD5 Encryption for generating the signature
		$rawSignatureSKU= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.getbillingtimestamp'.$timestamp.'v1.0'.$jsonDataBilling.'e4b362a3-7977-4699-82f7-31fadd33cf95';

         #md5 encrypt
		 $signatureSKU=md5($rawSignatureSKU);
			
		 
        #Inventory API
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.outbound.getbilling",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>"$jsonDataBilling",
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
		  ),
		));
        
		#Execute curl
		$response = curl_exec($curl);
        
		#json to array
		$BillingData=json_decode($response);	
        $DataArrayBilling =$BillingData->data->billinglist;
        //print_r($DataArrayBilling);
        foreach($DataArrayBilling as $BillKey)
        {
            $Amount[]=$BillKey->billing_amount;
        }}
        $totalAmount=array_sum($Amount);
        $ArrayTotalAmount[]=$totalAmount;
        
        echo $FinalTotalAmount=array_sum($ArrayTotalAmount);
        
      
#------------------------EndOfGetBillingFromWarehouse------------------------# 



#------------------------UpdateZohoFromWarehouse------------------------#
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Sales_Orders/$ZohoCrmId",
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
                      "field56": "'.$FinalTotalAmount.'",
                      
                      
                    }
                   
                  ],
                  "trigger": [
                    "approval"
                  ]
                }',
                  CURLOPT_HTTPHEADER => array(
                    "Authorization: Zoho-oauthtoken $token",
                    'Content-Type: application/json',
                    'Cookie: 1a99390653=56bdb8954b9cdff9a2ee611ff070c4d2; 1ccad04dca=ac2c79d538938da07c169c4202fd521e; JSESSIONID=663E0CF2D7DF26F31C2AFF255ACDD228; _zcsr_tmp=f5ecce93-2893-44d9-b1c7-c0d36742629b; crmcsr=f5ecce93-2893-44d9-b1c7-c0d36742629b'
                  ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                //echo $response;
               print_r($response);
#------------------------UpdateZohoFromWarehouse------------------------#

