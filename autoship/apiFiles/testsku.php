<?php
header("Content-Type: text/html");
#Project started on Date - 29/11/2021
#include zones
require "zones.php";
require "database.php";
#include Function File
//require "function.php";

#Recived Data From Webhook
$json = file_get_contents("php://input");

#Put Data in text file
//file_put_contents("Order_test.txt", $json);

#Get Data from the file
 $json1 = file_get_contents("order_test.txt"); 

#Variables For the mail to be sent as notification ######
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

#Calling the autoload file
require "smtp/vendor/autoload.php";

#mail Sender
$sender = "promowares86@gmail.com";

#mail Sender Name
$senderName = "santosh";
#time delay for 300 seconds
$timeDelayShip = strtotime("+200 seconds");

$timeDelayLabel = strtotime("+300 seconds");

#mail recipient
$recipient = "jeff@promowares.com";

#UserName SMTP
$usernameSmtp = "AKIA4XJY64523WF6GJ42";

#UserPassword SMTP
$passwordSmtp = "BFAh+vcbbbU9JmVZ5F/XsSBvtFuUHc//FC2aRb1wjqff";

#configurationSet
$configurationSet = "ConfigSet";

# AWS Host Name
$host = "smtp.exmail.qq.com";

#Aws Port
$port = 465;

#OPEN4x APP KEY
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret
$appSecret = "e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version = "1.0";

#Timestamp
$timestamp = time();

#Decode json Response To Array
$wooData = json_decode($json1);

echo "<pre>";
//print_r($wooData);



#Order Status
$orderStauts = $wooData->status;

#customer Notes
$customerNote = $wooData->customer_note;

$paymentMethod = $wooData->payment_method_title;

$shippingID = $wooData->transaction_id;

echo $wooId = $wooData->number;

if ($orderStauts == "picked") {
    if ($paymentMethod == "PayPal") {
        paypalTracking($wooId, $shippingID);
    }
}

