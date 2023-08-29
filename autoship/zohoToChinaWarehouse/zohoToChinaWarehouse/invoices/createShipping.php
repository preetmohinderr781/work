<?php

#Project started on Date - 07/09/2022
header('Content-Type: text/html');

#include Database File
require ('database.php');

#include lineItem File$reference_no
require ('lineItem.php');

#include Function File
//require ('function.php');

#Timestamp
$timestamp = time();

echo '<pre>';
#Recived Data From Webhook From Zoho CRM
$json = file_get_contents('php://input');

#Put Data in text file
//file_put_contents("ZohoInvoiceWebhook.txt",$json);

#Get Data from the file
$json1 = file_get_contents('ZohoInvoiceWebhook.txt');

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
  CURLOPT_URL => "https://www.zohoapis.com/crm/v2/Invoices/$ZohoCrmId",
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

$ZohoData = json_decode($response,true);
//print_r($ZohoData);



 $ZohoContactID= $ZohoData['data'][0]['Contact_Name']['id'];

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



$contactZoho= $ZohoContactData['data'][0]['Phone'];

$companyZoho= $ZohoContactData['data'][0]['field1'];

//print_r($ZohoData);



/* $json = file_get_contents('ZohoWebAllDAta.txt');
    #Decode json Response To Array
    $ZohoData = json_decode($json,TRUE);*/




$name= $ZohoData['data'][0]['Owner']['name'];
$ZohoCrmIdID= $ZohoData['data'][0]['id'];
$email = $ZohoData['data'][0]['Owner']['email'];


$shippingCode== $ZohoData['data'][0]['Shipping_Code'];
$orderIdNumber= $ZohoData['data'][0]['field13'];
$totalAmount= $ZohoData['data'][0]['Sub_Total'];
$ContactName= $ZohoData['data'][0]['Contact_Name']['name'];


    $status= $ZohoData['data'][0]['Status'];

$transportationStatus= $ZohoData['data'][0]['field18'];




$cartProducts= $ZohoData['data'][0]['Product_Details'];


 
            foreach ($cartProducts as $keyy)
            {
                
             
               #Getting SKU Code
                $skuCod = $keyy['product']['Product_Code'];

                #quantity
                $quantity = $keyy['product']['quantity'];

                $skuCodeArr[] = $skuCod;
            }


//if ($status == "Product Ready to Ship" && $transportationStatus == "腾信物流")






$skuQuantity = count($skuCodeArr);

if($skuQuantity > 1)
{

echo 'multipleeeeee';
    
multipleLine($ZohoData,$ZohoContactData,$companyZoho,$token);
}
else
{
echo 'singleLine';
singleLine($ZohoData,$ZohoContactData,$companyZoho,$token);
}







function singleLine($ZohoData,$ZohoContactData,$companyZoho,$token){
    
    #include database
    require('database.php');
    
    $timestamp = time();
    
    #array to get the Product SKU and QTY
	$cartProducts= $ZohoData['data'][0]['Product_Details'];
	
	$netTotal= $cartProducts[0]['net_total'];
	$TotalQuantity= $cartProducts[0]['quantity'];
	
	
	

        $ZohoCrmId= $ZohoData['data'][0]['id'];
        
        $orderIdNumber= $ZohoData['data'][0]['field13'];
                
                $skuCodID = $cartProducts[0]['product']['id'];
                
                 $quantityFinal = $cartProducts[0]['quantity'];
    
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

  $FinalWeight = $quantityFinal*$productWeight  / 1000;


 $invoice_cnname =$ZohoProductData['data'][0]['Chinese_Product_Name'];

$invoice_enname =$ZohoProductData['data'][0]['Product_English_Name'];

$hs_code=$ZohoProductData['data'][0]['HS_Code'];

$invoice_material=$ZohoProductData['data'][0]['Material'];

 //$unitPrice= $ZohoProductData['data'][0]['Unit_Price'];

 $Unit_Price = $netTotal/$TotalQuantity;

$invoice_spec = "无型号";
$invoice_use = $ZohoProductData['data'][0]['Usage'];
$invoice_brand ="无品牌";

 //print_r($ZohoData);  


$invoice_quantity = $quantityFinal .'('.  $invoice_enname . ')';



        $reference_no = $ZohoData['data'][0]['TX_Bill_No'];
        
        $shipping_method = $ZohoData['data'][0]['TX_Bill_Trans_Option'];
      
        #Total Weighrt in grams
        $order_weight =$FinalWeight;

        $order_pieces = $ZohoData['data'][0]['Total_CTNs_TX'];
        
        $cargotype ="W";
        
        $order_status ="P";
        
        $mail_cargo_type ="4";
        
        $custom_hawbcode = $ZohoData['data'][0]['TX_Bill_No'];
        
        $userId	=  $ZohoData['data'][0]['Owner']['id'];
        
         $CustomerPO	=  $ZohoData['data'][0]['PO'];
        
        
        
        
        
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
        $shipper_company="PromoWare";
        $shipper_countrycode ="CN";
        $shipper_province	=$ZohoUserData['users'][0]['state'];
        $shipper_city = $ZohoUserData['users'][0]['city'];
        $shipper_street = $ZohoUserData['users'][0]['street'];
        $shipper_postcode = $ZohoUserData['users'][0]['zip'];
        $shipper_mobile = $ZohoUserData['users'][0]['mobile'];
        $shipper_telephone= $ZohoUserData['users'][0]['phone'];
        
        
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
        
        $Final_order_weight= $order_weight / $order_pieces;


    

    $jsonFright='{
	"reference_no": "'.$reference_no.'",
	"order_pieces": "",
	"shipping_method": "'.$shipping_method.'",
	"order_weight": "",
	"order_status": "D",
	"mail_cargotype": "'.$mail_cargo_type.'",

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
	"invoice": [{
		"invoice_enname": "'.$invoice_enname.'",
		"invoice_cnname": "'.$invoice_cnname.'",
		"invoice_quantity": "'.$quantityFinal.'",
		"hs_code": "'.$hs_code.'",
		"invoice_unitcharge": "'.$Unit_Price.'",
		"invoice_spec": "'.$invoice_spec.'",
		"invoice_use": "'.$invoice_use.'",
		"invoice_material": "'.$invoice_material.'",
		"unit_code": "PCE",
		"invoice_brand": "'.$invoice_brand.'"
	  

	  
	  
	}],
	  "cargovolume":[{
		"child_number": "3465345",
		"involume_length": "f651e053023124210cbf573e7520c7ae",
		"involume_width": "40",
		"involume_height": "20",
		"involume_grossweight": "'.$Final_order_weight.'"
	  }]
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
                   $sqlquery = "INSERT INTO zohoChinaInvoices (id,con_no,ship_no,zoho_id,website_order_id,status,datee,time_update,dupStatus,order_id) VALUES 
    (NULL, '$warehouseOrderID', '$channel_hawbcode', '$ZohoCrmIdID','$orderIdNumber','processing','$timestamp','','','$refrence_no')";
    if ($conn->query($sqlquery) === true)
    {
        //echo "record inserted successful
        
    }
                
                
                
                
                
                
                
            }
            
            
            
            
            

}


