<?php

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
   
    $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Sales_Orders/4269537000089425085",
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
                      "Carrier_Company": "UPS",
                      "Status": "Product Ready to Ship"
                      
                    }
                          ],
                 
                }',
                  CURLOPT_HTTPHEADER => array(
                    "Authorization: Zoho-oauthtoken $token",
                    'Content-Type: application/json',
                    'Cookie: 1a99390653=56bdb8954b9cdff9a2ee611ff070c4d2; 1ccad04dca=ac2c79d538938da07c169c4202fd521e; JSESSIONID=663E0CF2D7DF26F31C2AFF255ACDD228; _zcsr_tmp=f5ecce93-2893-44d9-b1c7-c0d36742629b; crmcsr=f5ecce93-2893-44d9-b1c7-c0d36742629b'
                  ),
                ));
                
             echo   $response = curl_exec($curl);