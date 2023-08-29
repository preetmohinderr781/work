<?php

function updateZohoModule($refrence_no,$token,$ZohoCrmId)
{
    sleep(15);
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
  CURLOPT_POSTFIELDS => "appToken=f651e053023124210cbf573e7520c7ae&appKey=%E5%BE%AE%E6%A0%BC%E5%B7%A5%E8%B4%B8&serviceMethod=gettrackingnumber&paramsJson=%7B%22reference_no%22%3A%22$refrence_no%22%7D",
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded'
  ),
));

$response = curl_exec($curl);

curl_close($curl);


$dataTXorder=json_decode($response,TRUE);


$shipping_method_no =$dataTXorder['data']['channel_hawbcode'];


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

$response = curl_exec($curl);

curl_close($curl);

$dataTXorderFee=json_decode($response,TRUE);

echo '<pre>';

$fright =$dataTXorderFee['data'][0]['currency_amount'];




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
                      "TX_Bill_Status": "已预报",
                      
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

    
    
}

?>