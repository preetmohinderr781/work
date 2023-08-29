<?php

function Create_shipment($jsonnData,$ZohoCrmIdID,$token)
{

    #include details
        require ('details.php');
        
        #include database
        require ('database.php');


  echo $jsonnData;
   file_put_contents('JsonencodedData.txt', $LogDate . $ZohoCrmIdID . $jsonnData . PHP_EOL, FILE_APPEND);
  
  # MD5 Encryption for generating the signature
            $rawSignature = 'app_key' . $appKey . 'formatjsonmethodfu.wms.outbound.createtimestamp' . $timestamp . 'v1.0' . $jsonnData . 'e4b362a3-7977-4699-82f7-31fadd33cf95';

            $rawSignature;
            $signature = md5($rawSignature);
    
              
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
                        CURLOPT_POSTFIELDS => "$jsonnData",
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
                        ) ,
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    echo $response;

                    $LogDate = date("jS  F Y h:i:s A");
                    file_put_contents('response2.txt', $LogDate . $ZohoCrmIdID . $response . PHP_EOL, FILE_APPEND);

                    $error = json_decode($response);
                    print_r($error);
                    $result = $error->result;

                    $resData = $error->data;
                    $errorrs = $error->errors;
                    foreach ($errorrs as $errr)
                    {
                        $errors = $errr->error_msg;
                    }
                    $msg = $error->msg;
                    $consignment = $resData->consignment_no;
                    if ($result == "1")
                    { 
                        
                        Cancellll($consignment);
                        unDefinedAdd($consignment,$ZohoCrmIdID);
                        
                        #Sending the information in database to call the functions
                        
                            $sqlDBShip = "INSERT INTO `ZohoFunctionShip` (`id`, `con_no`, `zohoId`, `status`, `timestamp`) VALUES (NULL, '$consignment', '$ZohoCrmIdID', '1','$timeDelayShip')";
                            $res = mysqli_query($conn, $sqlDBShip); 
                            
                            $sqlDBLAbel = "INSERT INTO `ZohoFunctionLabel` (`id`, `con_no`, `zohoId`, `status`, `timestamp`) VALUES (NULL, '$consignment', '$ZohoCrmIdID', '1','$timeDelayLabel')";
                            $res = mysqli_query($conn, $sqlDBLAbel); 
                            
                        sendingEmails($consignment, $ZohoCrmIdID);
                        fedexStatusToWoo($consignment, $ZohoCrmIdID, $token);

                    }
                    if ($result == "0")
                    {
                        #send Overweight Notification by Email
                        $subject = "Order No.:$ZohoCrmIdID, 其他错误，需手动处理，错误内容请查看邮件正文代码.";

                        $bodyText = "Order No.:$ZohoCrmIdID, Error:$errors ,Message: $msg .需手动处理";

                        $bodyHtml = "Order No.:$ZohoCrmIdID, Error:$errors ,Message: $msg .需手动处理";

                        $mail = new PHPMailer(true);

                        try
                        {

                            $mail->setFrom($sender, $senderName);
                            $mail->Username = $usernameSmtp;
                            $mail->Password = $passwordSmtp;
                            $mail->Host = $host;
                            $mail->Port = $port;
                            $mail->SMTPAuth = true;
                            $mail->SMTPSecure = 'tls';
                            $mail->CharSet = "UTF-8";
                            $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);

                            $mail->addAddress($recipient);
                            // You can also add CC, BCC, and additional To recipients here.
                            // Specify the content of the message.
                            $mail->isHTML(true);
                            $mail->Subject = $subject;
                            $mail->Body = $bodyHtml;
                            $mail->AltBody = $bodyText;
                            $mail->Send();

                            echo "Email sent!", PHP_EOL;
                        }
                        catch(phpmailerException $e)
                        {
                            echo "An error occurred. {$e->errorMessage() }", PHP_EOL; //Catch errors from PHPMailer.
                            
                        }
                        catch(Exception $e)
                        {
                            echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
                            
                        }
                    }

                
    
    
    
}