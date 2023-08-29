<?php

/**
Plugin Name: Donation Form Plugin
Description: Plugin for Donation
Author: Zamba
Version: 1
*/
//global $change_version;
//$change_version = '1.0';
#Project Started Date - 29/08/2021
#Dev - Preet(Suraj Mehta)
#Description - Create Plugin for donation in stripe on bassis of one time and Recurring payment
#Function For create Menus and submenus on the admin Dashboard


function create_tb() {
    global $wpdb;
  //  global $change_version;

    $table_name = $wpdb->prefix . 'cre_det';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id int(9) NOT NULL AUTO_INCREMENT,
        token VARCHAR(300) NOT NULL,
        token_secret VARCHAR(300) NOT NULL,



        PRIMARY KEY  (id)
    ) $charset_collate;";



    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
$Query="INSERT INTO `pjay_cre_det` (`id`, `token`, `token_secret`) VALUES
(1, 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX')";
        $restt=$wpdb->get_results($Query);

}
register_activation_hook(__FILE__, 'create_tb' );

// Delete table
function drop_tb()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cre_det';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
   // delete_option('change_version');
}

register_deactivation_hook(__FILE__, 'drop_tb');



function admin_menu()
{    

add_menu_page('Donation Form', 'Donation Form', 'manage_options', 'donation-slug', 'donation_form');

add_submenu_page( 'donation-slug', 'User Credentials', 'User Credentials', 'manage_options', 'user-cred-slug', 'user_credent');



}
#Add Menu Hook
add_action('admin_menu', 'admin_menu');

function user_credent(){
global $wpdb;
$keyQry = "SELECT * FROM `pjay_cre_det`";

#Get Results From Execute Query
$rest=$wpdb->get_results($keyQry);

foreach($rest as $keyy)
{
    $clientKey=$keyy->token;
    $clientSecret=$keyy->token_secret;
}

?>
<body>
<style>
form.c-key {
    background: #f1f2f8;
    padding: 40px;
    width: 100%;
    max-width: 600px;
}


form.c-key input[type="submit"] {
    padding: 16px 36px!important;
    background: #232d82;
    border: none;
    color: #fff;
    width: 20%!important;
    text-align: left;
    border-radius: 0;
    position: relative;
    cursor: pointer;
    font-size: 16px;
    margin: 0;
}
form.c-key input {
    padding: 16px!important;
    border-style: solid;
    border-width: 0 0 1px;
    border-color: #f1f2f8;
    border-radius: 0;
    width: 100%;
    display: block;
    line-height: 0;
    min-height: unset!important;
    margin: 0 auto;
}
form.c-key label {
    color: darkblue;
    font-size: 16px;
    font-family: sans-serif;
    font-weight: bolder;
    display: block;
    line-height: 0;
}
</style>


<div>
  <form method="POST" class="c-key">
  <h3>Enter Client credentials</h3>
    <label for="key">Client Key</label>
    <input type="text" id="key" name="key" value="<?php echo $clientKey; ?>"><br>

    <label for="secret">Client Secret</label>
    <input type="text" id="secret" name="secret" value="<?php echo $clientSecret; ?>"><br>


    <input type="submit" value="Submit" name="submit">
  </form>
</div>

</body>
<?php

if(isset($_POST['submit']))
{
    $keyField=$_REQUEST["key"];
    $secretField=$_REQUEST["secret"];


    $qryy="UPDATE `pjay_cre_det` SET `token` = '$keyField' , `token_secret` = '$secretField' WHERE `pjay_cre_det`.`id` = 1";
    #Get Results From Execute Query
$res=$wpdb->get_results($qryy);
if($res)
{
    echo 'gdfsgs';
}
}


}

