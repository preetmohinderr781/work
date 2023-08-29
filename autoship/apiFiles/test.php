<?php

#method for API
$apiMethod="fu.wms.outbound.cancel";

#OPEN4x APP KEY
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();


$jsonDataInventory='{

    "consignment_no": "OC9427082304170038",
	"cancel_type":"OT",
	"cancel_remark":"TEST"
}';
# MD5 Encryption for generating the signature



$rawSignatureSKU= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.canceltimestamp'.$timestamp.'v1.0'.$jsonDataInventory.'e4b362a3-7977-4699-82f7-31fadd33cf95';


 $signatureSKU=md5($rawSignatureSKU);


#------------------------Starting of the API CURL------------------------------#
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.outbound.cancel",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>"$jsonDataInventory",
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
 echo $response;


die();
require "database.php";

$sql = "SELECT * FROM `warehouse`";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_array($res)) {
            $projects[] = $row;
        }
    }
}
echo '<pre>';
print_r($projects);


die();
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret 
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();
#json body for API
                $jsonDataSku = "{
	'lstsku': [
		TMSS0328-600SMWT5PKUS,
	]
    }";

                # MD5 Encryption for generating the signature
                $rawSignatureSKU = 'app_key' . $appKey . 'formatjsonmethodfu.wms.sku.getlisttimestamp' . $timestamp . 'v1.0' . $jsonDataSku . 'e4b362a3-7977-4699-82f7-31fadd33cf95';

                #encrypt to MD5
                $signatureSKU = md5($rawSignatureSKU);

                #API for get items data using sku
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.sku.getlist",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => "$jsonDataSku",
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
                    ),
                ));

                #Execute Curl
                $response = curl_exec($curl);

                #Json To Arrau
                $data = json_decode($response);

                #Get sku List
                $skulist = $data
                    ->data->skulist;

                #loop over skulist
                foreach ($skulist as $key)

                {

                    $customer_code = $key->customer_code;

                    $custNumber = $key->weight;

                    $product_code = $key->product_code;

                    $sku_name = $key->sku_name;

                    $weight = $key->weight;

                    echo $weightInKGG = $weight / 1000;
                    $weightArray[] = $weightInKGG;
    
                }