#Checking If the status will be processing then the whole flow starts
if ($orderStauts == "processing") { 
    $sqlquery = "INSERT INTO warehouse VALUES
    (NULL, '', '','$wooId','$orderStauts','$timestamp','','')";
    if ($conn->query($sqlquery) === true) {
        //echo "record inserted successfully";
    }



        #Condition if order status is set on processing
        if ($orderStauts == "processing") {
            #------Create Variable for API Integration--------#
            
            #Consignment Type
            $Consignment_type = "S";

            #Order Created Date
            $dateCreated = $wooData->date_created;

            #woo ID
            $wooId = $wooData->number;

            #Order ID
            $orderId = $wooData->id;

            #Date Format in API
            $newDate = date("dmY", strtotime($dateCreated));

            #Customer order Number
            $customerOrderNumber = "Woo" . $newDate . $orderId;

            #Platform transaction No.
            $Platform_transaction_No = $orderId;

            #Get shipping details
            #First Name of user
            $firstNamee = $wooData->shipping->first_name;
            $firstName = preg_replace("/[^A-Za-z0-9\-]/", "", $firstNamee);

            #Last name of user
            $lastNamee = $wooData->shipping->last_name;
            $lastName = preg_replace("/[^A-Za-z0-9\-]/", "", $lastNamee);

            #Company
            $companyy = $wooData->shipping->company;
            $company = preg_replace("/[^A-Za-z0-9\-]/", "", $companyy);

            #LineItem
            $lineItem = $wooData->line_items;

            

            #phone Number
            $phonee = $wooData->billing->phone;

            $string = str_replace(" ", "", $phonee); // Replaces all spaces with hyphens.
            $phone = preg_replace("/[^A-Za-z0-9\-]/", "", $string);

            #street 1
            $street1 = $wooData->shipping->address_1;

            #street
            $street2 = $wooData->shipping->address_2;

            #Merged Address
            $street = $street1 . " " . $street2;

            $addressUndefinedLower = strtolower($street);

            if (
                strpos($addressUndefinedLower, "undefined") !== false ||
                strpos($addressUndefinedLower, "po box") !== false
            ) {
                die();
            }
            #City
            $city = $wooData->shipping->city;

            #State
            $state = $wooData->shipping->state;

            #Country
            $country = $wooData->shipping->country;

            #ZipCode
            $ZipCodes = $wooData->shipping->postcode;

            #ZipCode remove symbole
            $ZipCode = substr($ZipCodes, 0, 5);

            #Short zipcode to select zone
       echo     $shortZipCode = substr($ZipCode, 0, 3);
            
            #Getting SKU Code using foreach
            foreach ($lineItem as $keyy) {
                #Getting SKU Code
                $skuCod = $keyy->sku;
                #arrat to Check Sku quantity
                $skuCodeArr[] = $skuCod;
                
                $jsonDataInventory = "{
			        
					'lstsku': [
						'$skuCod',
					]
				}";

                        # MD5 Encryption for generating the signature
                        $rawSignatureSKU = 'app_key' . $appKey . 'formatjsonmethodfu.wms.inventory.gettimestamp' . $timestamp . 'v1.0' . $jsonDataInventory . 'e4b362a3-7977-4699-82f7-31fadd33cf95';

                        #md5 encrypt
                        $signatureSKU = md5($rawSignatureSKU);

                        #Inventory API
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.inventory.get",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => "$jsonDataInventory",
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/json',
                                'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
                            ) ,
                        ));

                        #Execute curl
                        $response = curl_exec($curl);

                        #json to array
                        $InventoryData = json_decode($response);
                        $DataArrayInventory = $InventoryData
                            ->data->data;
                       // print_r($InventoryData);
                        foreach ($DataArrayInventory as $DataArrayInvent)
                        {
                            echo '<pre>';
                            print_r($DataArrayInvent);
                            $stock_quality = $DataArrayInvent->stock_quality;
                            $available_stock = $DataArrayInvent->available_stock;
                            $pending_stock = $DataArrayInvent->pending_stock;
                            $onway_stock = $DataArrayInvent->onway_stock;
                            $wareCode = $DataArrayInvent->warehouse_code;
                            
                            if($wareCode == "USHOUA")
                            {
                                $texasStock = $available_stock;
                                $texasWarehouse="USHOUA";
                            }
                            
                            if($wareCode == "USLAXA")
                            {
                                $LAStock = $available_stock;
                                $LAWarehouse="USLAXA";
                            }
                            if($wareCode == "USNYCA")
                            {
                                $NYStock = $available_stock;
                                $NYWarehouse="USNYCA";
                            }
                            
                        }
                        
                        
                     if(!isset($texasWarehouse))
                     {
                         $texasStock = "0";
                     }
                     if(!isset($LAWarehouse))
                     {
                         $LAStock = "0";
                     }
                     if(!isset($NYWarehouse))
                     {
                         $NYStock = "0";
                     }
                        
                        
                        #variable to test first codition
                    // $texasStock ="0";
                    // $LAStock = '1';
                    // $NYStock ="1";
                    // $shortZipCode= '706';
                        
                        #First Condition to select the Correct warehouse
                       if(($texasStock >="1") && ($LAStock >="1") && ($NYStock >="1"))
                        {
                            
                            echo 'condition works  1';
                            
                            
                            
                            if (in_array($shortZipCode, $txHouse))
                            {
                                $WarehouseCode = "USHOUA";
                                $WarehouseName = "Texas";
                            }
                            else
                            {      
                            
                            if(in_array($shortZipCode,$zoneNJ2)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 2';$zoneCalculation="2";}
                        elseif(in_array($shortZipCode,$zoneNJ3)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 3';$zoneCalculation="3";}
                        elseif(in_array($shortZipCode,$zoneNJ4)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 4';$zoneCalculation="4";}
                        elseif(in_array($shortZipCode,$zoneNJ5)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 5';$zoneCalculation="5";}
                        elseif(in_array($shortZipCode,$zoneNJ6)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 6';$zoneCalculation="6";}
                        elseif(in_array($shortZipCode,$zoneNJ7)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 7';$zoneCalculation="7";}
                        elseif(in_array($shortZipCode,$zoneNJ8)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 8';$zoneCalculation="8";}#----------------------------
                        if(in_array($shortZipCode,$zoneLA2)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 2';$zoneCalculationLa="2";}
                        elseif(in_array($shortZipCode,$zoneLA3)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 3';$zoneCalculationLa="3";}
                        elseif(in_array($shortZipCode,$zoneLA4)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 4';$zoneCalculationLa="4";}
                        elseif(in_array($shortZipCode,$zoneLA5)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 5';$zoneCalculationLa="5";}
                        elseif(in_array($shortZipCode,$zoneLA6)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 6';$zoneCalculationLa="6";}
                        elseif(in_array($shortZipCode,$zoneLA7)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 7';$zoneCalculationLa="7";}
                        elseif(in_array($shortZipCode,$zoneLA8)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 8';$zoneCalculationLa="8";}
                            if($zoneCalculationLa>$zoneCalculation){$WarehouseCode="USNYCA";$WarehouseName="New Jersey Warehouse";}
                            elseif($zoneCalculationLa<$zoneCalculation){$WarehouseCode="USLAXA";$WarehouseName="Los Angles Warehouse";}
                            elseif($zoneCalculationLa==$zoneCalculation){$WarehouseCode="USNYCA";$WarehouseName="New Jersey Warehouse";}
                        
                        
                        
                            } #inarray close
                        }#condition First Closed 
                        
                        #Second Condition to select the Correct warehouse
                        if(($texasStock == '0') && ($LAStock >="1") && ($NYStock >="1"))
                        {
                            echo 'condition works  2';
                            
                                if(in_array($shortZipCode,$zoneNJ2)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 2';$zoneCalculation="2";}
                        elseif(in_array($shortZipCode,$zoneNJ3)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 3';$zoneCalculation="3";}
                        elseif(in_array($shortZipCode,$zoneNJ4)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 4';$zoneCalculation="4";}
                        elseif(in_array($shortZipCode,$zoneNJ5)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 5';$zoneCalculation="5";}
                        elseif(in_array($shortZipCode,$zoneNJ6)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 6';$zoneCalculation="6";}
                        elseif(in_array($shortZipCode,$zoneNJ7)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 7';$zoneCalculation="7";}
                        elseif(in_array($shortZipCode,$zoneNJ8)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 8';$zoneCalculation="8";}#----------------------------
                        if(in_array($shortZipCode,$zoneLA2)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 2';$zoneCalculationLa="2";}
                        elseif(in_array($shortZipCode,$zoneLA3)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 3';$zoneCalculationLa="3";}
                        elseif(in_array($shortZipCode,$zoneLA4)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 4';$zoneCalculationLa="4";}
                        elseif(in_array($shortZipCode,$zoneLA5)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 5';$zoneCalculationLa="5";}
                        elseif(in_array($shortZipCode,$zoneLA6)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 6';$zoneCalculationLa="6";}
                        elseif(in_array($shortZipCode,$zoneLA7)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 7';$zoneCalculationLa="7";}
                        elseif(in_array($shortZipCode,$zoneLA8)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 8';$zoneCalculationLa="8";}
                        
                        if($zoneCalculationLa>$zoneCalculation){$WarehouseCode="USNYCA";$WarehouseName="New Jersey Warehouse";}
                        elseif($zoneCalculationLa<$zoneCalculation){$WarehouseCode="USLAXA";$WarehouseName="Los Angles Warehouse";}
                        elseif($zoneCalculationLa==$zoneCalculation){$WarehouseCode="USNYCA";$WarehouseName="New Jersey Warehouse";}
                            
                            
                        }#Condition Second Closed
                        
                        
                        #Third Condition to select the Correct warehouse
                        if(($texasStock >="1") && ($LAStock =="0") && ($NYStock >="1"))
                        {
                            echo 'condition works 3';
                            
                             if (in_array($shortZipCode, $txHouse))
                            {
                                $WarehouseCode = "USHOUA";
                                $WarehouseName = "Texas";
                            }
                            else
                            {
                                $WarehouseCode = "USNYCA";
                                $WarehouseName = "New Jersey Warehouse";
                            }
                        }
                        
                        #Second Condition to select the Correct warehouse
                        if(($texasStock >="1") && ($LAStock >="1") && ($NYStock =="0"))
                        {
                            echo 'condition works 4';
                             if (in_array($shortZipCode, $txHouse))
                            {
                                $WarehouseCode = "USHOUA";
                                $WarehouseName = "Texas";
                            }
                            else
                            {
                                $WarehouseCode = "USLAXA";
                                $WarehouseName = "Los Angles Warehouse";
                            }
                        }
                        
                        if(($texasStock >="1") && ($LAStock =="0") && ($NYStock =="0"))
                        {
                            echo 'condition works 5';
                            $WarehouseCode = "USHOUA";
                            $WarehouseName = "Texas";
                        }
                        
                        if(($texasStock =="0") && ($LAStock >="1") && ($NYStock =="0"))
                        {
                            echo 'condition works 6';
                            
                            $WarehouseCode = "USLAXA";
                            $WarehouseName = "Los Angles Warehouse";
                        }
                        
                        if(($texasStock =="0") && ($LAStock =="0") && ($NYStock >="1"))
                        {
                            echo 'condition works 7';
                            
                            $WarehouseCode = "USNYCA";
                            $WarehouseName = "New Jersey Warehouse";
                            
                        }
                        
                        
                        

                
                echo $WarehouseName;
                
                
            }
            
            


}}
