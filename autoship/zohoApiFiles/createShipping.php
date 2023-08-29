<?php

#Project started on Date - 07/09/2022
header('Content-Type: text/html');

#include Database File
require ('database.php');

#include lineItem File
require ('lineItem.php');

#include Function File
require ('function.php');

#Timestamp
$timestamp = time();

echo '<pre>';
#Recived Data From Webhook From Zoho CRM
$json = file_get_contents('php://input');

#Put Data in text file
file_put_contents("ZohoDataWebhook.txt",$json);

#Get Data from the file
$json1 = file_get_contents('ZohoDataWebhook.txt');

#Decode json Response To Array
 $ZohoDataHook=explode("=",$json1);
 $ZohoCrmId=$ZohoDataHook[1];
 
 echo '<pre>';
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
#-----------------------Get Contacts from zoho-----------------------#
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Sales_Orders/$ZohoCrmId",
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
#Decode response

file_put_contents("ZohoWebAllDAta.txt",$response);

$ZohoData = json_decode($response,true);

echo $ZohoContactID= $ZohoData['data'][0]['Contact_Name']['id'];

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Contacts/$ZohoContactID",
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
#Decode response
$ZohoContactData = json_decode($response,true);



echo $contactZoho= $ZohoContactData['data'][0]['Phone'];
$contactZoho= $ZohoContactData['data'][0]['Phone'];


$companyZoho= $ZohoContactData['data'][0]['field1'];

//print_r($ZohoData);




 $json = file_get_contents('ZohoWebAllDAta.txt');
    #Decode json Response To Array
    $ZohoData = json_decode($json,TRUE);




$name= $ZohoData['data'][0]['Owner']['name'];
$ZohoCrmIdID= $ZohoData['data'][0]['id'];
$email = $ZohoData['data'][0]['Owner']['email'];


$shippingCode== $ZohoData['data'][0]['Shipping_Code'];
$orderIdNumber= $ZohoData['data'][0]['field13'];
$totalAmount= $ZohoData['data'][0]['Sub_Total'];
$ContactName= $ZohoData['data'][0]['Contact_Name']['name'];


$status= $ZohoData['data'][0]['Status'];


$cartProducts= $ZohoData['data'][0]['Product_Details'];


            foreach ($cartProducts as $keyy)
            {
                
               // print_r($keyy);
              
              
               #Getting SKU Code
                $skuCod = $keyy['product']['Product_Code'];

                #quantity
                $quantity = $keyy['product']['quantity'];

                $skuCodeArr[] = $skuCod;
            }


if ($status == "Approved")
{

    $sqlquery = "INSERT INTO zohoWarehouse VALUES 
    (NULL, '', '', '$ZohoCrmId','$orderIdNumber','processing','$timestamp','','')";
    if ($conn->query($sqlquery) === true)
    {
        //echo "record inserted successfully";
        
    }



$skuQuantity = count($skuCodeArr);

if($skuQuantity > 1)
{

    
 multipleLine($ZohoData,$contactZoho,$companyZoho,$token);
}
else
{

singleLine($ZohoData,$contactZoho,$companyZoho,$token);
}

}  





