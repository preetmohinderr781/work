<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api-m.paypal.com/v1/oauth2/token',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic QWF5eXNMX3JOUkgwV3c1WUZLR0Nfd1B3UjF0V3lzeEQwZ0huZXlrWVBwZVh1ZTZZU244ck5qUk5wd2FFcjlSdENtOHc5OHZSY0RwVnZ1cFM6RU5FemZhV01wVlVXUnhBRVVJQTM0dmV4LU9ReDlDeDFHaEY2WXhiZEV3V2g4WFd5V1l0R1FfSWRxMnhiZXQzQkd3MlNTUVNWdmFGUzlsTjU=',
    'Content-Type: application/x-www-form-urlencoded',
    'Cookie: l7_az=ccg13.slc'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo '<pre>';

$tokenn=json_decode($response,TRUE);
echo $accessToken=$tokenn['access_token'];


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api-m.paypal.com/v1/shipping/trackers-batch',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
  "trackers": [
    {
      "transaction_id": "393371612669",
    
    "status": "SHIPPED"

    }
     ]
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    "Authorization: Bearer dfs$accessToken",
    'Cookie: l7_az=ccg13.slc'
  ),
));

$response = curl_exec($curl);


$datta=json_decode($response);


print_r($datta);
