<?php


#database details
require ('../database.php');

$LogDate = date("jS  F Y h:i:s A");
// Create connection

$timestamp=time();
$now = new DateTime();
$begin = new DateTime('20:00:00');
$end = new DateTime('24:00:00');
$begin1 = new DateTime('00:05:00');
$end1 = new DateTime('07:00:00');
$begin2 = new DateTime('07:05:00');
$end2 = new DateTime('14:00:00');

/*if (($now >= $begin && $now <= $end  ) || ( $now >= $begin1 && $now <= $end1)|| ( $now >= $begin2 && $now <= $end2))
{*/


# Checking the Id in database Then the loop will be started
//$sql = "SELECT * FROM `zohoWarehouse` WHERE `dupStatus` = '1' AND `status` NOT IN ( 'delivery', 'picked')  LIMIT 1";

$sql = "SELECT * FROM `zohoWarehouse` WHERE `dupStatus` = '1' AND `status` NOT IN ('Delivered','Picked Up') ";

if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
         while ($row = mysqli_fetch_array($res)) {
             $projects[] = $row;
         }}}
             foreach ($projects as $row)
             {

             $idd=$row['id'];
            $dbconsign=$row['con_no'];
            $dbTracking=$row['ship_no'];
            $zoho_id=$row['zoho_id'];
            $dbTrackStatus=$row['status'];




             if($dbTrackStatus != "Delivered")
             {


#Checking the status Is not already same
###########################################################
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
  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Sales_Orders/$zoho_id",
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
$ZohoData = json_decode($response,true);
print_r($ZohoData);
$OrderStatus= $ZohoData['data'][0]['field55'];

###########################################################



       # Inserting the data in databse------------
 $sqlquery = "UPDATE `zohoWarehouse` SET `dupStatus` = '0' WHERE `zohoWarehouse`.`zoho_id` = '$zoho_id'";
if ($conn->query($sqlquery) === TRUE) {
   echo "record inserted successfully";
}

file_put_contents('OutForDelivery.txt', $LogDate . $OrderStatus.'----------------' .$dbTrackStatus. $zoho_id . PHP_EOL, FILE_APPEND);
if($OrderStatus != "completed" || $OrderStatus != "Delivered" )
{
   #Checking the status Is not already same
if($OrderStatus != $dbTrackStatus)
{
    
        
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Sales_Orders/$zoho_id",
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
                      
                      "field55": "Out for Delivery",
                      "Status": "Out for Delivery"
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
    
}


    # Getting the data from the database
    $sql = "SELECT * FROM `zohoWarehouse` WHERE `zoho_id` = '$zoho_id'";
if ($res = mysqli_query($conn, $sql)) {
         while ($row = mysqli_fetch_array($res)) {

        #woocommerve Status from the Database
       $dbStatus=$row['status'];

       #woocommerve ID from the Database
       $zohoCRM_id=$row['zoho_id'];

        $zohoTimestamp=$row['time_update'];



        if(empty($zohoTimestamp))
        {
       if($dbStatus == "Delivered")
 {
      # Inserting the data in databse------------
$sqlquery = "UPDATE `zohoWarehouse` SET `time_update` = '$timestamp' ,`dupStatus` = '2' WHERE `zohoWarehouse`.`zoho_id` = '$zohoCRM_id'";
if ($conn->query($sqlquery) === TRUE) {
   //echo "record inserted successfully";
 }
 } }

         }}


# End of the curl
}}}


//}
