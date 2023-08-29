<?php
echo '<pre>';
#Recived Data From Webhook From Zoho CRM
 $json = file_get_contents('php://input');

#Put Data in text file
file_put_contents("ZohoTracking.txt",$json);

#Get Data from the file
$json1 = file_get_contents('ZohoTracking.txt');

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


        


#-------------------------GetTrackingInfoFromWooCommerce-------------------------#
echo $WooField;
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.promowares.com/wp-json/wc-ast/v3/orders/$WooField/shipment-trackings?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125: cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276',
    'Cookie: PHPSESSID=b58d8b6cd98c06b729b920b73793770c; designer_session_id=b58d8b6cd98c06b729b920b73793770c'
  ),
));

echo $response = curl_exec($curl);

curl_close($curl);

$data=json_decode($response);

print_r($data);
foreach($data as $key)
{
    
     $tracking_provider[]=$key->tracking_provider.' ';
     $tracking_number[]=$key->tracking_number.' ';
}

echo $woo_tracking_provider=join($tracking_provider);
echo $woo_tracking_number=join($tracking_number);


#------------------------Endd of woocommerce API------------------------#



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
                      
                      "field": "'.$woo_tracking_number.'",
                      "Carrier_Company": "'.$woo_tracking_provider.'",
                      
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

