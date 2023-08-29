<?php

#Project started on Date - 29/11/2021
header('Content-Type: text/html');

#include Database File
require ('database.php');

#include lineItem File
require ('latestLineItem.php');

#include Function File
require ('function.php');

  $LogDate = date("jS  F Y h:i:s A");
#Recived Data From Webhook
$json = file_get_contents('php://input');

#Put Data in text file
file_put_contents("latest.txt", $json);

file_put_contents("order_test_getJson.txt", $json . $LogDate. PHP_EOL, FILE_APPEND);


#Get Data from the file
 $json1 = file_get_contents('latest.txt');


#Decode json Response To Array
$wooData = json_decode($json1);

echo "<pre>";
//print_r($wooData);



#Order Status
$orderStauts = $wooData->status;



$shippingID=$wooData->transaction_id;


$totalAmount=$wooData->total;

$wooId = $wooData->number;


$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders/$wooId?consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276&consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125",
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
$WooStatusCheck= $jsonn['status'];









if ($WooStatusCheck == "picked")
{
    if($paymentMethod == "PayPal")
    {
       paypalTracking($wooId,$shippingID);
        
    }
    
}

if ($WooStatusCheck == "processing")
{

    $sqlquery = "INSERT INTO warehouse VALUES 
    (NULL, '', '','$wooId','$WooStatusCheck','$timestamp','','')";
    if ($conn->query($sqlquery) === true)
    {
        //echo "record inserted successfully";
        
    }


            $lineItem = $wooData->line_items;

            #Getting SKU Code using foreach
            foreach ($lineItem as $keyy)
            {
              //  print_r($keyy);
                #Getting SKU Code
                $skuCod = $keyy->sku;
                #arrat to Check Sku quantity
                $skuCodeArr[] = $skuCod;
            }

      

//print_r($skuCodeArr);

$checkQuantity = $skuCodeArr[1];

$skuQuantity = count($skuCodeArr);

    if ($totalAmount >= 30) 
    {
        
    
    
        if($skuQuantity > 1)
            {
                multipleLine($skuCod);
            }
            else
            {
                singleLine($skuCod);
            }
    }
    else
    {
        die();
    }

}  


