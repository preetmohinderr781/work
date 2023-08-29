<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
#Calling the autoload file
require 'smtp/vendor/autoload.php';


function sendingEmails($consignment,$wooId)
{
                             $LogDate=date("jS  F Y h:i:s A");
 //file_put_contents('sendemailfunctCAlled.txt',$LogDate.'--'.$response.PHP_EOL,FILE_APPEND);

#SMTP Variables
#Variables For the mail to be sent as notification ######

#mail Sender
$sender = 'promowares86@gmail.com';

#mail Sender Name
$senderName = 'Jeff';

#mail recipient
$recipient = 'jeff@promowares.com';

#UserName SMTP
$usernameSmtp = 'AKIA4XJY64523WF6GJ42';

#UserPassword SMTP
$passwordSmtp = 'BFAh+vcbbbU9JmVZ5F/XsSBvtFuUHc//FC2aRb1wjqff';

#configurationSet
$configurationSet = 'ConfigSet';

# AWS Host Name
$host = 'smtp.exmail.qq.com';

#Aws Port
$port = 465;



#OPEN4x APP KEY
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();

                    #Get list inbound api request headers
                			 $jsonDataGetInbound="{
                	'consignment_no': '$consignment',
                	'page_no': 1,
                	'page_size': 10
                }";
					# MD5 Encryption for generating the signature
		$rawSignatureSKU= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.getlisttimestamp'.$timestamp.'v1.0'.$jsonDataGetInbound.'e4b362a3-7977-4699-82f7-31fadd33cf95';

         #md5 encrypt
		 $signatureSKU=md5($rawSignatureSKU);

		 #Inventory API
		$curl = curl_init();

		  curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.outbound.getlist",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>"$jsonDataGetInbound",
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
		  ),
		));

		#Execute curl
		$response = curl_exec($curl);

		#json to array
		$InBoundListData=json_decode($response);
        $DataArrayInBoundList =$InBoundListData->data->data;
       // print_r($DataArrayInBoundList);
        foreach($DataArrayInBoundList as $keyy)
        {
           echo $CustNote=$keyy->remark;
           echo $total_weight=$keyy->total_weight;
           $weight=$total_weight/1000;
           echo $consignment_no=$keyy->consignment_no;

              echo $addressUndefined=$keyy->street;
        }



#Condition to check Whether note is empty
if(!empty($CustNote))
    {


        echo 'hello';
        #Send the mail to Adm
        // The subject line of the email
            $subject = "Order No.:$wooId, 含备注，查看已取消出库单.";

                                    $bodyText =  "Order No.:$wooId, 含备注，查看已取消出库单.";

                                    $bodyHtml = "<p>Order No.:$wooId, 含备注，查看已取消出库单.</p>";
            $headers = "Content-Type: text/html; charset=UTF-8";
            $mail = new PHPMailer(true);

            try {
                // Specify the SMTP settings.
                //$mail->isSMTP();
                $mail->setFrom($sender, $senderName);
                $mail->Username   = $usernameSmtp;
                $mail->Password   = $passwordSmtp;
                $mail->Host       = $host;
                $mail->Port       = $port;
                $mail->SMTPAuth   = true;
                $mail->SMTPSecure = 'tls';
                $mail->CharSet="UTF-8";
                $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);

                // Specify the message recipients.
                $mail->addAddress($recipient);
                // You can also add CC, BCC, and additional To recipients here.

                // Specify the content of the message.
                $mail->isHTML(true);
                $mail->Subject    = $subject;
                $mail->Body       = $bodyHtml;
                $mail->AltBody    = $bodyText;
                $mail->Send();
                echo "Email sent!" , PHP_EOL;
            } catch (phpmailerException $e) {

                echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
            } catch (Exception $e) {
                echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
            }
        #----------------End Mail code-------------------#

    }



}


