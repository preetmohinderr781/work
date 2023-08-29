<?php
require ('database.php');

$timestamp = time();
$timeGapUpd = date(strtotime("-2 days"));

$now = new DateTime();
$begin = new DateTime('17:00:00');
$end = new DateTime('20:00:00');

if ($now >= $begin && $now <= $end)
{

    $sql = "SELECT * FROM `zohoWarehouse`WHERE `dupStatus` = '2' ORDER BY time_update ASC LIMIT 1";
    if ($res = mysqli_query($conn, $sql))
    {
        if (mysqli_num_rows($res) > 0)
        {
            while ($roww = mysqli_fetch_array($res))
            {
                $projectt[] = $roww;
            }
        }
    }
    foreach ($projectt as $row)
    {
        //print_r($projectt);
        $idd = $row['id'];
        $dbconsign = $row['con_no'];
        $dbTracking = $row['ship_no'];
        $zoho_id = $row['zoho_id'];
        $dbTrackStatus = $row['status'];
        $dbtime = $row['time_update'];

        if ($dbTrackStatus == "delivered" && $dbtime <= $timeGapUpd)
        {

            $sqlquery = "UPDATE `zohoWarehouse` SET status = 'completed', time_update = '$timestamp',`dupStatus` = '0' WHERE `zohoWarehouse`.`id` = $idd";

            if ($conn->query($sqlquery) === true)
            {

                #send status to the woocommerce from the fedex
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
                    CURLOPT_POSTFIELDS => '{
                  "data": [
                    {
                      "field55": "' . $dbTrackStatus . '",
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
                    ) ,
                ));

                $response = curl_exec($curl);

            }
        }
    }
    $LogDate = date("jS  F Y h:i:s A");
    file_put_contents('response1_CompDel.txt', $LogDate . $zoho_id . PHP_EOL, FILE_APPEND);

    #Delete the completed Record
    

    $sql = "SELECT * FROM `zohoWarehouse`";
    if ($res = mysqli_query($conn, $sql))
    {
        if (mysqli_num_rows($res) > 0)
        {
            while ($roww = mysqli_fetch_array($res))
            {
                $projectss[] = $roww;
            }

        }

    }

    foreach ($projectss as $row)
    {
        //print_r($projectss);
        $idd = $row['id'];
        $dbconsign = $row['con_no'];
        $trackkinngg = $row['ship_no'];
        $zoho_id = $row['zoho_id'];
        $dbTrackStatus = $row['status'];
        $dbActualTime = $row['date'];
        $dbUPDActualTime = $row['time_update'];

        $timeGapUpd_hours = date(strtotime("-3 hours"));
        $timeGapUpdOneDay = date(strtotime("-2 days"));
        if ($dbTrackStatus == "completed" && $dbUPDActualTime <= $timeGapUpd_hours || $dbTrackStatus == "Cancelled")

        {

            $sqlquery = "DELETE FROM `zohoWarehouse` WHERE `zohoWarehouse`.`id` = $idd";

            if ($conn->query($sqlquery) === true)
            {
                echo "record deleted successfully";

            }
        }

        elseif ($dbTrackStatus == "processing" && $dbActualTime <= $timeGapUpdOneDay)

        {
            if (empty($trackkinngg))
            {

                $sqlquery = "DELETE FROM `zohoWarehouse` WHERE `zohoWarehouse`.`id` = $idd";

                if ($conn->query($sqlquery) === true)
                {

                }

            }

        }
    }

}

