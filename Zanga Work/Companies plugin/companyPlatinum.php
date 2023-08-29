

<?php
require('../../../wp-config.php');
#Global Database 
global $wpdb;

echo'<pre>';







$args2 = array(
    'role'    => 'corporate_-_gold_admin',
);
$users2 = get_users( $args2 );

$args3 = array(
    'role'    => 'corporate_-_platinum_admin',
);
$users3 = get_users( $args3 );



$data = array_merge($users2,$users3);



echo '<pre>';





foreach($data as $allUsers){
    

    
    $userId = $allUsers->ID;
    
    $userName = $allUsers->user_nicename;
    
     $role = $allUsers->role;
     
    
    
    
    

    $user_meta=get_user_meta($userId);
    

    $companName=$user_meta['billing_company'][0];

    if (empty($companName)) {

        $companyName= $userName." - No Associated Company";
    }
    else
    {
        $companyName=$user_meta['billing_company'][0];
    }



   // $user_roles=$user_meta['role'][0];

                   $author_obj = get_user_by('id', $userId);
                $real=$author_obj->data;
                
                $realrole=$author_obj->roles;
                #Author Role
                $roleuser=$realrole[0];
  


    $Companies = "SELECT * FROM `pjay_postmeta` WHERE `meta_key` = 'company_name' AND `meta_value` LIKE '$companyName'";

#Get Results From Execute Query
$rest=$wpdb->get_results($Companies);

foreach($rest as $companyy)
{


        #Comapanies Name    
        $company_names = $companyy->meta_value;
        
    
        #Comapanies Id
        $company_id = $companyy->post_id;
} 


 

    $userdat="SELECT * FROM `pjay_postmeta` WHERE `meta_key` LIKE '_customer_user' AND `meta_value` LIKE '$userId'";
    
     $tr_res=$wpdb->get_results($userdat);
    
    
     
     $userpostid= $tr_res[0]->post_id;



      $tradeCompanies="SELECT * FROM `pjay_postmeta` WHERE `post_id` =  $userpostid AND `meta_key` = '_billing_trade__name'";

$trade_res=$wpdb->get_results($tradeCompanies);
    


  if(count($trade_res) == 0){
      
 $trade_title = "  ";  
}
  else
    {     
        $trade_title= $trade_res[0]->meta_value;
    }
 
    #------------------trade----------

        $a = $roleuser;
        
        #Condition to arange role Properly
        if (strpos($a, 'gold') !== false) {
    $usr_role="Gold Admin";
} 
elseif (strpos($a, 'bronze') !== false) {
    $usr_role="Bronze Admin";
} 
elseif (strpos($a, 'platinum') !== false) {
    $usr_role="Platinum Admin";
} 
elseif (strpos($a, 'silver') !== false) {
    $usr_role="Silver Admin";
} 
elseif (strpos($a, 'small_business') !== false) {
    $usr_role="Small Business Admin";
} 
elseif (strpos($a, 'individual') !== false) {
 $usr_role="Individual";
}
else
{
     $usr_role="-";
}



                     
                        $getCompanies="SELECT * FROM `company_users` WHERE `user_id` = '$userId'";
                        $comp_res=$wpdb->get_results($getCompanies);
                        $total_result_rows = $wpdb->num_rows;
                        if($total_result_rows == 0)
                          {
                           $qryInsert="INSERT INTO `company_users` (`user_id`, `user_name`, `company`, `company_id`, `membership_type`)
VALUES ('$userId', '$userName', '$companyName', '', '$usr_role')";

							$trade_res=$wpdb->get_results($qryInsert);
                              
                              echo 'Successfully Added'; 
                          }
    else 
    {
        echo 'No Record to  be added '; 
    }

                         
    
 
                           
                            
                            
        
}
        ?>