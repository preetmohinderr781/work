<?php

/**

Plugin Name: Companies Records

Description: Plugin to manage the records of the companies

Author: Zamba

Version: 1
*/
//global $change_version;

//$change_version = '1.0';

#Project Started Date - 29/08/2021
#Dev - Preet(Manish Gautam)
#Description - Create Plugin for get data according to the associated Companies and Their Users




#Function For create Menus and submenus on the admin Dashboard
function mysite_admin_menu(){
    
#Add Menu Page in Admin Dashboard
add_menu_page('Members', 'Companies', 'manage_options', 'members-slug', 'members_function');


#Add Submenus page In Admin Dashboard
add_submenu_page( 'members-slug', '', '', 'manage_options', 'edit-members-slug', 'add_members_function');


#Add Submenus page In Admin Dashboard
add_submenu_page( 'user-associated', '', '', 'manage_options', 'user-associated', 'user_assoc');

#Add Submenus page In Admin Dashboard
add_submenu_page( 'orders-slug', '', '', 'manage_options', 'orders-slug', 'orders');

#Add Submenus page In Admin Dashboard
add_submenu_page( 'subscription-slug', '', '', 'manage_options', 'subscription-slug', 'subscription');





}
#Add Menu Hook
add_action('admin_menu', 'mysite_admin_menu');

#--->Function Close

function members_function($wpdb){

    ?>
<div style="    width: 98.8%;     margin-top: 2rem;     overflow: hidden;">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.0/css/dataTables.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.0/js/dataTables.bootstrap4.min.js"></script>


<script>
$(document).ready(function() {
    $('#table_id').DataTable( {
        "ordering": false,
        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select class="dropdown-postion-change"><option value=""></option></select>')
                    .appendTo( $(column.header()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    } );
} );
</script>
<style>
.record-list select.dropdown-postion-change {
    display: none;
}tr.table-headings.odd {
    font-weight: 700;
    background-color: #0000002e!important;
}
</style>
<!------Table For Companies------->
<table id="table_id" class="table table-striped table-bordered" style="width:100%">
            <!--tfoot>
            <th class="test">Admin Role</th>
            <th class="test">Admin Role</th>
            
        </tfoot-->
    <thead>
        <tr>
            <th>COMPANIES</td>         
          <th>ADMIN ROLE</td>
          <th>EDIT COMPANIES RECORDS</td>
            
        </tr>
        <tr class = "table-headings">
          <th>COMPANIES</td>         
          <th>ADMIN ROLE</td>
          <th class="record-list">EDIT COMPANIES RECORDS</td>
      </tr>
    </thead>
    <?php
    
#Global Database 
global $wpdb;


						$getCompanies="SELECT * FROM `company_users`";
							$comp_res=$wpdb->get_results($getCompanies);
							//print_r($comp_res);

							foreach($comp_res as $allCompanies)
							{		
									$userId=$allCompanies->user_id;
									$companyName=$allCompanies->company;
									//$trade_title=$allCompanies->user_id;
									$usr_role=$allCompanies->membership_type;

							   
							



    ?>
     
      <tr>
                <td><?php echo $companyName;?> </td>                
                <td><?php echo $usr_role;?> </td>
                <?php $site_url =  get_site_url(); ?>
                 <td><a href="<?php echo $site_url.'/wp-admin/admin.php?page=edit-members-slug&post_id='.$userId.'&cname='.$companyName ?> " target="_blank">Edit</a></td>     
      
                </tr>
        <?php
        
}
     $pathSilver=plugins_url( 'companyOther.php' , __FILE__ );
    $pathPlatinum=plugins_url( 'companyPlatinum.php' , __FILE__ );
    $pathOther=plugins_url( 'companySilver.php' , __FILE__ );
        ?>
    </tbody>

</table>
            
         <div class="alert">

  Note - If any Record is missing then please go to this link and wait for around 2 minutes <a href="<?php echo $pathSilver;?>"  target="_blank">Link 1</a>
      <a href="<?php echo $pathPlatinum;?>"  target="_blank">Link 2</a> <a href="<?php echo $pathOther;?>"  target="_blank">Link 3</a>

</div>   

</div>


<?php
}
#Function Close

#Function For Edit Companies Related Details
function add_members_function()
{
#Global Database    
   global $wpdb;

   #Request Companies ID From Url
   $userId=$_REQUEST["post_id"];

   $comp_name=$_REQUEST["cname"];
   
   #Get Table Prefix
   $table_prefix = $wpdb->prefix . "users";
   
   #Query For Get Information About Comapanies
   $qry="SELECT * FROM $table_prefix WHERE ID ='$userId'";


   #Get Results From Query
   $res=$wpdb->get_results($qry);


   #Post Title
   $post_title= $res[0]->display_name;
   
   #Post Title
   $post_id= $res[0]->ID;



   
                #Getting Author details 
                //$author_id = get_post_field( 'post_author', $post_id );
                $author_obj = get_user_by('id', $post_id);
               
                $real=$author_obj->data;
                
                #Author name
                $realname=$real->display_name;
                
                #author Email
                $realemail=$real->user_email;
                
                #Author Role
                $realrole=$author_obj->roles;
    
    
                $roleuser=$realrole[0];


    $qrry="SELECT * FROM `pjay_postmeta` WHERE `meta_key` LIKE '_customer_user' AND `meta_value` LIKE '$post_id'";
    $ress=$wpdb->get_results($qrry);
    
    $array= $ress[0]->post_id;

$compname=$_REQUEST["cname"];

$qrrry="SELECT * FROM `pjay_postmeta` WHERE `meta_key` LIKE 'company_name' AND `meta_value` LIKE '%$compname%'";
    $resss=$wpdb->get_results($qrrry);
    
   $real_comp= $resss[0]->post_id;



     $qqrry="SELECT * FROM `pjay_postmeta` WHERE `post_id` = $array AND `meta_key` LIKE '_billing_phone'";
    $rress=$wpdb->get_results($qqrry);
    $O_phn= $rress[0]->meta_value;
    
   // $data=maybe_unserialize($array);
   // $O_phn=$data['item-0']['phone-number'];


        $a=$roleuser;
        if (strpos($a, 'gold_admin') !== false) {
    $usr_role="Gold Admin";
} 
elseif (strpos($a, 'bronze_admin') !== false) {
    $usr_role="Bronze Admin";
} 
elseif (strpos($a, 'platinum_admin') !== false) {
    $usr_role="Platinum Admin";
} 
elseif (strpos($a, 'silver_admin') !== false) {
    $usr_role="Silver Admin";
} 
elseif (strpos($a, 'small_business_admin') !== false) {
    $usr_role="Small Business Admin";
}
elseif (strpos($a, 'individual') !== false) {
 $usr_role="Individual";
}
else
{
     $usr_role="-";
}


      ?>
      <style>
table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}
</style>
</head>
<body>
<style>
#roleuser {

  width: 40%;
}
#roleuser td, #roleuser th {
    padding: 12px;
    font-weight: 400;
    font-size: 1rem;
}