function singleLine($ZohoData,$contactZoho,$companyZoho,$token){


    #include zones
    require ('zones.php');
    
    #include database
    require ('database.php');
    
   
    #include details 
    require ('details.php');
    
    

    $json = file_get_contents('ZohoWebAllDAta.txt');
    #Decode json Response To Array
    $ZohoData = json_decode($json,TRUE);
    
   print_r($ZohoData);
	
	    #time delay for 300 seconds
    $timeDelayShip=strtotime("+200 seconds");
    
    $timeDelayLabel=strtotime("+300 seconds");
	
	$Consignment_type = "S";
	
	#order Status
	$status= $ZohoData['data'][0]['Status'];
	
	#websiteOrderNumber
	$orderIdNumber= $ZohoData['data'][0]['field13'];
    
    #name of the Customer Owner
    $OwnerNamee= $ZohoData['data'][0]['Owner']['name'];
	
	
	#Email of the customer
	$email = $ZohoData['data'][0]['Owner']['email'];
	
	#zohoCRM ID
	$ZohoCrmIdID= $ZohoData['data'][0]['id'];


	
	
	#Total amount of the order
	$totalAmount= $ZohoData['data'][0]['Sub_Total'];
	
	#Name of the Customer
	$ContactName= $ZohoData['data'][0]['Contact_Name']['name'];
	$firstName = preg_replace('/[^A-Za-z0-9\-]/', ' ', $ContactName);
	
	#Order Created Date
    $dateCreated = $ZohoData['data'][0]['Sales_Order_Date'];
	
	#formatted Date to link with Customer ID
	$newDate = date("dmY", strtotime($dateCreated));
	
	#Customer order Number To send in Warehouse
    $customerOrderNumber = "Zoho" . $newDate . $orderIdNumber;
    
	#final PhoneNumber
	$phone = $contactZoho;
	
	#street
	$street = $ZohoData['data'][0]['Shipping_Street'];
	
	#city
	$city = $ZohoData['data'][0]['Shipping_City'];
	 $city=strtoupper($city);
	  $city=  str_replace("-"," ",$city);
  
	#state
	$state = $ZohoData['data'][0]['Shipping_State'];
	
	#country
	$country =  $ZohoData['data'][0]['Country_Area'];
	
	#zipcode
	$ZipCodes = $ZohoData['data'][0]['Shipping_Code'];
	
	#ZipCode remove symbole
    $ZipCode = substr($ZipCodes, 0, 5);

    #Short zipcode to select zone
    $shortZipCode = substr($ZipCode, 0, 3);
    
    
    #if Company name is empty
    if(empty($companyZoho))
{
    $companyZoho = $firstName;
}


	#array to get the Product SKU and QTY
	$cartProducts= $ZohoData['data'][0]['Product_Details'];


            foreach ($cartProducts as $keyy)
            {
                
                
              //  print_r($keyy);
              
                #Getting SKU Code
                $skuCod = $keyy['product']['Product_Code'];

                #quantity
                $quantityFinal = $keyy['quantity'];

            }
    
    
    

 
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
                        //    print_r($DataArrayInvent);
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
                        
                        if(($texasStock =="0") && ($LAStock =="0") && ($NYStock >="0"))
                        {
                            echo 'condition Preet';
                            
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
                
              //  print_r($data);
				
				
				
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
                	'sales_no': '$orderIdNumber',
                	'currency': 'USD',
                	'remark': '$customerNote',
                	'oconsignment_desc': {
                		'country': '$country',
                		'state': '$state',
                		'city': '$city',
                		'post_code': $ZipCode,
                		'street': '$street',
                		'phone' : '$phone',
                		'company': '$companyZoho',
                		'last_name': '$lastName',
                		'first_name': '$firstName'
                	},
                	'oconsignment_sku': [{
                		'sku_code': '$skuCod',
                		'qty': '$quantityFinal',
                		'stock_quality': 'G'
                	}]
                }";
				
				
				file_put_contents('JsonencodedData.txt', $LogDate . $orderIdNumber . $jsonData . PHP_EOL, FILE_APPEND);
                        #Over weight order create and cancel process
                        if ($weightInKGG > "23")
                        {

                            echo $jsonDataFunc = "{
                    'customer_code': '$customer_code',
                	'ref_no': '$customerOrderNumber',
                	'from_warehouse_code': '$WarehouseCode',
                	'consignment_type':'$Consignment_type',
                	'logistics_service_info': {
                		'logistics_product_code': '$logProdCode'
                	},
                	'shop_id': 'promowares',
                	'sales_no': '$orderIdNumber',
                	'currency': 'USD',
                	'remark': '$customerNote',
                	'oconsignment_desc': {
                		'country': '$country',
                		'state': '$state',
                		'city': '$city',
                		'post_code': $ZipCode,
                		'street': '$street',
                		'phone' : '$phone',
                		'company': '$companyZoho',
                		'last_name': '$lastName',
                		'first_name': '$firstName'
                	},
                	'oconsignment_sku': [{
                		'sku_code': '$skuCod',
                		'qty': '1',
                		'stock_quality': 'G'
                	}]
                }";
                       
                          //  echo addCancelOrder($orderIdNumber, $jsonDataFunc);
                          
                          
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
                        file_put_contents('response1_log.txt', $LogDate . $orderIdNumber . $response . PHP_EOL, FILE_APPEND);

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
                            
                             //Cancellll($consignment);
                              //unDefinedAdd($consignment,$orderIdNumber);
                            
                            #Sending the information in database to call the functions
                                $sqlDBShip = "INSERT INTO `ZohoFunctionShip` (`id`, `con_no`, `wooId`, `status`, `timestamp`) VALUES (NULL, '$consignment', '$orderIdNumber', '1','$timeDelayShip')";
                            $res = mysqli_query($conn, $sqlDBShip); 
                            
                            $sqlDBLAbel = "INSERT INTO `ZohoFunctionLabel` (`id`, `con_no`, `wooId`, `status`, `timestamp`) VALUES (NULL, '$consignment', '$orderIdNumber', '1','$timeDelayLabel')";
                            $res = mysqli_query($conn, $sqlDBLAbel); 
                            
                           
                           
                          // Test if string contains the word 
                           // if(strpos($street, "undefined") !== false){
                               
                           // }
                            
                           // sendingEmails($consignment, $orderIdNumber);
                            
                           // fedexStatusToWoo($consignment, $orderIdNumber, $token);

                        }
                        
                        elseif($result == "0")
                        {
                           if($error_code == "AVALIABLE_STOCK_IS_NOT_ENOUGH") {
                               
                       //     checkStockAvaliable($customer_code,$customerOrderNumber,$Consignment_type,$logProdCode,$orderIdNumber,$customerNote,$country,$state,$city,$ZipCode,$street,$phone,$company,$lastName,$firstName,$skuCode,$quantityFinal);
                            
                            
                         
                
                 file_put_contents('SKUUu.txt', $LogDate . $orderIdNumber . $available_stock.'AVALIABLE_STOCK_IS_NOT_ENOUGH'.$jsonnData . PHP_EOL, FILE_APPEND);
                         // checkStockAvaliable()
                          
                           }
                           
                        }
                        
                        
                        if ($weightInKGG < "23")
                        {
                            if ($result == "0")
                            {
                                #send Overweight Notification by Email
                                $subject = "Order No.:$orderIdNumber, 其他错误，需手动处理，错误内容请查看邮件正文代码.";

                                $bodyText = "Order No.:$orderIdNumber, Error:$errors ,Message: $msg 需手动处理.";

                                $bodyHtml = "Order No.:$orderIdNumber, Error:$errors ,Message: $msg 需手动处理.";

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