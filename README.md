# [REST API] サーバからプッシュ通知を送ってみましょう！【PHP編】
*2016/10/18作成*

![画像1](/readme-img/zentai.png)

## 概要

 * [ニフティクラウドmobile backend](http://mb.cloud.nifty.com/)の『プッシュ通知』をサーバから登録するためのサンプルです
 * ニフティクラウドmobile backendが提供している[REST API](http://mb.cloud.nifty.com/doc/current/rest/common/format.html)を利用することで、簡単にサーバ環境からもプッシュ通知送信実装可能
 * サンプルを動かすことは簡単ですぐに [ニフティクラウドmobile backend](http://mb.cloud.nifty.com/)の機能を体験いただけます★☆

## ニフティクラウドmobile backendって何？

 スマートフォンアプリのバックエンド機能（プッシュ通知・データストア・会員管理・ファイルストア・SNS連携・位置情報検索・スクリプト）が**開発不要**、しかも基本**無料**(注1)で使えるクラウドサービス！

 注1：詳しくは[こちら](http://mb.cloud.nifty.com/price.htm)をご覧ください

## プッシュ通知の仕組み

* ニフティクラウドmobile backendのプッシュ通知は、各プラットフォームが提供している通知サービスを利用しています
 * Androidの通知サービス __FCM（Firebase Cloud Messaging）__

 ![画像a1](/readme-img/a001.png)

 ※ FCMはGCM(Google Cloud Messaging)の新バージョンです。既にGCMにてプロジェクトの作成・GCMの有効化設定を終えている場合は、継続してご利用いただくことが可能です。新規でGCMをご利用いただくことはできませんので、あらかじめご了承ください。

 * iOSの通知サービス　__APNs（Apple Push Notification Service）__

 ![画像i1](/readme-img/i001.png)

* 上図のように、アプリ（Monaca）・サーバー（ニフティクラウドmobile backend）・通知サービス（FCMあるいはAPNs）の間で認証が必要になります

* 今回サーバからプッシュ通知を送信するサンプルを動作確認するために、AndroidかiOSで端末登録済みの状態必要があります。



## 環境の準備！

* 本サンプルはPHP v.5.4〜動作確認しております。
* 本サンプルを動かすためにPHP環境を用意する必要がありますので、以下のどちらをご参考いただき、動作環境をご用意してください。
  * ローカル環境
    * MACの場合：PHPが入っております、そのまま、ターミナルで確認できます。
    * Windowsの場合、こちらの[URL](http://php.net/manual/ja/install.windows.php)ご参考し、インストールを行ってください。
  * サーバ環境：デフォルトでPHPが入っていない場合、こちらの[URL](http://php.net/manual/ja/install.php)をご参考し、インストールを行ってください。

# 作業の手順

### 0. 受信するアプリの準備

ニフティクラウドmobile backendが提供しているプッシュ通知のクイックスタートアプリをご利用いただくことで、すぐ受信アプリを準備できます。
  * iOS:　[Objective-C](https://github.com/NIFTYCloud-mbaas/ObjcPushApp) / [Swift](https://github.com/NIFTYCloud-mbaas/SwiftPushApp)
  * [Android](https://github.com/NIFTYCloud-mbaas/android_push_demo)
  * [Unity](https://github.com/NIFTYCloud-mbaas/UnityFirstApp)
  * [Monaca](https://github.com/NIFTYCloud-mbaas/MonacaPushApp)

  * プッシュ通知を準備する際、以下の手順を必ず実行してください。
  * 「アプリの新規作成」画面が出るのでアプリを作成し、２種類のAPIキー（アプリケーションキーとクライアントキー）を取得します。

  ![画像3](/readme-img/003.png)

  ![画像4](/readme-img/004.png)

  * プッシュ通知の設定し、プッシュ通知の許可およびiOS, Androidのプッシュ通知APIキーを設定します（プッシュ通知サンプルで実施済みの場合、再設定不要）

  ![画像5](/readme-img/005.png)


### 1. インストレーション確認

上記のサンプルを利用して、アプリをビルドします。
ビルドしたアプリを起動することで、端末情報をinstallationsクラスに登録されます。

![画像installation](/readme-img/installation.png)

登録した端末のObjectIdをこちらで確認できます。
ObjectIdを利用して、端末を絞って配信することが可能。

### 2. GitHubからサンプルをダウンロード

下記リンクをクリックしてプロジェクトをダウンロードします

  [RESTAPI_PushDemo_PHP](https://github.com/NIFTYCloud-mbaas/RESTAPI_PushDemo_PHP/archive/master.zip)

ファイルを解凍します。
ダウンロードしたプロジェクトには以下の二つファイルが入っていることを確認してください。
 * `send_push_all.php`　REST APIを使って全配信するサンプル
 * `send_push_condition.php`　REST APIを使って端末を絞り込み配信するサンプル


### 3. サンプルコードでキーを設定

`send_push_condition.php`　および `send_push_all.php` ファイルをエディターで編集します.
* 先程[ニフティクラウドmobile backend](http://mb.cloud.nifty.com/)のダッシュボード上で確認したAPIキーを貼り付けます

![画像07](/readme-img/007.png)

* それぞれ`APPLICATION_KEY`と`CLIENT_KEY`の部分を書き換えます
 * このとき、ダブルクォーテーション（`"`）を消さないように注意してください！
* 書き換え終わったら`command + s`キーで保存をします

### 4. 全配信を指定

`send_push_all.php`　ファイルを編集します。
以下のコードの`DELIVERY_TIME`, `MESSAGE`, `TITLE`を編集して、保存してください。

BEFORE

```php
sendPush ("DELIVERY_TIME", "MESSAGE", "TITLE", "INSTALLATION");
```

AFTER(例)

```php
sendPush ("now", "メッセージ", "タイトル");
```


### 5. 絞り込み配信を指定

`send_push_condition.php`　ファイルについて詳細コード解説にて説明します。
簡単に紹介しますと、`send_push_condition.php`　に `sendPush ($time, $message, $title, $installations)`　メソッドの定義および、そのメソッドを呼び出す実装となっています。

```
sendPush ("DELIVERY_TIME", "MESSAGE", "TITLE", "INSTALLATION");
```

各変数は以下のように指定可能

* `$time`: 配信時間を指定します。設定可能項目は`"now"`,もしくは`"2016-10-18T18:46:57.046Z"`のようなUTC時間形式の文字列を指定します。
* `$message`: 配信メッセージの文字列を指定します。
* `$title`: 配信タイトルの文字列を指定します。
* `$installations`: 配信端末のobjectIdを指定します。objectIdの文字列、もしくは、objectIdの配列を指定できます。

　例：

```
sendPush("2016-10-18T18:46:57.046Z", "Show mess", "show title", [ "df3fdDE2******" , "XDYYdDE2******" ]);
sendPush("now", "Show mess", "show title", "df3fdDE2******" );
sendPush("now", "Show mess", "show title", [ "df3fdDE2******" , "XDYYdDE2******" ]);
sendPush("2016-10-18T18:46:57.046Z", "Show mess", "show title", "df3fdDE2******");
```

上記ご参考して、`send_push_condition.php`　ファイルにある以下のコードの`DELIVERY_TIME`, `MESSAGE`, `TITLE`, `INSTALLATION`を編集して、保存ください。

BEFORE

```php
sendPush ("DELIVERY_TIME", "MESSAGE", "TITLE", "INSTALLATION");
```

AFTER(例)

```php
sendPush("now", "メッセージ", "タイトル", "objectId");
```


### 6. 実行＆動作確認

コマンドライン（ターミナル）を使って、解答したダイレクトリに移動します。
`php`コマンドを利用して、ファイルを実行してください。
以下のように実行します。

 * 全配信

```
php send_push_all.php
```

 * 絞り込み配信

```
php send_push_condition.php
```

正常に登録されて、登録したプッシュ通知IDが表示されることを確認します。

```
{"createDate":"2016-10-19T01:33:39.724Z","objectId":"UBug5A********"}
```

ニフティクラウドmobile backendのプッシュ通知画面にて登録したプッシュ通知を確認します。

![画像4](/readme-img/kakunin.png)

エラーの場合、

```
{"code":"E404005","error":"No such application."}
```

エラーが発生場合、こちらの[エラーコード](http://mb.cloud.nifty.com/doc/current/rest/common/error.html)を参考してください。


# 解説

## REST API実装について

* ニフティクラウド mobile backend は REST API を提供しているため、外部サーバからデータストアや会員管理などすべての機能をご利用いただけます。

* REST APIを利用するため、共通フォーマットを従って、リクエスト作成する必要があります。
共通フォーマットは[こちら](http://mb.cloud.nifty.com/doc/current/rest/common/format.html)をご確認ください。

* 共通フォーマットにて、セキュリティを守るための独自仕様としてリクエストヘッダーに毎回シグネチャー作成し、ヘッダーに付ける必要があります。シグネチャーの作成は[こちら](http://mb.cloud.nifty.com/doc/current/rest/common/signature.html)ご参照ください。

* `send_push_all.php`　`send_push_condition.php`ファイルにてシグネチャー実装は以下となっています。

```php
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

```

* `send_push_all.php`　`send_push_condition.php`ファイルにてREST APIでヘッダー情報を設定実装は以下となっています。

```php
//ヘッダー指定
$headers = array(
    'Content-Type: application/json',
    'X-NCMB-Application-Key: '.$application_key,
    'X-NCMB-Signature: '.$signature,
    'X-NCMB-Timestamp: '.$timestamp
);
```

## REST APIでプッシュ通知の登録について

* プッシュ通知を登録するためのREST APIを利用することで、サーバからプッシュ通知登録可能です。
* プッシュ通知登録APIは[こちら](http://mb.cloud.nifty.com/doc/current/rest/push/pushRegistration.html)をご参考ください。
* プッシュ通知登録REST APIで、`immediateDeliveryFlag`  か　`deliveryTime`, `message`, `title`を指定する必要があります。

```php
//時間指定
if ($time == "now" ) {
  $data = array(
    "immediateDeliveryFlag" => true,
    "message" => $message,
    "title" => $title
  );
} else {
  $data = array(
    "deliveryTime" => array(
                      "__type" => "Date",
                      "iso" => $time
                    ),
    "message" => $message,
    "title" => $title
  );
}
```

*　端末を絞り込みするために、`searchCondition`を指定する必要があります `send_push_condition.php`　ファイルにて配信時間判定は以下となっています。

```php
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
```

* `send_push_all.php` `send_push_condition.php`　ファイルにて登録リクエスト実装は以下となっています。

```php
$options = array('http' => array(
    'method' => 'POST',
    'content' => stripcslashes(json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)),
    'header' => implode("\r\n", $headers),
    'ignore_errors' => true
));
$push_contents = file_get_contents($url, false, stream_context_create($options));
print($push_contents);
```

# 参考

* [ニフティクラウドmobile backendのREST API](http://mb.cloud.nifty.com/doc/current/rest/common/format.html)
* [ニフティクラウドmobile backendのプッシュ通知登録](http://mb.cloud.nifty.com/doc/current/rest/push/pushRegistration.html)
