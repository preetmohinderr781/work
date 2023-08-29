<?php

#Variables For the mail to be sent as notification ######
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

#Calling the autoload file
require 'smtp/vendor/autoload.php';

#mail Sender
$sender = 'promowares86@gmail.com';

#mail Sender Name
$senderName = 'santosh';


#mail recipient
$recipient = 'jeff@promowares.com';

#UserName SMTP
$usernameSmtp = 'AKIA4XJY64523WF6GJ42';

#UserPassword SMTP
$passwordSmtp = 'BFAh+vcbbbU9JmVZ5F/XsSBvtFuUHc//FC2aRb1wjqff';

#configurationSet
$configurationSet = 'ConfigSet';

# AWS Host Name
$host = 'smtp.exmail.qq.com';

#Aws Port
$port = 465;




#OPEN4x APP KEY
$appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

#OPEN4x APP Secret
$appSecret = "e4b362a3-7977-4699-82f7-31fadd33cf95";

#API Version
$version = "1.0";

#Timestamp
$timestamp = time();