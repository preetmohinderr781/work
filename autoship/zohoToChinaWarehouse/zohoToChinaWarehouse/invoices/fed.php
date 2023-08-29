<?php

echo "<pre>";
#database details
require "database.php";

$timestamp = time();
echo "<br>";
$timeGap = date(strtotime("-10 days"));

$LogDate = date("jS  F Y h:i:s A");

# Cul to generate the Access Token For Fedex API
$curl = curl_init();



#-------------------------Check Conddition--------------------------------#

$sql = "SELECT * FROM `zohoChinaInvoices`";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_array($res)) {
            $projects[] = $row;
        }
    }
}

foreach ($projects as $row) 
{
    $idd = $row["id"];
    $dbconsign = $row["con_no"];
    $dbTracking = $row["ship_no"];
   $ZohoCrmId = $row["zoho_id"];
    $dbTrackStatus = $row["status"];

    echo "<br>";
    
    
     if ($dbTrackStatus != "已递送" && $dbTrackStatus != "completed" && $dbTrackStatus != "已派送" && $dbTrackStatus != "已经派送" && $dbTrackStatus != "签收") {

    
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
  CURLOPT_POSTFIELDS => "appToken=f651e053023124210cbf573e7520c7ae&appKey=%E5%BE%AE%E6%A0%BC%E5%B7%A5%E8%B4%B8&serviceMethod=gettrack&paramsJson=%7B%22tracking_number%22%3A%22$dbTracking%22%7D",
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
 $response;

    $DATA= json_decode($response,TRUE);



echo $dbTrackStatus = $DATA['data'][0]['track_status_name'];
    
            

        


                 $sqlquery = "UPDATE `zohoChinaInvoices` SET `status` = '$dbTrackStatus'  WHERE `zohoChinaInvoices`.`zoho_id` = '$ZohoCrmId'";
                if ($conn->query($sqlquery) === TRUE)
                    {

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
                      "TX_Logistic_Order_Status": "'.$dbTrackStatus.'"
                      
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
           $dbTracking='';        
                    
                    
                }
    
        
}
    


    
