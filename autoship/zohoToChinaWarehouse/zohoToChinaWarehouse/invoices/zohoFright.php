<?php

#include Database File
require ('database.php');

echo '<pre>';
#Recived Data From Webhook From Zoho CRM
$json = file_get_contents('php://input');

#Put Data in text file
//file_put_contents("ZohoDataWebhookForFright.txt",$json);

#Get Data from the file
$json1 = file_get_contents('ZohoDataWebhookForFright.txt');

#Decode json Response To Array
 $ZohoDataHook=explode("=",$json1);
  $ZohoCrmId=$ZohoDataHook[1];
  
  
  
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
  
  
  
  
  
 
 echo '<pre>';


 $query = "SELECT * FROM zohoChinaInvoices WHERE zoho_id = '$ZohoCrmId'";
 $result = $conn->query($query);
 
    
    while($res = mysqli_fetch_array($result))
    {
        $refrence_no = $res['order_id'];
    }







$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://ywtx.rtb56.com/webservice/PublicService.asmx/ServiceInterfaceUTF8',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => "appToken=f651e053023124210cbf573e7520c7ae&appKey=%E5%BE%AE%E6%A0%BC%E5%B7%A5%E8%B4%B8&serviceMethod=getbusinessfee&paramsJson=%7B%22reference_no%22%3A%22$refrence_no%22%7D",
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded'
  ),
));

echo $response = curl_exec($curl);

curl_close($curl);

$dataTXorderFee=json_decode($response,TRUE);

echo '<pre>';

echo $fright =$dataTXorderFee['data'][0]['currency_amount'];




    $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Invoices/$ZohoCrmId",
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
                      "TX_Bill_Freight": "'.$fright.'"
                      
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

    