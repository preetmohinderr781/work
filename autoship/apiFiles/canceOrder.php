  <?php
  
  echo "<form method='post'>
  
  <input type='text' name='key'>
  
  <input type='submit' name='submit'></form>";
  
  
  if(isset($_POST['submit']))
  {
      
 $orderNo = $_POST['key'];
  
  $jsonDataInventory='{

    "consignment_no": "'.$orderNo.'",
	"cancel_type":"OT",
	"cancel_remark":"TEST"
}';

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
 $response;

 $LogDate=date("jS  F Y h:i:s A");
 file_put_contents('cancelll_log.txt',$LogDate.$response.PHP_EOL,FILE_APPEND);

$data=json_decode($response);
echo '<pre>';

print_r($data);

}