function addCancelOrder($wooId,$jsonDataFunc)
{

#Variables For the mail to be sent as notification ######

#mail Sender
$sender = 'promowares86@gmail.com';

#mail Sender Name
$senderName = 'Jeff';

#mail recipient
$recipient = 'jeff@promowares.com';

#UserName SMTP
$usernameSmtp = 'AKIA4XJY64523WF6GJ42';

#UserPassword SMTP
$passwordSmtp = 'BFAh+vcbbbU9JmVZ5F/XsSBvtFuUHc//FC2aRb1wjqff';

#configurationSet
$configurationSet = 'ConfigSet';

# AWS Host Name 
$host = 'smtp.exmail.qq.com';

#Aws Port
$port = 465;

#OPEN4x APP KEY 
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret 
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();

             
#------------------------Create Order in WareHouse----------------#

# MD5 Encryption for generating the signature
                $rawSignature= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.createtimestamp'.$timestamp.'v1.0'.$jsonDataFunc.'e4b362a3-7977-4699-82f7-31fadd33cf95';
                
                 $rawSignature;
                 $signature=md5($rawSignature);
            
                
#------------------------Starting of the API CURL------------------------------#

              $curl = curl_init();
                
                curl_setopt_array($curl, array(
                  CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signature&method=fu.wms.outbound.create",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>"$jsonDataFunc",
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
                  ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                echo $response;
                $error=json_decode($response);

               
                $resData=$error->data;
               
                $errorrs=$error->errors;

                $consignment_no=$resData->consignment_no;
#-------------------------------------end---------------------##
        sleep(50);

        #cancel the outbound order API
        $jsonDataCancel="{
    
                                'consignment_no': '$consignment_no',
                            	'cancel_type':'OT',
                            	'cancel_remark':'TEST'
                            }";
                            
            # MD5 Encryption for generating the signature
            
            $rawSignatureSKU= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.canceltimestamp'.$timestamp.'v1.0'.$jsonDataCancel.'e4b362a3-7977-4699-82f7-31fadd33cf95';
             $signatureSKU=md5($rawSignatureSKU);
            
            
            #---------------Starting of the API CURL--------------#
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
              CURLOPT_POSTFIELDS =>"$jsonDataCancel",
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
              ),
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            $data=json_decode($response);
            echo '<pre>';
                        print_r($data);
                         $LogDate=date("jS  F Y h:i:s A");
                         
 file_put_contents('Order_cancel_overweig_log.txt',$LogDate.'--'.$wooId.'--'.$response.PHP_EOL,FILE_APPEND);
 
    $subject = "Order No.:$wooId, 订单超重，需手动处理，查看已取消出库单";

                        $bodyText =  "Order No.:$wooId, 订单超重，需手动处理，查看已取消出库单";
                        
                        $bodyHtml = "<p>Order No.:$wooId, 订单超重，需手动处理，查看已取消出库单</p>";
                        
                        $mail = new PHPMailer(true);
                        
                        try {
                        
                            $mail->setFrom($sender, $senderName);
                            $mail->Username   = $usernameSmtp;
                            $mail->Password   = $passwordSmtp;
                            $mail->Host       = $host;
                            $mail->Port       = $port;
                            $mail->SMTPAuth   = true;
                            $mail->SMTPSecure = 'tls';
                            $mail->CharSet="UTF-8";
                            $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);
                        
                        
                            $mail->addAddress($recipient);
                            // You can also add CC, BCC, and additional To recipients here.
                        
                            // Specify the content of the message.
                            $mail->isHTML(true);
                            $mail->Subject    = $subject;
                            $mail->Body       = $bodyHtml;
                            $mail->AltBody    = $bodyText;
                            $mail->Send();
                            
                            echo "Email sent!" , PHP_EOL;
                        } catch (phpmailerException $e) {
                            echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
                        } catch (Exception $e) {
                            echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
                        }
    
}

function unDefinedAdd($consignment,$wooId)
{
                             $LogDate=date("jS  F Y h:i:s A");
 //file_put_contents('sendemailfunctCAlled.txt',$LogDate.'--'.$response.PHP_EOL,FILE_APPEND);

 sleep(60);
#OPEN4x APP KEY 
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret 
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();

                    #Get list inbound api request headers
                			 $jsonDataGetInbound="{
                	'consignment_no': '$consignment',
                	'page_no': 1,
                	'page_size': 10
                }";
					# MD5 Encryption for generating the signature
		$rawSignatureSKU= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.getlisttimestamp'.$timestamp.'v1.0'.$jsonDataGetInbound.'e4b362a3-7977-4699-82f7-31fadd33cf95';

         #md5 encrypt
		 $signatureSKU=md5($rawSignatureSKU);
		
		 #Inventory API
		$curl = curl_init();

		  curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.outbound.getlist",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>"$jsonDataGetInbound",
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
		  ),
		));
        
		#Execute curl
		$response = curl_exec($curl);
        
		#json to array
		$InBoundListData=json_decode($response);	
        $DataArrayInBoundList =$InBoundListData->data->data;
       // print_r($DataArrayInBoundList);
        foreach($DataArrayInBoundList as $keyy)
        {
           echo $CustNote=$keyy->remark;
           echo $total_weight=$keyy->total_weight;
           $weight=$total_weight/1000;
           echo $consignment_no=$keyy->consignment_no;
           
              echo $addressUndefined=$keyy->street;
        }
        
        
        
        
        
         $addressUndefinedLower = strtolower($addressUndefined);
  
