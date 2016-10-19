<?php

function sendPush ($time, $message, $title, $installations) {

    //アプリケーションキー指定
    $application_key = 'APPLICATION_KEY';
    $client_key      = 'CLIENT_KEY';

    //リクエスト作成
    $method = 'POST';
    $fqdn   = 'mb.api.cloud.nifty.com';
    $api_version = '2013-09-01';
    $path        = 'push';
    $timestamp = date(DATE_ISO8601, time());
    $url = "https://" . $fqdn . "/" . $api_version . "/" . $path;

    //シグネチャー計算
    $header_string  = "SignatureMethod=HmacSHA256&";
    $header_string .= "SignatureVersion=2&";
    $header_string .= "X-NCMB-Application-Key=".$application_key . "&";
    $header_string .= "X-NCMB-Timestamp=".$timestamp;
    $signature_string  = $method . "\n";
    $signature_string .= $fqdn . "\n";
    $signature_string .= "/" . $api_version . "/" . $path . "\n";
    $signature_string .= $header_string;
    $signature = base64_encode(hash_hmac("sha256", $signature_string, $client_key, true));

    // 検索条件
    if(is_array($installations)){
      $searchCondition = array(
        "objectId" => array (
           "\$inArray" => $installations
        )
      );
    } else {
      $searchCondition = array (
        "objectId" => $installations
      );
    }

    //時間指定
    if ($time == "now" ) {
      $data = array(
        "immediateDeliveryFlag" => true,
        "message" => $message,
        "title" => $title,
        "searchCondition" => $searchCondition
      );
    } else {
      $data = array(
        "deliveryTime" => array(
                          "__type" => "Date",
                          "iso" => $time
                        ),
        "message" => $message,
        "title" => $title,
        "searchCondition" => $searchCondition
      );
    }

    //ヘッダー指定
    $headers = array(
        'Content-Type: application/json',
        'X-NCMB-Application-Key: '.$application_key,
        'X-NCMB-Signature: '.$signature,
        'X-NCMB-Timestamp: '.$timestamp
    );
    $options = array('http' => array(
        'method' => 'POST',
        'content' => stripcslashes(json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)),
        'header' => implode("\r\n", $headers),
        'ignore_errors' => true
    ));
    $push_contents = file_get_contents($url, false, stream_context_create($options));
    print($push_contents);

}

sendPush ("DELIVERY_TIME", "MESSAGE", "TITLE", "INSTALLATION");
//sendPush("now", "メッセージ", "タイトル", "objectId");
?>
