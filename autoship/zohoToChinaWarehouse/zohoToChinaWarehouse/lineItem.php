    <?php
    function multipleLine($ZohoData,$ZohoContactData,$companyZoho,$token)
    {
        
    echo 'multiple';
    #include database
    require('database.php');
    
     #include database
    require('function.php');
    
    $timestamp = time();
    
    #array to get the Product SKU and QTY
	$cartProducts= $ZohoData['data'][0]['Product_Details'];
	
	
	$netTotal= $cartProducts[0]['net_total'];
	
	$ZohoCrmIdID= $ZohoData['data'][0]['id'];
	
	
	$TotalQuantity= $cartProducts[0]['quantity'];
	
	$array=array();
	
	 
    foreach ($cartProducts as $keyy)
            {
                
                
                $skuCodID = $keyy['product']['id'];
                
                 $quantityFinal = $keyy['quantity'];
                
                $netTotal =  $keyy['net_total'];
                
	            $TotalQuantity=  $keyy['quantity'];
	            
	            $list_price=  $keyy['list_price'];
	            
	           $weightInLoop = $netTotal / $TotalQuantity;
	           
	           
	           $list_price_for_db = $list_price * $quantityFinal;
	            
	          
    
    $curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Products/$skuCodID",
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
$ZohoProductData = json_decode($response,true);



$productWeight =$ZohoProductData['data'][0]['Net_Weight'] + 50;

$weightTotalPerProduct = $productWeight * $quantityFinal;

$FinalWeight[]= $weightTotalPerProduct;

$totalFinalWeight =array_sum($FinalWeight);

 $calculatedWeight =$totalFinalWeight/1000;



 $invoice_cnname =$ZohoProductData['data'][0]['Chinese_Product_Name'];

$invoice_enname =$ZohoProductData['data'][0]['Product_English_Name'];

$hs_code=$ZohoProductData['data'][0]['HS_Code'];

$invoice_material=$ZohoProductData['data'][0]['Material'];

$unitPrice= $ZohoProductData['data'][0]['Unit_Price'];

$invoice_spec = "无型号";
$invoice_use = $ZohoProductData['data'][0]['Usage'];
$invoice_brand ="无品牌";



$invoice_quantity = $quantityFinal .'('.  $invoice_enname . ')';



#sennding the LineItems in database---------------------------#
 $query = "SELECT * FROM Tx_order WHERE invoice_enname = '$invoice_enname'";
 $result = $conn->query($query);
 

if ($result) {
  if (mysqli_num_rows($result) > 0) {
    echo 'found!';
    
    while($res = mysqli_fetch_array($result))
    {
        
      //  print_r($res);
        
        $dbID = $res['id'];
        
       $price = $res['price'];
       $dbQuantity = $res['invoice_quantity'];
       
       $newQuantity = $dbQuantity + $quantityFinal;
       
       $newTotalPrice = $list_price_for_db + $price;
       
       $updateUnitAmount = $newTotalPrice / $newQuantity;
       
       
       
       
       
        $sqlquery = "UPDATE `Tx_order` SET `invoice_quantity` = '$newQuantity', `invoice_unitcharge` = '$updateUnitAmount', `price` = '$newTotalPrice' WHERE `id` = '$dbID'";
        if ($conn->query($sqlquery) === true)
    {
        echo "record inserted successful";
        
    }
    }
    
    
  } else {
   
     $sqlquery = "INSERT INTO `Tx_order` (`invoice_enname`, `invoice_cnname`, `invoice_quantity`, `hs_code`, `invoice_unitcharge`, `invoice_spec`, `invoice_use`, `invoice_material`, `unit_code`, `price`, `invoice_brand`)
VALUES ('$invoice_enname', '$invoice_cnname', '$quantityFinal', '$hs_code', '$weightInLoop', '无型号', '$invoice_use', '$invoice_material', 'PCE', '$list_price_for_db', '无品牌');";
    if ($conn->query($sqlquery) === true)
    {
        echo "record inserted successful";
        
    }
   
   
  }

}
 
 

    
 
 




}


        
        
        
        
        
        $query = "SELECT * FROM Tx_order";
 $result = $conn->query($query);
 


    
    while($res = mysqli_fetch_array($result))
    {
        
    //   print_r($res);
       
       
 
   $idDB = $res['id']; 
  $DBinvoice_enname  = $res['invoice_enname'];
   $DBinvoice_cnname = $res['invoice_cnname'];
   $DBinvoice_quantity = $res['invoice_quantity'];
   $DBhs_code = $res['hs_code'];
   $DBinvoice_unitcharge = $res['invoice_unitcharge'];
   $DBinvoice_use = $res['invoice_use'];
   $DBinvoice_material = $res['invoice_material'];

   
   $arrr[] = array(
                            "invoice_enname"=> "$DBinvoice_enname",
                    		"invoice_cnname"=> "$DBinvoice_cnname",
                    		"invoice_quantity"=> "$DBinvoice_quantity",
                    		"hs_code"=> "$DBhs_code",
                    		"invoice_unitcharge"=> "$DBinvoice_unitcharge",
                    		"invoice_spec"=> "无型号",
                    		"invoice_use"=> "$DBinvoice_use",
                    		"invoice_material"=> "$DBinvoice_material",
                    		"unit_code"=> "PCE",
                    		"invoice_brand"=> "无品牌"
                );


   
   
        
    }
        
        
        
        $arrayJson=json_encode($arrr,JSON_PRETTY_PRINT );
        
       // print_r($arrayJson);
        
        
        
        

        $reference_no = $ZohoData['data'][0]['TX_Logistic_Order_No'];
        
        $shipping_method = $ZohoData['data'][0]['TX_Logistic_Trans_Option'];
      
        #Total Weighrt in grams
        $order_weight =$FinalWeight;

        $order_pieces = $ZohoData['data'][0]['Total_CTNs'];
        
        $cargotype ="W";
        
        $order_status ="P";
        
        $mail_cargo_type ="4";
        
        $custom_hawbcode = $ZohoData['data'][0]['TX_Logistic_Order_No'];
        
        $userId	=  $ZohoData['data'][0]['Owner']['id'];
        
        $CustomerPO =  $ZohoData['data'][0]['PO'];
        
        
        
        
        
        #----------------------------------------------------#
        
        $curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.zohoapis.com/crm/v3/users/$userId",
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
$ZohoUserData= json_decode($response,true);
      
      
    //print_r($ZohoData);  

        $shipper_name	=  $ZohoData['data'][0]['Owner']['name'];
        $shipper_company ="PromoWare";
        $shipper_countrycode ="CN";
        $shipper_province = $ZohoUserData['users'][0]['state'];
        $shipper_city = $ZohoUserData['users'][0]['city'];
        $shipper_street = $ZohoUserData['users'][0]['street'];
        $shipper_postcode = $ZohoUserData['users'][0]['zip'];
        $shipper_mobile = $ZohoUserData['users'][0]['mobile'];
        $shipper_telephone = $ZohoUserData['users'][0]['phone'];
        
        
             #----------------------------------------------------#
        
        $consignee_name =  $ZohoContactData['data'][0]['Full_Name'];
        $consignee_company =$ZohoContactData['data'][0]['field1'];
        $consignee_countrycode =$ZohoContactData['data'][0]['Country_2_Letter_Code'];
        $consignee_province =$ZohoContactData['data'][0]['Mailing_State'];
        $consignee_city =$ZohoContactData['data'][0]['Mailing_City'];
        $consignee_street =$ZohoContactData['data'][0]['Mailing_Street'];
        $consignee_postcode =substr($ZohoContactData['data'][0]['Mailing_Zip'], 0, 5);
        
        $consignee_telephone =$ZohoContactData['data'][0]['Phone']; 
        $consignee_mobile = $ZohoContactData['data'][0]['Mobile'];
        $consignee_tariff =$ZohoContactData['data'][0]['Tax_No'];


$grossweight = $calculatedWeight / $order_pieces;

for ($x = 1; $x <= $order_pieces; $x++) {

  
  $cargoArray[] = array(
                            "child_number"=> "32165$x",
                    		"involume_length"=> "40",
                    		"involume_width"=> "40",
                    		"involume_height"=> "20",
                    		"involume_grossweight"=> "$grossweight"
                );
                
                

  
}

 $cargoArrayJSon=json_encode($cargoArray,JSON_PRETTY_PRINT );
    

        if(empty($consignee_company))
{
   $consignee_company =  $consignee_name;
}







            $jsonFright='{
	"reference_no": "'.$reference_no.'",
	"shipping_method": "'.$shipping_method.'",
	"order_weight": "",
	"order_status": "P",
	"mail_cargotype": "'.$mail_cargo_type.'",
	"order_pieces": "",
	"cargotype": "W",
	"consignee": {
		"consignee_company": "'.$consignee_company.'",
		"consignee_city": "'.$consignee_city.'",
		"consignee_street": "'.$consignee_street.'",
		"consignee_name": "'.$consignee_name.'",
		"consignee_telephone": "'.$consignee_telephone.'",
		"consignee_mobile": "'.$consignee_mobile.'",
		"consignee_countrycode": "'.$consignee_countrycode.'",
		"consignee_province": "'.$consignee_province.'",
		"consignee_postcode": "'.$consignee_postcode.'",
		"consignee_tariff": "'.$consignee_tariff.'"




	},
	"shipper": {
		"shipper_countrycode": "'.$shipper_countrycode.'",
		"shipper_city": "'.$shipper_city.'",
		"shipper_street": "'.$shipper_street.'",
		"shipper_name": "'.$shipper_name.'",
		"shipper_mobile": "'.$shipper_mobile.'",
		"shipper_company": "PromoWare",
		"shipper_province": "'.$shipper_province.'",
		"shipper_postcode": "'.$shipper_postcode.'",
		"shipper_telephone": "'.$shipper_telephone.'"
		
		
	},
	"invoice": 	'.$arrayJson.',
	  "cargovolume":'.$cargoArrayJSon.'
}';


            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://ywtx.rtb56.com/webservice/PublicService.asmx/ServiceInterfaceUTF8',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => "appToken=f651e053023124210cbf573e7520c7ae&appKey=Microgrid&serviceMethod=createorder&paramsJson=$jsonFright",
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
            
            print_r(json_decode($response));
            
            $dataWarehouse= json_decode($response);
            
            $warehouseOrderID = $dataWarehouse->data->order_id;
            $refrence_no = $dataWarehouse->data->refrence_no;
            $shipping_method_no = $dataWarehouse->data->shipping_method_no;
            
            $channel_hawbcode = $dataWarehouse->data->channel_hawbcode;
            
            $resultSuccess =$dataWarehouse->success;
            
                
            if($resultSuccess == "1" || $resultSuccess == "2")
            {
                
                updateZohoModule($refrence_no,$token,$ZohoCrmIdID);
                      
 

                # Inserting the data in databse------------
                   $sqlquery = "INSERT INTO zohoChinaWarehouse (id,con_no,ship_no,zoho_id,website_order_id,status,datee,time_update,dupStatus,order_id) VALUES 
    (NULL, '$warehouseOrderID', '$channel_hawbcode', '$ZohoCrmIdID','$orderIdNumber','processing','$timestamp','','','$refrence_no')";
    if ($conn->query($sqlquery) === true)
    {
        
        
        
        
        
    }
                
                
                
                
                
                
                
            }
            
            
            
            
            
  $query = "TRUNCATE TABLE Tx_order";
if ($conn->query($query) === true)
    {
        //echo "record inserted successful";
        
    }

        
        
        
    }
		
    
    
    
    
    
    
    
    