if(strpos($addressUndefinedLower, "undefined")!== false || strpos($addressUndefinedLower, "po box")!== false) {
            
 
            

       #cancel the outbound order API
        $jsonDataCancel="{
    
                                'consignment_no': '$consignment_no',
                            	'cancel_type':'AP',
                            	'cancel_remark':'TEST'
                            }";
                            
            # MD5 Encryption for generating the signature
            
            $rawSignatureSKU= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.canceltimestamp'.$timestamp.'v1.0'.$jsonDataCancel.'e4b362a3-7977-4699-82f7-31fadd33cf95';
             $signatureSKU=md5($rawSignatureSKU);
            
            
            #---------------Starting of the API CURL--------------#
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
              CURLOPT_POSTFIELDS =>"$jsonDataCancel",
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
              ),
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            
            $data=json_decode($response);
            echo '<pre>';
                        print_r($data);
                        
            $LogDate=date("jS  F Y h:i:s A");

           // file_put_contents('addressUndefined.txt',$LogDate.'--'.$response.PHP_EOL,FILE_APPEND);     
        }


}

function sendDataToWoo($consignment,$wooId)
{

    echo 'Functioncalledcalled';

     #OPEN4x APP KEY
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();



                    #Get list inbound api request headers
                			 $jsonDataGetInbound="{
                	'consignment_no': '$consignment',
                	'page_no': 1,
                	'page_size': 10
                }";
					# MD5 Encryption for generating the signature
		$rawSignatureSKU= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.getlisttimestamp'.$timestamp.'v1.0'.$jsonDataGetInbound.'e4b362a3-7977-4699-82f7-31fadd33cf95';

         #md5 encrypt
		 $signatureSKU=md5($rawSignatureSKU);

		 #Inventory API
		$curl = curl_init();

		  curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.outbound.getlist",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>"$jsonDataGetInbound",
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
		  ),
		));

		#Execute curl
		$response = curl_exec($curl);

		#json to array
		$InBoundListData=json_decode($response);
        $DataArrayInBoundList =$InBoundListData->data->data;
        echo '<pre>';
       // print_r($DataArrayInBoundList);
        foreach($DataArrayInBoundList as $keyy)
        {
            $consignment=$keyy->consignment_no;
           $logistics_product_code=$keyy->logistics_product_code;
         echo  $shipping_no=$keyy->shipping_no;
         $status=$keyy->status;
        }

        if($logistics_product_code == "F134")
        {
            $logisticShipping="FedEx Ground";
        }
        elseif($logistics_product_code == "F132")
        {
            $logisticShipping="FedEx";
        }
        elseif($logistics_product_code == "F126")
        {
            $logisticShipping="UPS";
        }
        elseif($logistics_product_code == "F123")
        {
            $logisticShipping="UPS";
        }
        elseif($logistics_product_code == "F124")
        {
            $logisticShipping="UPS";
        }


        # Adding the Tracking data to the Woocommerce

if($status == 'S' || $status == 'P' || $status == 'C' )
{
    //////////////////////////////////////////////////////////////

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders/$wooId/shipment-trackings?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_POSTFIELDS =>'',
  CURLOPT_HTTPHEADER => array(
    'ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125: cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276',
    'Content-Type: application/json',
    'Cookie: PHPSESSID=4964214755b8161e9789543486e0d3d6; designer_session_id=4964214755b8161e9789543486e0d3d6'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
 $response;
$decode=json_decode($response);
echo '<pre>';
foreach($decode as $checkTrack)
{

    $tracking_number=$checkTrack->tracking_number;

}
$tracking_numberr[]=$tracking_number;
 //print_r($tracking_numberr);



if(!in_array($shipping_no,$tracking_numberr))
{

    ////////////////////////////////////////////////////////////////////////
  echo   $consignment=$keyy->consignment_no;
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://www.promowares.com/wp-json/wc-ast/v3/orders/$wooId/shipment-trackings?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                            		"tracking_provider": "'.$logisticShipping.'",
                            		"tracking_number": "'.$shipping_no.'"
                            }
                            ',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: PHPSESSID=0408cd286afe002c9dccea4a62effd17; designer_session_id=0408cd286afe002c9dccea4a62effd17'
              ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
           // echo $response;
            $LogDate=date("jS  F Y h:i:s A");
// file_put_contents('sendDataToWoo_log.txt',$LogDate.$response.PHP_EOL,FILE_APPEND);

}
}}