#roleuser tr:nth-child(even){background-color: #f2f2f2;}

#roleuser tr:hover {background-color: #ddd;}

#roleuser th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04AA6D;
  color: white;
}


#roleuser {
    width: 40%;
    border: 2px solid #dee2e6!important;
    border-bottom: none!important;
}
.bp-page{
    padding: 30px 0 0;
    display: block;
}


</style>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.0/css/dataTables.bootstrap4.min.css">


<?php
  $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
  echo "<a href='$url' class='bp-page'>Back Page</a>"; 
?>
<h2 style="font-size: 1.5em; font-weight: bold;margin: 0 auto 8px;"><?php echo $comp_name; ?></h2>
      <?php
        echo '<table id="roleuser" class="display" style="border-top: 2px solid #000000c4; border-bottom: 2px solid #000000c4;
}
">';
        echo "<tr>";
    #Display Membership Role
    echo "<td>Membership Level</td>";
    echo "<td>$usr_role</td>";
    echo "</tr>";
    

    
    #Display Admin name
    echo "<tr>";
    echo "<td>Admin Name</td>";
    echo "<td>$realname</td>";
    echo "</tr>";
    
    #Display Admin contact
    echo "<tr>";
    echo "<td>Admin Contact</td>";
    echo "<td>$O_phn</td>";
    echo "</tr>";
    
    #Display Admin Email
    echo "<tr>";
    echo "<td>Admin Email</td>";
    echo "<td>$realemail</td>";
    echo "</tr>";
    echo "</table>";
    



#--------------------------------------

?>
<style>
table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}
</style>
</head>
<body>
<style>
#customers {

  width: 40%;
}

