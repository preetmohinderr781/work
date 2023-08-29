<?php
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders/86404?consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276&consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_POSTFIELDS =>'{
  "status": "delivered"
}',
  CURLOPT_HTTPHEADER => array(
    'ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125: cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276',
    'Content-Type: application/json'
  ),
));

 $response = curl_exec($curl);

curl_close($curl);
$jsonn=json_decode($response,TRUE);
echo '<pre>';
print_r($jsonn);
echo $WooStatusCheck= $jsonn['status'];