function fedexStatusToWoo($consignment,$wooId)
{

    echo 'Function 55555';

    #OPEN4x APP KEY
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();



#database details
$servername = "localhost";
$username = "sql_autoship_pro";
$password = "y8YzALPXH6edm233";

// Create connection
$conn = new mysqli($servername, $username, $password, "sql_autoship_pro");

                    #Get list inbound api request headers
                			 $jsonDataGetInbound="{
                	'consignment_no': '$consignment',
                	'page_no': 1,
                	'page_size': 10
                }";
					# MD5 Encryption for generating the signature
		$rawSignatureSKU= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.getlisttimestamp'.$timestamp.'v1.0'.$jsonDataGetInbound.'e4b362a3-7977-4699-82f7-31fadd33cf95';

         #md5 encrypt
		 $signatureSKU=md5($rawSignatureSKU);

		 #Inventory API
		$curl = curl_init();

		  curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.outbound.getlist",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>"$jsonDataGetInbound",
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
		  ),
		));

		#Execute curl
		$response = curl_exec($curl);

		#json to array
		$InBoundListData=json_decode($response);
        $DataArrayInBoundList =$InBoundListData->data->data;
        print_r($DataArrayInBoundList);
        foreach($DataArrayInBoundList as $keyy)
        {

           $consignment=$keyy->consignment_no;
           $customer_code=$keyy->customer_code;
           $shipping_no=$keyy->shipping_no;

        }

        $sql = "SELECT * FROM `warehouse`";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
        while ($roww = mysqli_fetch_array($res)) {
             $project[] = $roww;
         }
    }
    }
             foreach ($project as $row)
             {

              $wooCommerceId[]=$row['woo_id'];
             }
        if(in_array($wooId,$wooCommerceId)){

    $sqlquery = "UPDATE `warehouse` SET `ship_no` = '$shipping_no',`con_no`= '$consignment',`date`= '$timestamp' WHERE `warehouse`.`woo_id` = '$wooId'";
                    if ($conn->query($sqlquery) === TRUE) {
                       echo "record updated successfully";
        }else
        {

        # Inserting the data in databse------------
$sqlquery = "INSERT INTO warehouse VALUES
    (NULL, '$consignment', '$shipping_no','$wooId','','$timestamp','')";
if ($conn->query($sqlquery) === TRUE) {
    echo "record inserted successfully";
    #Put Data in text file


}
        }
}
}

function statusChange($consignment,$wooId)
{

         #OPEN4x APP KEY
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret
$appSecret="e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version="1.0";

#Timestamp
$timestamp=time();



                    #Get list inbound api request headers
                			 $jsonDataGetInbound="{
                	'consignment_no': '$consignment',
                	'page_no': 1,
                	'page_size': 10
                }";
					# MD5 Encryption for generating the signature
		$rawSignatureSKU= 'app_key'.$appKey.'formatjsonmethodfu.wms.outbound.getlisttimestamp'.$timestamp.'v1.0'.$jsonDataGetInbound.'e4b362a3-7977-4699-82f7-31fadd33cf95';

         #md5 encrypt
		 $signatureSKU=md5($rawSignatureSKU);

		 #Inventory API
		$curl = curl_init();

		  curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.outbound.getlist",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>"$jsonDataGetInbound",
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
		  ),
		));

		#Execute curl
		$response = curl_exec($curl);

		#json to array
		$InBoundListData=json_decode($response);
        $DataArrayInBoundList =$InBoundListData->data->data;
        echo '<pre>';
       // print_r($DataArrayInBoundList);
        foreach($DataArrayInBoundList as $keyy)
        {
            $consignment_no=$keyy->consignment_no;
           $logistics_product_code=$keyy->logistics_product_code;
         echo  $shipping_no=$keyy->shipping_no;
         $status=$keyy->status;
        }

        if($logistics_product_code == "F134")
        {
            $logisticShipping="FedEx Ground";
        }
        elseif($logistics_product_code == "F132")
        {
            $logisticShipping="FedEx";
        }
        elseif($logistics_product_code == "F126")
        {
            $logisticShipping="UPS";
        }
        elseif($logistics_product_code == "F123")
        {
            $logisticShipping="UPS";
        }
        # Adding the Tracking data to the Woocommerce

