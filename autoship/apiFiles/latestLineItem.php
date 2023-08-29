    <?php
    function multipleLine($skuCod)
    {
        
     
        require ('MultiLineItemFunction.php');
    
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
       // print_r($wooData);
        #time delay for 300 seconds
        $timeDelayShip = strtotime("+200 seconds");
    
        $timeDelayLabel = strtotime("+300 seconds");
    
        $Consignment_type = "S";
    
        #Order Status
        $orderStauts = $wooData->status;
    
        #customer Notes
        $customerNote = $wooData->customer_note;
    
        $paymentMethod = $wooData->payment_method_title;
    
        $shippingID = $wooData->transaction_id;
    
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
        $firstNamee = $wooData
            ->shipping->first_name;
    
        $firstName = preg_replace('/[^A-Za-z0-9\-]/', '', $firstNamee);
    
        #Last name of user
        $lastNamee = $wooData
            ->shipping->last_name;
    
        $lastName = preg_replace('/[^A-Za-z0-9\-]/', '', $lastNamee);
    
        #Company
        $companyy = $wooData
            ->shipping->company;
    
        $company = preg_replace('/[^A-Za-z0-9\-]/', '', $companyy);
        
            if(empty($company))
    {
        $company = $firstName;
    }
    
        #LineItem
        $lineItem = $wooData->line_items;
    
        #phone Number
        $phonee = $wooData
            ->billing->phone;
    
        $string = str_replace(' ', '', $phonee); // Replaces all spaces with hyphens.
        $phone = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    
        #street 1
        $street1 = $wooData
            ->shipping->address_1;
    
        #street
        $street2 = $wooData
            ->shipping->address_2;
    
        #Merged Address
        $street = $street1 . " " . $street2;
    
        $addressUndefinedLower = strtolower($street);
    
        if (strpos($addressUndefinedLower, "undefined") !== false || strpos($addressUndefinedLower, "po box") !== false)
        {
            die();
        }
        #City
        $city = $wooData
            ->shipping->city;
             $city=strtoupper($city);
           $city=  str_replace("-"," ",$city);
    
        #State
        $state = $wooData
            ->shipping->state;
    
        #Country
        $country = $wooData
            ->shipping->country;
    
        #ZipCode
        $ZipCodes = $wooData
            ->shipping->postcode;
    
        #ZipCode remove symbole
        $ZipCode = substr($ZipCodes, 0, 5);
    
        #Short zipcode to select zone
        $shortZipCode = substr($ZipCode, 0, 3);
    
        #Customer Provided Note
        $customerNote = $wooData->customer_note;
    
        #LineItem
        $lineItems = $wooData->line_items;
    $i=0;
        foreach ($lineItems as $key)
        {
           
            $customer_code= $key->customer_code;
            
            //print_r($key);
            #sku code
            $skuCode[] = $key->sku;
            
            $SKUCODE = $key->sku;
            
        //    print_r($skuCode);
    
            #quantity
           echo $quantityFinal = $key->quantity;
          
            $arr[] = array(
                            "sku_code" => "$SKUCODE",
                            "qty" => "$quantityFinal",
                            "stock_quality" => "G"
                        );
           
            
        } 
        
        
        #--------------------------------------------------#
        foreach ($lineItems as $key)
        {
           
            $skuCod = $key->sku;
            #quantity
           echo $quantityFinal = $key->quantity;
        
        
        #--------------------------------------------------#
        
        /*foreach($skuCode as $skuCod)
        {*/
            

       $wareCode ="";
        $available_stock="";
    
    $texasStock = "";
    $LAStock = "";
    $NYStock = "";
            #Getting 'the warehouse to get the availablity of the product in warehouse------------------------------
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
               
                print_r($DataArrayInvent);
                $stock_quality = $DataArrayInvent->stock_quality;
                $available_stock = $DataArrayInvent->available_stock;
                $pending_stock = $DataArrayInvent->pending_stock;
                $onway_stock = $DataArrayInvent->onway_stock;
                $wareCode = $DataArrayInvent->warehouse_code;
    
                if ($wareCode == "USHOUA")
                {
                    $texasStock = $available_stock;
                    $texasWarehouse = "USHOUA";
                }
     
                if ($wareCode == "USLAXA")
                {
                    $LAStock = $available_stock;
                    $LAWarehouse = "USLAXA";
                }
                if ($wareCode == "USNYCA")
                {
                    $NYStock = $available_stock;
                    $NYWarehouse = "USNYCA";
                }
    
                if (!isset($texasWarehouse))
                {
                    $texasStock = "0";
                }
                elseif (!isset($LAWarehouse))
                {
                    $LAStock = "0";
                }
                elseif (!isset($NYWarehouse))
                {
                    $NYStock = "0";
                }
                
                
                
                
    }
    
/*  if(empty($LAStock))
    {
        $LAStock ='0';
    }
    if(empty($NYStock))
    {
        $LAStock ='0';
    }
    if(empty($texasStock))
    {
        $LAStock ='0';
    }*/
    
    
    $arrayForWarehouse[]=array(
                    "LA"=>"$LAStock",
                    "NY"=>"$NYStock",
                    "TX"=>"$texasStock"
                    );
    

    foreach ($arrayForWarehouse as $compareArrayWarehouse)
    {
       
        print_r($compareArrayWarehouse);
        $LaWare[] = $compareArrayWarehouse['LA'];
        $NyWare[] = $compareArrayWarehouse['NY'];
        $txWare[] = $compareArrayWarehouse['TX'];
        //  $txWare[] ='2';
        
    }
    
   
    
    
 
     
    
    #json body for API
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
    

        
                    echo $WareHouseName;     
                        
         
        

        }
        
        $skuArray = json_encode($arr, JSON_PRETTY_PRINT);
        
        $totalWeight = array_sum($weightArray);
        
        $LogDate = date("jS  F Y h:i:s A");
       // file_put_contents('totalWeight.txt', $LogDate . $wooId .'Weight -  '. $totalWeight .' -'. PHP_EOL, FILE_APPEND);
        
        
        #########################################################################
        

        
        
            if(!in_array(0,$NyWare))
        {
   $arrayOfWareHouseName[]='USNYCA';
        }
        if(!in_array(0,$LaWare))
        {
      $arrayOfWareHouseName[]='USLAXA';
        }
        if(!in_array(0,$txWare))
        {
     $arrayOfWareHouseName[]='USHOUA';
        }
       
       
       
        
       
        
        print_r($arrayOfWareHouseName);
        
        
          $warehouseTotal = count($arrayOfWareHouseName);
          
        

