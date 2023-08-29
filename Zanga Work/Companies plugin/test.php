<?php
require('../../../wp-config.php');
#Global Database 
global $wpdb;

echo'<pre>';



$args4 = array(
    'role'    => 'individual',
);
$users4 = get_users($args4);




//print_r($users4);

$userId="2604";
$user_meta=get_user_meta($userId);

print_r($user_meta);