#customers td, #customers th {
  
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04AA6D;
  color: white;
}
table#customers a {
    font-size: 14px;
}
table#customers b {
    font-size: 14px;
    font-weight: 500;
}
table#customers a:focus {
    box-shadow: none!important;
}
table#customers {
    border: 2px solid #dee2e6;
 }
</style>

<table id="customers" class="display" style="position: relative;">
    
    
    <tbody>
        
        

 <tr>
     <style>
         button {
      background: #005177;
    color: white;
    padding: 4px 10px;
    border-radius: 3px;
    font-size: 1rem;
    border: 2px solid #005177;
    transition: .2s all ease;
}
button:hover {
    background: #f7f7f7;
    border: 2px solid #005177;
    color: #005177;
}
     </style>
     <?php $site_url =  get_site_url(); ?>
    <td><button  onclick="window.open('<?php echo $site_url.'/wp-admin/admin.php?page=subscription-slug&post_id='.$post_id.'&post_title='.$comp_name ?>')">View Subscriptions</button></td>
 </tr>
 <tr>
       <td><button onclick="window.open('<?php echo $site_url.'/wp-admin/admin.php?page=orders-slug&post_id='.$postId.'&post_title='.$comp_name ?>')">View Orders</button></td>
</tr>

 <tr>
    <td><button onclick="window.open('<?php echo $site_url.'/wp-admin/admin.php?page=user-associated&cname='.$comp_name; ?>')">View Associated Users</button></td>
 </tr>
 
 <tr>
    <td><button onclick="window.open('<?php echo $site_url.'/wp-admin/post.php?post='.$real_comp.'&action=edit&classic-editor'; ?>')">Edit Directory Listing</button></td>
 </tr>
 
 
 
</tbody>
</table>


<?php

}

#function for get Users orders data
function orders(){

?>
<div style="    width: 98.8%;     margin-top: 2rem;     overflow: hidden;">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.0/css/dataTables.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.0/js/dataTables.bootstrap4.min.js"></script>

<?php
  $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
  echo "<a href='$url'>Back Page</a>"; 
?>

<h4><b>Order - <?php echo $_REQUEST['post_title']; ?></b></h4>

<table id="Userorder" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>

            <th>Customer Name</th>
            <th>Total Orders</th>
            <th>Action</th>
            
        </tr>
        
    </thead>
    
    <tbody> 
    
    
<?php    
  
#Global Database
global $wpdb;

#Get Table Prefix
$table_prefix = $wpdb->prefix . "usermeta";

#Request Companies ID From Url
$PostId = $_REQUEST["post_id"]; 

$PostTitle = $_REQUEST["post_title"];

#Meta Key
$meta_key = "billing_company";
    
#Query For Get Users From User Meta With Associated Companies
$users = "SELECT * FROM $table_prefix WHERE meta_key ='$meta_key' and meta_value LIKE '%$PostTitle%'";

#Get Results 
$UserData = $wpdb->get_results($users);

#condition If no User available
if(count($UserData) == 0){

 echo "<b style='color:red;'>NO Data Available</b>";
 
die(); 
    
}



#Loop Over Users
Foreach($UserData as $Data){
    
    #Fetch User ID 
    $user_id = $Data->user_id;
    
    #Get User Details
    $UserMetaData = get_userdata( $user_id );
    
    $userRole = $UserMetaData->roles[0];

    #User Display Name
    $DisplayName = $UserMetaData->data->display_name;

    if ( strstr( $userRole, 'admin' ) ) {
              
            $DisplayName = $DisplayName. " <span style ='color:green'> - Admin</span>";  
              
            } 
    
    
    #Put Customer ID in Array
    $args = array(
    
    'customer_id' => $user_id
    
    );
    
    #Get Orders Using Customer ID
    $orders = wc_get_orders($args);

 
    
    foreach($orders as $array)
    {
         $orderrr=$array->get_data();
         $ord_id=$orderrr['id'];

    }
    

    #Count Total Orders Of Users
    $countOrder= count($orders);
   

  
    
    #Table Row
    echo "<tr>";
    
    #Display Customer Name In Table
    echo "<td>$DisplayName</td>";
    
    #Count Total Orders Of User
    echo "<td>$countOrder</td>";
    
    ?>
    
    <td><?php  #if No Order Exist
    if($countOrder == 0){
        
    echo $button = "<b style = 'color:grey;font-weight: 400;'>No Order Available</b>";
        
    }
else{
    $site_url =  get_site_url();
        foreach($orders as $array)
    {
        
         $orderrr=$array->get_data();
         
         
          $create_date = $array ->get_date_created();
          
           $new_date = date("d-m-Y",strtotime($create_date));
    
         $ord_id=$orderrr['id'];
     echo   $button = $new_date."&nbsp&nbsp&nbsp&nbsp<a href = $site_url/wp-admin/post.php?post=$ord_id&action=edit>Check Order</a><br>";

    }
        
      
    } ?></td>
    <?php
    #Close Table Row
    echo "</tr>";

  #Loop Close
  }
      #Body Close
      echo "</tbody>";

       #Table Close
       echo "</table>";


?>
<script>
    $(document).ready(function() {
    $('#Userorder').DataTable( {
        "order": [[ 1, "desc" ]]
    } );
} );
</script>
</div>
<?php
}