if($warehouseTotal > 1)
{
  
  if(in_array('USNYCA',$arrayOfWareHouseName))
        {
          $NYWarehous='USNYCA';
        }
        if(in_array('USLAXA',$arrayOfWareHouseName))
        {
           $LAWarehous='USLAXA';
        }
        if(in_array('USHOUA',$arrayOfWareHouseName))
        {
          $texasWarehous='USHOUA';
        }


        ##############################  
        
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

 
        
        
        ############################## 
                            echo 'preet';
                       
                    echo    $WarehouseCode ;    
                         
                            
                            }                      #countWarehouse Array   
                            
                            elseif($warehouseTotal = 1)
                            {
                            
                               $WarehouseCode =$arrayOfWareHouseName[0];
                            }
        
       
        
        
       // print_r($LaWare);
       
       
                        if(in_array($WarehouseCode,$arrayOfWareHouseName))
                        { 
                            
echo 'preet';
            
                        if ($WarehouseCode == "USNYCA")
                        {
                            if ($totalWeight < "4.05")
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($totalWeight > "4.05") && ($totalWeight <= "20"))
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($totalWeight > "20") && ($totalWeight <= "23"))
                            {
                                $logProdCode = "F123";
                            }
                            
                        }

                        elseif ($WarehouseCode == "USLAXA")
                        {
                            if ($totalWeight < "4.05")
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($totalWeight > "4.05") && ($totalWeight <= "20"))
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($totalWeight > "20") && ($totalWeight <= "23"))
                            {
                                $logProdCode = "F123";
                            }
                           
                        }
                        elseif ($WarehouseCode == "USHOUA")
                        {
                            if ($totalWeight < "4.05")
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($totalWeight > "4.05") && ($totalWeight <= "20"))
                            {
                                $logProdCode = "F132";
                            }
                            elseif (($totalWeight > "20") && ($totalWeight <= "23"))
                            {
                                $logProdCode = "F123";
                            }
                           
                        }
                        
                        
                        
                        if(!sizeof($arrayOfWareHouseName) == 0 )
{

                        
                echo        $jsonnData = "{
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
                
                	'oconsignment_sku': $skuArray
                }";
                
                
            if ($totalWeight > "23")
            {
                echo "function_calling";
                $jsonDataFunc = "{
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
                		'qty': '1',
                		'stock_quality': 'G'
                	}]
                }";

               // echo addCancelOrder($wooId, $jsonDataFunc);
               
            } #Over weight order create and cancel process Condition end
            #----------------------------------------------------#
            else
            {
                 # if isset condition to run the curl
                 
                echo 'Create_shipment';
             Create_shipment($jsonnData,$wooId);      
            }
                
}   
else

{
    $jsonDataFunc = "{
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
                		'qty': '1',
                		'stock_quality': 'G'
                	}]
                }";

                echo addCancelOrder($wooId, $jsonDataFunc);
}
      
        
                        }   
    }