if($status == 'S' || $status == 'P' || $status == 'C' )
{


    #send status to the woocommerce from the fedex
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders/$wooId?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
  "status": "shipping-label"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Cookie: PHPSESSID=062ffc1da5bc7894a034e0e33ca941c4; designer_session_id=062ffc1da5bc7894a034e0e33ca941c4'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

 $LogDate=date("jS  F Y h:i:s A");
 //file_put_contents('statusChange_log.txt',$LogDate.$response.PHP_EOL,FILE_APPEND);

# End of the curl

}
}

function InsertToDb($consignment,$wooId)
{

    #database details
    $servername = "localhost";
    $username = "sql_autoship_pro";
    $password = "y8YzALPXH6edm233";

    // Create connection
    $conn = new mysqli($servername, $username, $password, "sql_autoship_pro");

 # Inserting the data in databse------------
$sqlquery = "INSERT INTO all_Data_warehouse VALUES
    (NULL, '$consignment','$wooId')";
if ($conn->query($sqlquery) === TRUE) {
    echo "record inserted successfully";
}


}
function Cancellll($consignment)
{

    #database details
    $servername = "localhost";
    $username = "sql_autoship_pro";
    $password = "y8YzALPXH6edm233";

    // Create connection
    $conn = new mysqli($servername, $username, $password, "sql_autoship_pro");

#query to Close Or ON the flow
$sql = "SELECT * FROM `switch`";
if ($res = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($res) > 0) {
         while ($row = mysqli_fetch_array($res)) {
             $CheckingValue=$row[1];

         }
    }
}

    if($CheckingValue == "Yes")
                        {



     sleep(30);
    $jsonDataInventory='{

    "consignment_no": "'.$consignment.'",
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
 //file_put_contents('cancelll_log.txt',$LogDate.$response.PHP_EOL,FILE_APPEND);

$data=json_decode($response);
echo '<pre>';

print_r($data);
}
}

function shippingLabel($consignment,$wooId)
{

}

function paypalTracking($wooId,$shippingID)
{

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


 echo '<pre>';


$tokenn=json_decode($response,TRUE);
echo $accessToken=$tokenn['access_token'];


#----------------Getting Tracking info from the Woo---------------#
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.promowares.com/wp-json/wc-ast/v3/orders/$wooId/shipment-trackings?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125: cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276',
    'Cookie: PHPSESSID=b58d8b6cd98c06b729b920b73793770c; designer_session_id=b58d8b6cd98c06b729b920b73793770c'
  ),
));

$response = curl_exec($curl);

curl_close($curl);

$data=json_decode($response);

$key=$data[0];
echo  $tracking_provider=$key->tracking_provider;
echo $tracking_number=$key->tracking_number;


#-----------------


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
      "transaction_id": "'.$shippingID.'",
    "tracking_number": "'.$tracking_number.'",
    "status": "SHIPPED",
    "carrier": "'.$tracking_provider.'"
    }
     ]
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    "Authorization: Bearer $accessToken",
    'Cookie: l7_az=ccg13.slc'
  ),
));

$response = curl_exec($curl);


$datta=json_decode($response);

 $LogDate=date("jS  F Y h:i:s A");
 file_put_contents('paypal_log.txt',$LogDate.$response.PHP_EOL,FILE_APPEND);


print_r($datta);


}


