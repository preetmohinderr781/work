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

curl_setopt_array($curl, [
    CURLOPT_URL => "https://apis.fedex.com/oauth/token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=l751a40caae1d8495187357b2fee4b4706&client_secret=7634a28b-e344-48dd-9e13-451c626b6df1",
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/x-www-form-urlencoded",
        "Cookie: _abck=B2A5A0C32BB5275885EAFEFBAC4D3123~-1~YAAQt106F6mbsK19AQAAiusHsweuhJx3oXc/d6BIy2UK3QFGIZxG+k531NwoDASAQLL68QKO6gJ663j3Dsk9MeUcRZqP+nTQExJp9mie2TmbRu3/nlTInBnGwcSRvZG2X/DDnyzf21m0cA+RbHFrZn04kq2BIYwasEhE7iFf+ojZri2MSMOdbIFfUJFVQf4orXJ8sJ5U9mRYSxJ8IWBwg9Eu2U9gUjgiNco/26+QF3o/1JSY74qieYdd1vx4Y8TW3FlIRKS/oB5xcxQH0X60V9LM4cDxOYD5z/50eAOJHYqpevUvb7q6bbn2ZIMXX69CI/uKF3NaMYa2+ZFZ/BSSecXZ2MBce8KgY/jits/n23N2djt1XxYwBbxsHQMWQzqG5xVWLI/U~-1~-1~-1; ak_bmsc=89E0D27781B2BCAAA575B665676F33EA~000000000000000000000000000000~YAAQd106F2O+mC99AQAAVCXSsg5FtbTc5JTTKe2CSWoMbHo7XXi+67imJSSyQ4FyjeJMgc8YPe0buxjsyoS+Zogz9ftHFkKce8VcW9+EGB/a2uIraCRPL+FABWNKgjkEZLYmnqdSM4z+DOAz3OP693+rtXSNHWBBQTqADJiZFIfTsBq1AzGWmtMi1bVKcLxyz0tga6q1a1pMnRJpAIzNpbYrXLzPrcx8tKfMinjw6Zg7l8VnCXz8J8D0K89vIGRnhc7SoDuTwt00p+9ooZ2lCw7h0ZtjBBOBBkI5f4mlq+q9mmfJZ609B2hTr1lmgjfVkNdScCO1idgj/Lp048fSmNNSY2b+QxnYndq1wJdxqo4pJZOUf5R151ChZjU=; bm_sv=C468E9DD01AC18A2557C05DC08805F8C~/BhSGP9eChKuEoZMyuvzkJzy5s+IIyKF83FvVXcswxcnMmbBXrFTniyDqudlFdCJjxS0qhxsLL4tYp4iiQQcyGjCQVrfXiuiUtwzFaLwvAwg414CD75DNxi1C7oxxeyMoL49fZpYDVju75aOWH0d9qBPJNPmfbh3IARRQUazsws=; bm_sz=F2A7C76285BC5A37F8A20A4275638F79~YAAQt106F6qbsK19AQAAiusHsw5oqIVuTN1anjq37XuCoAAjoktLOK36CUJAEYq04Am6CbOMvvTAEtv0kF4+AK5Gpmu0YhiQXy6CW8unwmEs1SJ4dh4L+0v4Szio4uiurNJE/FH9pfGwIPpB8nRDx1TgQIudcPIOqK93j60sdrY4A/sXKLv3rxrp7eKqwt88Svl9LX7e1FPWZN4b/X0dm6iLeMep0PxkrYudh8wUM4dMj57yDtsnSHYNQ8u7zQve/NCyimsLpzd+mEgiJZ9LXhhQ2kTOCzmNw8VrzbqzutwnUA==~3425330~3618374; fdx_cbid=28495262151639386180002810233761; level=test; siteDC=wtc",
    ],
]);

$response = curl_exec($curl);

curl_close($curl);
#response decoded in Array
$jsonn = json_decode($response, true);

#Access Token
$accessToken = $jsonn["access_token"];

#-------------------------Check Conddition--------------------------------#

