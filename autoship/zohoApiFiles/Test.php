<?php

echo '<pre>';

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
  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Sales_Orders/4269537000089425085",
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

file_put_contents("ZohoWebAllDAta.txt",$response);

$ZohoData = json_decode($response,true);

echo $ZohoContactID= $ZohoData['data'][0]['Contact_Name']['id'];

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Contacts/$ZohoContactID",
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
$ZohoContactData = json_decode($response,true);



echo $contactZoho= $ZohoContactData['data'][0]['Phone'];
$contactZoho= $ZohoContactData['data'][0]['Phone'];
if(empty($contactZoho))
{
   $contactZoho = $ZohoContactData['data'][0]['field22'];
   
}

$companyZoho= $ZohoContactData['data'][0]['field1'];

print_r($ZohoContactData);










die();


$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Sales_Orders/4269537000085529242",
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

//file_put_contents("ZohoWebAllDAta.txt",$response);




$ZohoData = json_decode($response,true);

print_r($ZohoData);

die();

echo $ZohoContactID= $ZohoData['data'][0]['Contact_Name']['id'];

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Contacts/$ZohoContactID",
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
$ZohoContactData = json_decode($response,true);
 $contactZoho= $ZohoContactData['data'][0]['Phone'];
echo '<pre>';
print_r($ZohoContactData);
