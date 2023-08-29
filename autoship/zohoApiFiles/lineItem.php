    <?php
    function multipleLine($ZohoData,$contactZoho,$companyZoho,$token)
    {
        
     //   echo 'preetpreet';
        
        
        
        
        require ('MultiLineItemFunction.php');
    
        #include zones
        require ('zones.php');
    
        #include database
        require ('database.php');
    
  
    
        #include details
        require ('details.php');
    
    

    
  
   
 //   print_r($ZohoData);
   
   
   
	
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
				
				
				$skuCode[] = $skuCod	;
						
                #quantity
                $quantityFinal = $keyy['quantity'];
				
				$arr[] = array(
                            "sku_code" => "$skuCod",
                            "qty" => "$quantityFinal",
                            "stock_quality" => "G"
                        );

            }
			
			##---------------------------------------##
			
			
			foreach ($cartProducts as $keyy)
            {
                
                #Getting SKU Code
                $skuCod = $keyy['product']['Product_Code'];
				
                #quantity
                $quantityFinal = $keyy['quantity'];
			##---------------------------------------##
			
		/*	foreach($skuCode as $skuCod)
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
                            elseif ($totalWeight > "23")
                            {
                                $logProdCode = "F132";
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
                            elseif ($totalWeight > "23")
                            {
                                $logProdCode = "F132";
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
                            elseif ($totalWeight > "23")
                            {
                                $logProdCode = "F132";
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
                	'sales_no': '$ZohoCrmIdID',
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
                	'sales_no': '$ZohoCrmIdID',
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
                		'sku_code': '$skuCode',
                		'qty': '1',
                		'stock_quality': 'G'
                	}]
                }";

              //  echo addCancelOrder($ZohoCrmIdID, $jsonDataFunc);
            } #Over weight order create and cancel process Condition end
            #----------------------------------------------------#
            else
            {
                 # if isset condition to run the curl
                 
                    echo 'Create_shipment';
                Create_shipment($jsonnData,$ZohoCrmIdID,$token);      
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
                	'sales_no': '$ZohoCrmIdID',
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
                		'sku_code': '$skuCode',
                		'qty': '1',
                		'stock_quality': 'G'
                	}]
                }";

                echo addCancelOrder($ZohoCrmIdID, $jsonDataFunc);
}
      
        
                        }   
    }
		
    
    
    
    
    
    
    
    