$sql = "SELECT * FROM `warehouse` WHERE date BETWEEN $timeGap AND $timestamp ";
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
    $wooId = $row["woo_id"];
    $dbTrackStatus = $row["status"];

    echo "<br>";

    if ($dbTrackStatus != "delivered" && $dbTrackStatus != "completed") {
        if (strpos($dbTracking, "1Z") === 0) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://onlinetools.ups.com/track/v1/details/$dbTracking",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "Username: jeffwang86",
                    "AccessLicenseNumber: 0DACC8EB7D810072",
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "password: Wang~78985200",
                ],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);
            $UPS_data = json_decode($response);

            unset($StatusInWords);
            $StatusInWords = "";

            #store status of order in variable
            $StatusInWords =
                $UPS_data->trackResponse->shipment[0]->package[0]->activity[0]
                    ->status->description;
            $Status =
                $UPS_data->trackResponse->shipment[0]->package[0]->activity[0]
                    ->status->type;
            $Statuus =
                $UPS_data->trackResponse->shipment[0]->package[0]->activity[0]
                    ->status->type;
            $StatusInWordds =
                $UPS_data->trackResponse->shipment[0]->package[0]->activity[0]
                    ->status->description;
                    
                    
                    
                            echo "$wooId.'--'.$StatusInWords.'--'.$Status";

            #Condition to check the woo mapping field
           /* if (strpos($Status, "I") !== false) {
                $StatusInWords = "picked";
            } elseif (strpos($StatusInWords, "Out for Delivery") !== false) {
                $StatusInWords = "out-delivery";
            }
            elseif (strpos($StatusInWords, "Out For Delivery Today") !== false) {
                $StatusInWords = "out-delivery";
            }
            elseif (strpos($StatusInWords, "Delivered") !== false) {
                $StatusInWords = "delivered";
            }
            elseif (strpos($StatusInWords, "DELIVERED") !== false) {
                $StatusInWords = "delivered";
            }*/
            
            
             if (strpos($Status, "I") !== false) {
                $StatusInWords = "picked";
            } 
            elseif (strpos($Status, "P") !== false) {
                $StatusInWords = "picked";
            }
            elseif (strpos($Status, "O") !== false) {
                $StatusInWords = "out-delivery";
            }

            elseif (strpos($StatusInWords, "Delivered") !== false) {
                $StatusInWords = "delivered";
            }
            elseif (strpos($StatusInWords, "DELIVERED") !== false) {
                $StatusInWords = "delivered";
            }
            
            
            
        } else {
            # Curl To get the shipping details from the fedex!!!!!!

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://apis.fedex.com/track/v1/trackingnumbers",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>
                    '{
        "trackingInfo": [
        {
        "trackingNumberInfo": {
        "trackingNumber": "' .
                    $dbTracking .
                    '"
        }
        }
        ],
        "includeDetailedScans": true
        }',
                CURLOPT_HTTPHEADER => [
                    "x-customer-transaction-id: ",
                    "Content-Type: application/json",
                    "x-locale: en_US",
                    "authorization: Bearer $accessToken",
                ],
            ]);

            $response = curl_exec($curl);

            curl_close($curl);

            #response decoded in Array
            $json = json_decode($response, true);
            //print_r($json);

            #consignment number same as warehouse
            $FedConsign =
                $json["additionalTrackingInfo"]["packageIdentifiers"][0][
                    "type"
                ]["values"][0];
            if (!empty($json)) {
                #Status of the shipping in short
                $Status =
                    $json["output"]["completeTrackResults"][0][
                        "trackResults"
                    ][0]["latestStatusDetail"]["code"];

                # Status of the shipping in full words
                $StatusInWords =
                    $json["output"]["completeTrackResults"][0][
                        "trackResults"
                    ][0]["latestStatusDetail"]["statusByLocale"];

                $StatusInWordsss =
                    $json["output"]["completeTrackResults"][0][
                        "trackResults"
                    ][0]["latestStatusDetail"]["statusByLocale"];
            }



            #Condition to check the woo mapping field
            if (strpos($StatusInWords, "In transit") !== false) {
                $StatusInWords = "picked";
            } elseif (
                strpos($StatusInWords, "On FedEx vehicle for delivery") !==
                false
            ) {
                $StatusInWords = "out-delivery";
            }
            
            elseif (strpos($StatusInWords, "Out for Delivery") !== false) {
                $StatusInWords = "out-delivery";
            } elseif (strpos($StatusInWords, "Delivered") !== false) {
                $StatusInWords = "delivered";
            }
        }
    }
    
            
            
            
    if(!empty($Status))
    {
        
        # Checking the Id in database
$sql = "SELECT * FROM `warehouse` WHERE `ship_no` = '$dbTracking'";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
         while ($row = mysqli_fetch_array($res)) {
             echo '<pre>';
          //print_r($row);
            #Shipping Tracking number
             $dbOrderShip=$row['ship_no'];
             
             
             $wooId = $row["woo_id"];

            #Status from the Database
             $dbStatus=$row['status'];
             

            
             
    }}}
        
        
       // echo "$wooId.'--'.$StatusInWords.'--'.$Status";
        
        
         if($dbStatus != 'delivered' && $dbStatus != "completed" )
         {
        
            if($dbStatus != $StatusInWords && $StatusInWords != "" )
            
                {
                    

                       # Inserting the data in databse------------
                 $sqlquery = "UPDATE `warehouse` SET `status` = '$StatusInWords' ,`dupStatus` = '1' WHERE `warehouse`.`ship_no` = '$dbOrderShip'";
                if ($conn->query($sqlquery) === TRUE)
                    {
                        echo "record inserted successfully";
                    }
                   
                    
                    
                }
    }
        
    }
    
               
             
    $StatusInWords='';
$Status='';
$dbTracking='';
    
}