#function for get Users orders data
function subscription(){

?>

    <div style="    width: 98.8%;     margin-top: 2rem;     overflow: hidden;">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.0/css/dataTables.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.0/js/dataTables.bootstrap4.min.js"></script>

<?php
  $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
  echo "<a href='$url'>Back Page</a>"; 
?>

<h4><b>Subscriptions - <?php echo $_REQUEST['post_title']; ?></b></h4>

<table id="Usersub" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
        
            <th>Users</th>
            <th>Subscription</th>
            <th>Action</th>
            
        </tr>
        
    </thead>
    
    <tbody> 

<?php    
  
#Global Database
global $wpdb;

#Get Table Prefix
$table_prefix = $wpdb->prefix . "usermeta";

#Request Companies ID From Url
$userId = $_REQUEST["post_id"];

$PostTitle = $_REQUEST["post_title"];

#Meta Key
$meta_key = "billing_company";

 #Getting Author details 
if($userId)
{

                $author_obj = get_user_by('id', $userId);
            
                
                $real=$author_obj->data;
                
                #Author name
                $realname=$real->display_name;
                
                #Real Role
                $realrole=$author_obj->roles;
                
                #Author Role
                $roleuser=$realrole[0];

                $author_id=$author_obj->ID;

 #Put Customer ID in Array
    $args = array(
    
    'customer_id' => $author_id
    
    );
    

    #Get subscriptions Using Customer ID
    $subscriptions = wcs_get_subscriptions( $args );
    
    foreach($subscriptions as $array)
    {
         $subscriptionn=$array->get_data();
         
         $subid=$subscriptionn['id'];
    }

   #Count Total subscriptions Of Users
   $countOrder= count($subscriptions);
   

    #if No Order Exist
    if($countOrder == 0){
        
     $button = "<b style = 'color:grey; font-weight: 400;'>No Subscription Available</b>";
        
    }
    #if subscriptionsfound
    else{
        
     //$button = "<a href = $site_url/wp-admin/admin.php?page=subscription-detail-slug&user_id=$user_id'>Check subscriptions</a>";
     
     $site_url =  get_site_url();
     
        $button = "<a href = $site_url/wp-admin/post.php?post=$subid&action=edit>Check subscription</a>";       
    }

echo "<tr>
        
            <td>$realname <span style ='color:green'> - Admin</span></td>
            <td>$countOrder</td>
            <td>$button</td>

        </tr>";
}

    #--------------------------
#Query For Get Users From User Meta With Associated Companies
$users = "SELECT * FROM $table_prefix WHERE meta_key LIKE '$meta_key' and meta_value LIKE '%$PostTitle%'";

#Get Results 
$UserData = $wpdb->get_results($users);

#condition If no User available
if(count($UserData) == 0){

 echo "<b style='color:red;'>NO Data Available</b>";
 
    
}

#Loop Over Users
Foreach($UserData as $Data){
    
    #Fetch User ID 
    $user_id = $Data->user_id;
    
    #Get User Details
    $UserMetaData = get_userdata( $user_id );
    
    #user role 
    $userRole = $UserMetaData->roles[0];
    
    #User Display Name
    $DisplayName = $UserMetaData->data->display_name;
    
       
    
    
    
    #Put Customer ID in Array
    $args = array(
    
    'customer_id' => $user_id
    
    );
    

    #Get subscriptions Using Customer ID
    $subscriptions = wcs_get_subscriptions( $args );
    
    foreach($subscriptions as $array)
    {
         $subscriptionn=$array->get_data();
         
         $subid=$subscriptionn['id'];
    }

   #Count Total subscriptions Of Users
   $countOrder= count($subscriptions);
   

    #if No Order Exist
    if($countOrder == 0){
        
     $button = "<b style = 'color:grey; font-weight: 400;'>No Subscription Available</b>";
        
    }
    #if subscriptionsfound
    else{
        
     //$button = "<a href = $site_url/wp-admin/admin.php?page=subscription-detail-slug&user_id=$user_id'>Check subscriptions</a>";
     
     $site_url =  get_site_url();
     
        $button = "<a href = $site_url/wp-admin/post.php?post=$subid&action=edit>Check subscription</a>";       
    }

     if ( strstr( $userRole, 'admin' ) ) {
              
            $DisplayName = '';  
            $countOrder = '';  
             $button = '';  
              
            } 
    
    #Table Row
    echo "<tr>";
    
    #Display Customer Name In Table
    echo "<td>$DisplayName</td>";
    
    #Count Total Orders Of User
    echo "<td>$countOrder</td>";
    ?>

    <?php $site_url =  get_site_url(); ?>
    <td><?php echo $button; ?></td>
    <?php
    #Close Table Row
    echo "</tr>";

  #Loop Close
  }
      #Body Close
      echo "</tbody>";

       #Table Close
       echo "</table>";
?>
<script>
    $(document).ready(function() {
    $('#Usersub').DataTable( {
        "order": [[ 1, "desc" ]]
    } );
} );
</script>
</div>
<?php
}

