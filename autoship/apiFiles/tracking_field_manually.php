<?php

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276&page=1&per_page=30",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125: cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
        "Content-Type: application/json",
        "Cookie: PHPSESSID=cc5a0f2a06d08939a61b0cd3006fb5f6; designer_session_id=cc5a0f2a06d08939a61b0cd3006fb5f6",
    ],
]);

$response = curl_exec($curl);

curl_close($curl);

$WooData = json_decode($response);

$tracking_numbers = [];

echo "<pre>";
//print_r($WooData);
foreach ($WooData as $WooKey) {
    echo $wooId = $WooKey->id;

    $WooShipingStatus = $WooKey->status;
    echo "<br>";

    if (
        $WooShipingStatus == "shipping-label" ||
        $WooShipingStatus == "processing" ||
        $WooShipingStatus == "picked"
    ) {
        $WooShipingStatus;
        echo "<br>";

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://www.promowares.com/wp-json/wc/v3/orders/$wooId/shipment-trackings?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
                "ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125: cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
                "Content-Type: application/json",
                "Cookie: PHPSESSID=4964214755b8161e9789543486e0d3d6; designer_session_id=4964214755b8161e9789543486e0d3d6",
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        $decode = json_decode($response);
        print_r($decode);
        foreach ($decode as $checkTrack) {
            array_push($tracking_numbers, $checkTrack->tracking_number);
            $tracking_provider = $checkTrack->tracking_provider;
        }

        #OPEN4x APP KEY
        $appKey = "db93e435-8205-494d-aa42-6a4b8e466289";

        #OPEN4x APP Secret
        $appSecret = "e4b362a3-7977-4699-82f7-31fadd33cf95";

        #API Version
        $version = "1.0";

        #Timestamp
        $timestamp = time();

        #Get list inbound api request headers
        $jsonDataGetInbound = "{
                	'sales_no': '$wooId',
                	'page_no': 1,
                	'page_size': 10
                }";
        # MD5 Encryption for generating the signature
        $rawSignatureSKU =
            "app_key" .
            $appKey .
            "formatjsonmethodfu.wms.outbound.getlisttimestamp" .
            $timestamp .
            "v1.0" .
            $jsonDataGetInbound .
            "e4b362a3-7977-4699-82f7-31fadd33cf95";

        #md5 encrypt
        $signatureSKU = md5($rawSignatureSKU);

        #Inventory API
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.outbound.getlist",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "$jsonDataGetInbound",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676",
            ],
        ]);

        #Execute curl
        $response = curl_exec($curl);

        #json to array
        $InBoundListData = json_decode($response);
        $DataArrayInBoundList = $InBoundListData->data->data;
        echo "<pre>";
        //print_r($DataArrayInBoundList);
        foreach ($DataArrayInBoundList as $keyy) {
            $status = $keyy->status;
            $shipping_no = $keyy->shipping_no;

            if ($status == "S" || $status == "P" || $status == "C") {
                $consignment_noo[] = $keyy->consignment_no;
            }
        }
    }
}

$woocommerce_order_ids = [];

foreach ($consignment_noo as $consignment_no) {
    //print_r($consignment_no);
    #__________________________________

    #Get list inbound api request headers
    $jsonDataGetInbound = "{
                	'consignment_no': '$consignment_no',
                	'page_no': 1,
                	'page_size': 10
                }";
    # MD5 Encryption for generating the signature
    $rawSignatureSKU =
        "app_key" .
        $appKey .
        "formatjsonmethodfu.wms.outbound.getlisttimestamp" .
        $timestamp .
        "v1.0" .
        $jsonDataGetInbound .
        "e4b362a3-7977-4699-82f7-31fadd33cf95";

    #md5 encrypt
    $signatureSKU = md5($rawSignatureSKU);

    #Inventory API
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "http://open.4px.com/router/api/service?app_key=$appKey&v=1.0&timestamp=$timestamp&format=json&sign=$signatureSKU&method=fu.wms.outbound.getlist",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "$jsonDataGetInbound",
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Cookie: JSESSIONID=1C8125E634314301FC59626CE4153A96; SERVERID=7f878fd8b51d7d6586614dd22d20b20c|1638164875|1638150615; route=1638150616.125.395809.566676",
        ],
    ]);

    #Execute curl
    $response = curl_exec($curl);

    #json to array
    $InBoundListData = json_decode($response);
    $DataArrayInBoundList = $InBoundListData->data->data;
    echo "<pre>";

    //print_r($DataArrayInBoundList);
    foreach ($DataArrayInBoundList as $keyy) {
        $status = $keyy->status;
        $shipping_no = $keyy->shipping_no;

        $logistics_product_code = $keyy->logistics_product_code;

        if ($logistics_product_code == "F134") {
            $logisticShipping = "FedEx Ground";
        } elseif ($logistics_product_code == "F132") {
            $logisticShipping = "FedEx";
        } elseif ($logistics_product_code == "F126") {
            $logisticShipping = "UPS";
        } elseif ($logistics_product_code == "F123") {
            $logisticShipping = "UPS";
        }
        $wooCommerceID = $keyy->sales_no;
        $shipping_no = $keyy->shipping_no;
        $logisticShipping;

        array_push($woocommerce_order_ids, $wooCommerceID);
        // echo $wooCommerceID.'-'.$shipping_no;

        #__________________________________

        print_r($tracking_numbers);

        if (!in_array($shipping_no, $tracking_numbers)) {
            ////////////////////////////////////////////////////////////////////////
            echo $consignment_no = $keyy->consignment_no;
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://www.promowares.com/wp-json/wc-ast/v3/orders/$wooCommerceID/shipment-trackings?consumer_key=ck_3ba8965b5cb3f257a224d9fb3eaa48315aa65125&consumer_secret=cs_f7fea99ab0d5d80201328bbd4dfd57bc5b238276",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS =>
                    '{
                            		"tracking_provider": "' .
                    $logisticShipping .
                    '",
                            		"tracking_number": "' .
                    $shipping_no .
                    '"
                            }
                            ',
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Cookie: PHPSESSID=0408cd286afe002c9dccea4a62effd17; designer_session_id=0408cd286afe002c9dccea4a62effd17",
                ],
            ]);

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;
        }
    }
}