function singleLine($skuCod){
    
    echo 'preet';
    
    #include zones
    require ('ZonesList.php');
    
    #include database
    require ('database.php');
    
   
    #include details 
    require ('details.php');
    
    $json = file_get_contents('latest.txt');
    #Decode json Response To Array
    $wooData = json_decode($json);
    
    echo "<pre>";
    //print_r($wooData);
    
    #time delay for 300 seconds
  echo  $timeDelayShip=strtotime("+200 seconds");
    
    $timeDelayLabel=strtotime("+300 seconds");
    
    
    $Consignment_type = "S";
    
    #Order Status
    $orderStauts = $wooData->status;
    
    #customer Notes
    $customerNote = $wooData->customer_note;
    
    $paymentMethod = $wooData->payment_method_title;
    
    $shippingID=$wooData->transaction_id;
    
    $wooId = $wooData->number;

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
    
    $firstName = preg_replace('/[^A-Za-z0-9\-]/', '', $firstNamee);

    #Last name of user
    $lastNamee = $wooData->shipping->last_name;
    
    $lastName = preg_replace('/[^A-Za-z0-9\-]/', '', $lastNamee);

    #Company
    $companyy = $wooData->shipping->company;
    
    $company = preg_replace('/[^A-Za-z0-9\-]/', '', $companyy);
    
    
    if(empty($company))
    {
        $company = $firstName;
    }

    #LineItem
    $lineItem = $wooData->line_items;

    #phone Number
    $phonee = $wooData->billing->phone;

    $string = str_replace(' ', '', $phonee); // Replaces all spaces with hyphens.
    
    $phone = preg_replace('/[^A-Za-z0-9\-]/', '', $string);

    #street 1
    $street1 = $wooData->shipping->address_1;

    #street
    $street2 = $wooData->shipping->address_2;

    #Merged Address
    $street = $street1 . " " . $street2;
            
    $addressUndefinedLower = strtolower($street);
  
        if(strpos($addressUndefinedLower, "undefined")!== false || strpos($addressUndefinedLower, "po box")!== false) {
                                 die();           
                                }
    #City
    $city = $wooData->shipping->city;
    $city=strtoupper($city);
     $city=  str_replace("-"," ",$city);

    #State
    $state = $wooData->shipping->state;

    #Country
    $country = $wooData->shipping->country;

    #ZipCode
    $ZipCodes = $wooData->shipping->postcode;

    #ZipCode remove symbole
    $ZipCode = substr($ZipCodes, 0, 5);

    #Short zipcode to select zone
    $shortZipCode = substr($ZipCode, 0, 3);

    #Customer Provided Note
    $customerNote = $wooData->customer_note;

    #LineItem
  echo  $lineItems = $wooData->line_items;
    
    foreach ($lineItems as $key)
            {
                #sku code
                 $skuCod = $key->sku;

                #quantity
                $quantityFinal = $key->quantity;
                
            }
    
    //echo $skuCod;
    
    
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
                            
                            
                            if (in_array($shortZipCode, $zoneTX2))
                                      {                     
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 2';
                                        $zoneCalculationTX="2";                
                                      }
                            elseif (in_array($shortZipCode, $zoneTX3))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 3';
                                        $zoneCalculationTX="3";             
                                      }
                            elseif (in_array($shortZipCode, $zoneTX4))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 4';
                                        $zoneCalculationTX="4";             
                                      }
                            elseif (in_array($shortZipCode, $zoneTX5))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 5';
                                        $zoneCalculationTX="5";             
                                      }
                            elseif (in_array($shortZipCode, $zoneTX6))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 6';
                                        $zoneCalculationTX="6";             
                                      }
                            elseif (in_array($shortZipCode, $zoneTX7))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 7';
                                        $zoneCalculationTX="7";             
                                      }
                            elseif (in_array($shortZipCode, $zoneTX8))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 8';
                                        $zoneCalculationTX="8";             
                                      }
                                      
                                      
                                    
                                    
                    $selectwareHouse = array(
                      "USHOUA" => $zoneCalculationTX,
                      "USLAXA" => $zoneCalculationLa,
                      "USNYCA" => $zoneCalculation
                    );
                                                 
                   
                    if(($selectwareHouse["USLAXA"] == $selectwareHouse["USNYCA"]) && ($selectwareHouse["USLAXA"] == $selectwareHouse["USHOUA"]))   
                      {
                        $WarehouseCode = "USLAXA";
                      }
                    elseif(($selectwareHouse["USLAXA"] == $selectwareHouse["USNYCA"]) && ($selectwareHouse["USLAXA"] != $selectwareHouse["USHOUA"]))
                      {
                      	$WarehouseCode= array_keys($selectwareHouse, min($selectwareHouse))[0]; 
                      }
                    elseif(($selectwareHouse["USLAXA"] == $selectwareHouse["USHOUA"]) && ($selectwareHouse["USLAXA"] != $selectwareHouse["USNYCA"]))
                      {
                      	$WarehouseCodee= array_keys($selectwareHouse, min($selectwareHouse))[0]; 
                        if($WarehouseCodee == "USHOUA")
                        {
                        $WarehouseCode ="USLAXA";
                        }
              
                      }
                    elseif(($selectwareHouse["USNYCA"] == $selectwareHouse["USHOUA"]) && ($selectwareHouse["USLAXA"] != $selectwareHouse["USNYCA"]))
                      
                      {
                              if(($selectwareHouse["USLAXA"]) < ($selectwareHouse["USNYCA"]))
                              {
                              	  $WarehouseCode = "USLAXA";
                              }
                              else
                              {
                             	 $WarehouseCode = "USNYCA";
                              }
                      }
                    else
                      {
                        $WarehouseCode= array_keys($selectwareHouse, min($selectwareHouse))[0]; 
                      } 
                                                                          
                                      
                            
                        
                                      
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
                        elseif($zoneCalculationLa==$zoneCalculation){$WarehouseCode="USLAXA";$WarehouseName="New Jersey Warehouse";}
                            
                            
                        }#Condition Second Closed
                        
                        
                        #Third Condition to select the Correct warehouse
                        if(($texasStock >="1") && ($LAStock =="0") && ($NYStock >="1"))
                        {
                            echo 'condition works 3';
                            
                            
                        if (in_array($shortZipCode, $zoneTX2))
                                      {                     
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 2';
                                        $zoneCalculationTX="2";                
                                      }
                            elseif (in_array($shortZipCode, $zoneTX3))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 3';
                                        $zoneCalculationTX="3";             
                                      }
                            elseif (in_array($shortZipCode, $zoneTX4))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 4';
                                        $zoneCalculationTX="4";             
                                      }
                            elseif (in_array($shortZipCode, $zoneTX5))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 5';
                                        $zoneCalculationTX="5";             
                                      }
                            elseif (in_array($shortZipCode, $zoneTX6))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 6';
                                        $zoneCalculationTX="6";             
                                      }
                            elseif (in_array($shortZipCode, $zoneTX7))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 7';
                                        $zoneCalculationTX="7";             
                                      }
                            elseif (in_array($shortZipCode, $zoneTX8))
                                      {                
                                        $WarehouseCode = "USHOUA";
                                        $WarehouseName = "Texas";
                                        $zone ='Zone 8';
                                        $zoneCalculationTX="8";             
                                      }
                           
                                  if(in_array($shortZipCode,$zoneNJ2)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 2';$zoneCalculation="2";}
                        elseif(in_array($shortZipCode,$zoneNJ3)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 3';$zoneCalculation="3";}
                        elseif(in_array($shortZipCode,$zoneNJ4)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 4';$zoneCalculation="4";}
                        elseif(in_array($shortZipCode,$zoneNJ5)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 5';$zoneCalculation="5";}
                        elseif(in_array($shortZipCode,$zoneNJ6)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 6';$zoneCalculation="6";}
                        elseif(in_array($shortZipCode,$zoneNJ7)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 7';$zoneCalculation="7";}
                        elseif(in_array($shortZipCode,$zoneNJ8)){$WarehouseCode="USNYCA";$WarehouseName="New Jersey";$zone='Zone 8';$zoneCalculation="8";}
                        
                          $selectwareHouse = array(
                                        "USHOUA" => $zoneCalculationTX,   
                                        "USNYCA" => $zoneCalculation
                                    );
                                    print_r($selectwareHouse);
                                    
                                    
                        if($selectwareHouse["USHOUA"] == $selectwareHouse["USNYCA"])
                        {
                          $WarehouseCode = "USNYCA";
                        }
                        else
                        {
                            $WarehouseCode= array_keys($selectwareHouse, min($selectwareHouse))[0]; 
                        }
                                
                                echo "THe WareHouse id ----------". $WarehouseCode;
                                
                                
                            }
                        
                        #Second Condition to select the Correct warehouse
                        if(($texasStock >="1") && ($LAStock >="1") && ($NYStock =="0"))
                        {
                            echo 'condition works 4';
                             if (in_array($shortZipCode, $zoneTX2)) { $WarehouseCode = "USHOUA"; $WarehouseName = "Texas"; $zone ='Zone 2'; $zoneCalculationTX="2"; } elseif (in_array($shortZipCode, $zoneTX3)) { $WarehouseCode = "USHOUA"; $WarehouseName = "Texas"; $zone ='Zone 3'; $zoneCalculationTX="3"; } elseif (in_array($shortZipCode, $zoneTX4)) { $WarehouseCode = "USHOUA"; $WarehouseName = "Texas"; $zone ='Zone 4'; $zoneCalculationTX="4"; } elseif (in_array($shortZipCode, $zoneTX5)) { $WarehouseCode = "USHOUA"; $WarehouseName = "Texas"; $zone ='Zone 5'; $zoneCalculationTX="5"; } elseif (in_array($shortZipCode, $zoneTX6)) { $WarehouseCode = "USHOUA"; $WarehouseName = "Texas"; $zone ='Zone 6'; $zoneCalculationTX="6"; } elseif (in_array($shortZipCode, $zoneTX7)) { $WarehouseCode = "USHOUA"; $WarehouseName = "Texas"; $zone ='Zone 7'; $zoneCalculationTX="7"; } elseif (in_array($shortZipCode, $zoneTX8)) { $WarehouseCode = "USHOUA"; $WarehouseName = "Texas"; $zone ='Zone 8'; $zoneCalculationTX="8"; }
                             
                              if(in_array($shortZipCode,$zoneLA2)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 2';$zoneCalculationLa="2";}
                        elseif(in_array($shortZipCode,$zoneLA3)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 3';$zoneCalculationLa="3";}
                        elseif(in_array($shortZipCode,$zoneLA4)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 4';$zoneCalculationLa="4";}
                        elseif(in_array($shortZipCode,$zoneLA5)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 5';$zoneCalculationLa="5";}
                        elseif(in_array($shortZipCode,$zoneLA6)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 6';$zoneCalculationLa="6";}
                        elseif(in_array($shortZipCode,$zoneLA7)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 7';$zoneCalculationLa="7";}
                        elseif(in_array($shortZipCode,$zoneLA8)){$WarehouseCode="USLAXA";$WarehouseName="Los Angles";$zone='Zone 8';$zoneCalculationLa="8";}
                             
                              $selectwareHouse = array(
                                     "USHOUA" => $zoneCalculationTX,
                                     "USLAXA" => $zoneCalculationLa
                                     );
                              if($selectwareHouse["USLAXA"] == $selectwareHouse["USHOUA"])
                                 {
                                   $WarehouseCode = "USLAXA";
                                 }
                                 else
                                 {
                             $WarehouseCode= array_keys($selectwareHouse, min($selectwareHouse))[0]; 
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
                    
 #---------------------------------------Get inventory from the warehouse------------------------------#            
                    #Get inventory from the warehouse 
                      $jsonDataSku = "{
	'lstsku': [
		$skuCod,
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
                    ) ,
                ));

                #Execute Curl
                $response = curl_exec($curl);

                #Json To Arrau
                $data = json_decode($response);
                
                //print_r($data);

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

                    $weightInKGG = $weight / 1000 * $quantityFinal;
                    $weightArray[] = $weightInKGG;
                    
                }
                
                if ($WarehouseCode == "USNYCA")
                        {
                            if ($weightInKGG < "4.05")
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($weightInKGG > "4.05") && ($weightInKGG <= "20"))
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($weightInKGG > "20") && ($weightInKGG <= "23"))
                            {
                                $logProdCode = "F123";
                            }
                            elseif ($weightInKGG > "23")
                            {
                                $logProdCode = "F132";
                            }
                        }

                        elseif ($WarehouseCode == "USLAXA")
                        {
                            if ($weightInKGG < "4.05")
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($weightInKGG > "4.05") && ($weightInKGG <= "20"))
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($weightInKGG > "20") && ($weightInKGG <= "23"))
                            {
                                $logProdCode = "F123";
                            }
                            elseif ($weightInKGG > "23")
                            {
                                $logProdCode = "F132";
                            }
                        }
                        elseif ($WarehouseCode == "USHOUA")
                        {
                            if ($weightInKGG < "4.05")
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($weightInKGG > "4.05") && ($weightInKGG <= "20"))
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($weightInKGG > "20") && ($weightInKGG <= "23"))
                            {
                                $logProdCode = "F123";
                            }
                            elseif ($weightInKGG > "23")
                            {
                                $logProdCode = "F132";
                            }
                        }
                        

                        #----------------------Api for OPEN4X Create outbond--------------------#
                        #Post INBOUND api request headers
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
                		'sku_code': '$skuCod',
                		'qty': '$quantityFinal',
                		'stock_quality': 'G'
                	}]
                }";
                
                file_put_contents('JsonencodedData.txt', $LogDate . $wooId . $jsonData . PHP_EOL, FILE_APPEND);
                        #Over weight order create and cancel process
                        if ($weightInKGG > "23")
                        {
                                die();
                            echo $jsonDataFunc = "{
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
                		'sku_code': '$skuCod',
                		'qty': '1',
                		'stock_quality': 'G'
                	}]
                }";
                       
                           // echo addCancelOrder($wooId, $jsonDataFunc);
                           
                        } #Over weight order create and cancel process Condition end
                        # MD5 Encryption for generating the signature
                        $rawSignature = 'app_key' . $appKey . 'formatjsonmethodfu.wms.outbound.createtimestamp' . $timestamp . 'v1.0' . $jsonData . 'e4b362a3-7977-4699-82f7-31fadd33cf95';

                        $rawSignature;
                        $signature = md5($rawSignature);

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
                            CURLOPT_POSTFIELDS => "$jsonData",
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/json',
                                'Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676'
                            ) ,
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
                        echo $response;
                        $LogDate = date("jS  F Y h:i:s A");
                        file_put_contents('response1_log.txt', $LogDate . $wooId . $response . PHP_EOL, FILE_APPEND);

                        $error = json_decode($response);
                        print_r($error);
                        $result = $error->result;
                        $resData = $error->data;
                        $errorrs = $error->errors;
                        foreach ($errorrs as $errr)
                        {
                            $errors = $errr->error_msg;
                            $error_code=$errr->error_code;
                        }
                        $msg = $error->msg;
                        $consignment = $resData->consignment_no;
                        if ($result == "1")
                        {
                            
                             Cancellll($consignment);
                              unDefinedAdd($consignment,$wooId);
                            
                            #Sending the information in database to call the functions
                            $sqlDBShip = "INSERT INTO `wooFunctionShip` (`id`, `con_no`, `wooId`, `status`, `timestamp`) VALUES (NULL, '$consignment', '$wooId', '1','$timeDelayShip')";
                            $res = mysqli_query($conn, $sqlDBShip); 
                            
                            $sqlDBLAbel = "INSERT INTO `wooFunctionLabel` (`id`, `con_no`, `wooId`, `status`, `timestamp`) VALUES (NULL, '$consignment', '$wooId', '1','$timeDelayLabel')";
                            $res = mysqli_query($conn, $sqlDBLAbel); 
                            
                           
                           
                          // Test if string contains the word 
                           // if(strpos($street, "undefined") !== false){
                               
                           // }
                            
                            sendingEmails($consignment, $wooId);
                            
                            fedexStatusToWoo($consignment, $wooId);

                        }
                        
                        elseif($result == "0")
                        {
                           if($error_code == "AVALIABLE_STOCK_IS_NOT_ENOUGH") {
                               
                            checkStockAvaliable($customer_code,$customerOrderNumber,$Consignment_type,$logProdCode,$wooId,$customerNote,$country,$state,$city,$ZipCode,$street,$phone,$company,$lastName,$firstName,$skuCode,$quantityFinal);
                            
                            
                         
                
                // file_put_contents('SKUUu.txt', $LogDate . $wooId . $available_stock.'AVALIABLE_STOCK_IS_NOT_ENOUGH'.$jsonnData . PHP_EOL, FILE_APPEND);
                         // checkStockAvaliable()
                          
                           }
                           
                        }
                        
                        
                        if ($weightInKGG < "23")
                        {
                            if ($result == "0")
                            {
                                #send Overweight Notification by Email
                                $subject = "Order No.:$wooId, 其他错误，需手动处理，错误内容请查看邮件正文代码.";

                                $bodyText = "Order No.:$wooId, Error:$errors ,Message: $msg 需手动处理.";

                                $bodyHtml = "Order No.:$wooId, Error:$errors ,Message: $msg 需手动处理.";

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

                    
    
    
    

}