function checkStockAvaliable($customer_code,$customerOrderNumber,$Consignment_type,$logProdCode,$wooId,$customerNote,$country,$state,$city,$ZipCode,$street,$phone,$company,$lastName,$firstName,$skuCode,$quantityFinal)
{
      //  file_put_contents('SKUUu.txt', $LogDate . $wooId . $available_stock.'AVALIABLE_STOCK_IS_NOT_ENOUGH Function called'.$response . PHP_EOL, FILE_APPEND);
#OPEN4x APP KEY
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret
$appSecret = "e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version = "1.0";

#Timestamp
$timestamp = time();



$jsonDataInventory = "{
			        'warehouse_code': '';
					'lstsku': [
						'$skuCode',
					]
				}";

                        # MD5 Encryption for generating the signature
                        $rawSignatureSKU =
                            "app_key" .
                            $appKey .
                            "formatjsonmethodfu.wms.inventory.gettimestamp" .
                            $timestamp .
                            "v1.0" .
                            $jsonDataInventory .
                            "e4b362a3-7977-4699-82f7-31fadd33cf95";

                        #md5 encrypt
                        $signatureSKU = md5($rawSignatureSKU);

                        #Inventory API
                        $curl = curl_init();

                        curl_setopt_array($curl, [
                            CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.inventory.get",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => "$jsonDataInventory",
                            CURLOPT_HTTPHEADER => [
                                "Content-Type: application/json",
                                "Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676",
                            ],
                        ]);

                        #Execute curl
                        $response = curl_exec($curl);

                        #json to array
                        $InventoryData = json_decode($response);
                        $DataArrayInventory = $InventoryData->data->data;
                        echo '<pre>';
                      //  print_r($InventoryData);
                        
                            echo  $count =count($DataArrayInventory);
                            
                              foreach ($DataArrayInventory as $DataArrayInvent) {
                            echo "<pre>";
                            print_r($DataArrayInvent);
                            $stock_quality = $DataArrayInvent->stock_quality;
                            $available_stock =
                                $DataArrayInvent->available_stock;
                            $pending_stock = $DataArrayInvent->pending_stock;
                            $onway_stock = $DataArrayInvent->onway_stock;
                             
                            
                            if($count > 1)
                            {
                                print_r($available_stock);
                               if($available_stock != 0)
                               {
                           echo     $WarehouseCode =
                                    $DataArrayInvent->warehouse_code;
                               }
                            }
                        }
                        
                       
                        
                        
                         echo $jsonData = "{
                    'customer_code': '$customer_code',
                	'ref_no': '$customerOrderNumber',
                	'from_warehouse_code': '$WarehouseCode',
                	'consignment_type':'$Consignment_type',
                	'logistics_service_info': {
                		'logistics_product_code': '$logProdCode'
                	},
                	'shop_id': 'promowares',
                	'sales_no': '$wooId',
                	'currency': 'USD',
                	'remark': '$customerNote',
                	'oconsignment_desc': {
                		'country': '$country',
                		'state': '$state',
                		'city': '$city',
                		'post_code': $ZipCode,
                		'street': '$street',
                		'phone' : '$phone',
                		'company': '$company',
                		'last_name': '$lastName',
                		'first_name': '$firstName'
                	},
                	'oconsignment_sku': [{
                		'sku_code': '$skuCode',
                		'qty': '$quantityFinal',
                		'stock_quality': 'G'
                	}]
                }";
                        
                        
                     $rawSignature =
                            "app_key" .
                            $appKey .
                            "formatjsonmethodfu.wms.outbound.createtimestamp" .
                            $timestamp .
                            "v1.0" .
                            $jsonData .
                            "e4b362a3-7977-4699-82f7-31fadd33cf95";

                        $rawSignature;
                        $signature = md5($rawSignature);

                        #------------------------Starting of the API CURL------------------------------#
                        $curl = curl_init();

                        curl_setopt_array($curl, [
                            CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signature&method=fu.wms.outbound.create",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => "$jsonData",
                            CURLOPT_HTTPHEADER => [
                                "Content-Type: application/json",
                                "Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676",
                            ],
                        ]);

                        $response = curl_exec($curl);

                        curl_close($curl);
                        echo $response;
                        $LogDate = date("jS  F Y h:i:s A");
                        file_put_contents(
                            "response1_log.txt",
                            $LogDate . $wooId . $response . PHP_EOL,
                            FILE_APPEND
                        );

                        $error = json_decode($response);
                        print_r($error);   
                        
                        
                        
   // file_put_contents('SKUUu.txt', $LogDate . $wooId . $available_stock.'AVALIABLE_STOCK_IS_NOT_ENOUGH tttttttttttttttt'.$response . PHP_EOL, FILE_APPEND);
    
}


#----------------End Getting Tracking info from the Woo---------------#