function donation_form()
{

     $site_url =  get_site_url(); 

require ('tab.html');


global $wpdb;
$keyQry = "SELECT * FROM `pjay_cre_det`";

#Get Results From Execute Query
$rest=$wpdb->get_results($keyQry);


foreach($rest as $keyy)
{
    $clientKey=$keyy->token;
    $clientSecret=$keyy->token_secret;
}


if(isset($_POST["btn_monthly"]))
{
#monthly-------------------------
$amount_monthly=$_POST['amount_monthly'];
$name_monthly=$_POST['name_monthly'];
$surname_monthly=$_POST['surname'];
$email_monthly=$_POST['email_monthly'];
$card_owner_monthly=$_POST['card_owner_monthly'];
$cvv_monthly=$_POST['cvv_monthly'];
$card_monthly=$_POST['card_number_monthly'];
$month_monthly=$_POST['month_monthly'];
$year_monthly=$_POST['year_monthly'];
$currentStripeAmount = $amount_monthly."00";

if($amount_monthly == "50"){
	
	$productPlan = $_POST['price'];
	
}else if($amount_monthly == "100"){
	
	$productPlan = $_POST['price'];

}else if($amount_monthly == "500"){
	
	$productPlan = $_POST['price'];
	
}else if($amount_monthly == "1000") 
{
	$productPlan = $_POST['price'];
	
}else{
	
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/prices');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "unit_amount=$currentStripeAmount&currency=aud&recurring[interval]=month&product=prod_KxgqFnvmoFp6Tr");
curl_setopt($ch, CURLOPT_USERPWD, "$clientSecret" . ':' . '');
$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);	
$json_decode = json_decode($result);
$productPlan = $json_decode->id;
}




$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/tokens');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "card[number]=$card_monthly&card[exp_month]=$month_monthly&card[exp_year]=$year_monthly&card[cvc]=$cvv_monthly");
curl_setopt($ch, CURLOPT_USERPWD, "$clientSecret" . ':' . '');

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

$json = json_decode($result);



$code=$json->error->code;
 $message=$json->error->message; 
 # message
 
#condition to display the failed status
if (!empty($code)) {
?>
<script>
swal("Payment Failed!", "<?php echo $message;  ?>")
</script>
<?php
}

$token_id = $json->id;

if(!empty($token_id)){
    
#for subscritpion
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/customers');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "name=$name_monthly&email=$email_monthly");
curl_setopt($ch, CURLOPT_USERPWD, "$clientSecret" . ':' . '');

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

$create_customer = json_decode($result);

$customer_id = $create_customer->id;    
    

if(!empty($customer_id)){
    
   
    
 $ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/customers/$customer_id/sources");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "source=$token_id");
curl_setopt($ch, CURLOPT_USERPWD, "$clientSecret" . ':' . '');

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);

$json = json_decode($result);



$customerr=$json->customer;

  // print_r($json);

}   
    
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/subscriptions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "customer=$customerr&items[0][price]=$productPlan&&metadata[user_name]=$name_monthly $surname_monthly&metadata[email]=$email_monthly&metadata[method]=Montly&metadata[amount]=$amount_monthly");

curl_setopt($ch, CURLOPT_USERPWD, "$clientSecret" . ':' . '');

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
$json = json_decode($result);
$id=$json->id;

if (!empty($id)) {
?>
<script>
swal("Payment Successfull!", "Payment is Done!", "success")
</script>
<?php
}else{
	$message = $json->error->message;
	
?>
<script>
swal("Payment Failed!", "<?php echo $message;  ?>")
</script>
<?php	
	
}




}

if(isset($_POST["btn_otp"]))
{
$amount_otp = $_POST['amount']*100;
$name_otp=$_POST['name_OTP'];
$lname=$_POST['surname'];
$email_OTP=$_POST['email_OTP'];
$card_owner_OTP=$_POST['card_owner_OTP'];
$cvv_OTP=$_POST['cvv_OTP'];
$card_OTP=$_POST['card_OTP'];
$month_OTP=$_POST['month_OTP'];
$year_OTP=$_POST['year_OTP'];


    $ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/tokens');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "card[number]=$card_OTP&card[exp_month]=$month_OTP&card[exp_year]=$year_OTP&card[cvc]=$cvv_OTP");
curl_setopt($ch, CURLOPT_USERPWD, "$clientSecret" . ':' . '');
$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
$json = json_decode($result);
$token_id = $json->id;
$code = $json->error->code;
$message = $json->error->message;
#condition to display the failed status
if (!empty($code)) {
?>
<script>
swal("Payment Failed!", "<?php echo $message;  ?>")
</script>
<?php
}

curl_close($ch);


if(!empty($token_id)){
    
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/charges');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, "amount=$amount_otp&currency=aud&source=$token_id&description=$email_OTP&receipt_email=$email_OTP&metadata[user_name]=$name_otp $lname&metadata[email]=$email_OTP&metadata[method]=One_Time");

curl_setopt($ch, CURLOPT_USERPWD, "$clientSecret" . ':' . '');

$headers = array();

$headers[] = 'Content-Type: application/x-www-form-urlencoded';

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);    


$data = json_decode($result);
 $id=$data->id;

if (!empty($id)) {
?>
<script>
swal("Payment Successfull!", "Payment is Done!", "success");
//location.reload();
</script>
<?php
}



	
}
}}
 add_shortcode('donation_shortcode', 'donation_form');

?>