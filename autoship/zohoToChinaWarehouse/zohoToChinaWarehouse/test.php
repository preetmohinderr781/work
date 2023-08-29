<head>
<script src="jquery-3.6.0.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<?php


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
echo $token=$data['access_token'];


?>

<script>

var token = "<?php echo $token; ?>";
var settings = {
  "url": "https://www.zohoapis.com/crm/v2/Sales_Orders",
  "method": "GET",
  "timeout": 0,
  "headers": {
    "Authorization": "Zoho-oauthtoken 1000.e449aeec0907f0bc77876ab9a0850f4e.c50a3cdd71ea1a96e0f34d3cfb415826",
    "Cookie": "1ccad04dca=d29e417f368f50fa25b6be760117403f; _zcsr_tmp=390d2baa-6cb1-4736-8e16-29569a1ba1ff; crmcsr=390d2baa-6cb1-4736-8e16-29569a1ba1ff"
  },
};

$.ajax(settings).done(function (response) {
  console.log(response);
});
    
</script>