function user_assoc()
{
    ?>
    <div style="    width: 98.8%;     margin-top: 2rem;     overflow: hidden;">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.0/css/dataTables.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.0/js/dataTables.bootstrap4.min.js"></script>

<?php
  $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
  echo "<a href='$url'>Back Page</a>"; 
?>

<h4><b>Associated Users - <?php echo $_REQUEST['cname']; ?></b></h4>

<table id="Users" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
            <th>Customer ID</th>
            <th>Customer Name</th>
            <th>Email</th>
            <th>Registration Date</th>
            <th>Edit User</th>
        </tr>
    </thead>
    <tbody>  
    <?php
    #Global Database
global $wpdb;

#Get Table Prefix
$table_prefix = $wpdb->prefix . "usermeta";

#Request Companies ID From Url
$PostId = $_REQUEST["post_id"]; 

$PostTitle = $_REQUEST["cname"];

#Meta Key
$meta_key = "billing_company";
    
#Query For Get Users From User Meta With Associated Companies
$users = "SELECT * FROM $table_prefix WHERE meta_key ='$meta_key' and meta_value LIKE '%$PostTitle%'";

#Get Results 
$UserData = $wpdb->get_results($users);

//print_r($UserData);
#condition If no User available
if(count($UserData) == 0)
{
    echo "<b style='color:red;'>NO Data Available</b>";
}

Foreach($UserData as $Data){
    
    #Fetch User ID 
    $user_id = $Data->user_id;
    
    #Get User Details
    $UserMetaData = get_userdata( $user_id );
    #User Display Name

    $userRole = $UserMetaData->roles[0];

    $DisplayName = $UserMetaData->data->display_name;

if ( strstr( $userRole, 'admin' ) ) {
              
            $DisplayName = $DisplayName. " <span style ='color:green'> - Admin</span>";  
              
            } 


    #User Display email
    $useremail = $UserMetaData->data->user_email;
    #User Display date
    $user_reg = $UserMetaData->data->user_registered;
    
    $site_url =  get_site_url();
$button = "<a href = $site_url/wp-admin/user-edit.php?user_id=$user_id>Edit User</a>";

#Table Row
    echo "<tr>";
    
    #Display Customer ID In Table
    echo "<td>$user_id</td>";
    
    #Display Customer Name In Table
    echo "<td>$DisplayName</td>";
    
    #User name
    echo "<td>$useremail</td>";
    
    #User regestration date
    echo "<td>$user_reg</td>";
    
    #User link
    echo "<td>$button</td>";
           
    
    #Close Table Row
    echo "</tr>";

  #Loop Close
}   
?>
</table>
<script>
    $(document).ready(function() {
    $('#Users').DataTable();
} );
</script>

    <?php
}
?>