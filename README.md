# 【PHP】 サーバからプッシュ通知を送ってみよう！<br>[REST API]
*2016/10/18作成(2016/10/20更新)*

![画像1](/readme-img/zentai.png)

## 概要

 * [ニフティクラウドmobile backend](http://mb.cloud.nifty.com/)の『プッシュ通知』をサーバから登録するためのサンプルです
 * ニフティクラウドmobile backendが提供している[REST API](http://mb.cloud.nifty.com/doc/current/rest/common/format.html)を利用することで、サーバ環境からも簡単にプッシュ通知の実装が可能です
 * 簡単な操作ですぐに [ニフティクラウドmobile backend](http://mb.cloud.nifty.com/)の機能を体験いただけます★☆

## ニフティクラウドmobile backendって何？
 スマートフォンアプリのバックエンド機能（プッシュ通知・データストア・会員管理・ファイルストア・SNS連携・位置情報検索・スクリプト）が**開発不要**、しかも基本**無料**(注1)で使えるクラウドサービス！

 注1：詳しくは[こちら](http://mb.cloud.nifty.com/price.htm)をご覧ください

 ![画像2](/readme-img/002.png)

## プッシュ通知の仕組み

* ニフティクラウドmobile backendのプッシュ通知は、各プラットフォームが提供している通知サービスを利用しています
 * Androidの通知サービス __FCM（Firebase Cloud Messaging）__

 ![画像a1](/readme-img/a001.png)

 ※ FCMはGCM(Google Cloud Messaging)の新バージョンです。既にGCMにてプロジェクトの作成・GCMの有効化設定を終えている場合は、継続してご利用いただくことが可能です。新規でGCMをご利用いただくことはできませんので、あらかじめご了承ください。

 * iOSの通知サービス　__APNs（Apple Push Notification Service）__

 ![画像i1](/readme-img/i001.png)

* 上図のように、アプリ・サーバー（ニフティクラウドmobile backend）・通知サービス（FCMあるいはAPNs）の間で認証が必要になります

* 今回サーバからプッシュ通知を送信するサンプルを動作確認するために、AndroidかiOSで端末登録済みの状態必要があります。

## 環境環境
下記環境で動作確認しております
* PHP v5.4以降
 * httpsへ通信が必要のため、https通信ができるよう環境をご用意ください。
 * v5.6.x の場合、SSL証明書が検証されますので、正しい証明書をご利用ください。(参考：http://php.net/manual/ja/migration56.openssl.php)

### PHP環境準備について
PHP環境を用意する必要がありますので、以下のいずれかを参考していただき、動作環境をご用意ください。
* ローカル環境
  * __Macの場合__：既にPHPが入っております、そのまま、ターミナルで確認いただけます。
  * __Windowsの場合__：[こちら](http://php.net/manual/ja/install.windows.php)を参考していただき、インストールを行ってください。
* サーバ環境
 * デフォルトでPHPが入っていない場合、[こちら](http://php.net/manual/ja/install.php)を参考していただき、インストールを行ってください。

## 作業の手順
### 0. 受信するアプリの準備
サーバーからサンプルを実行し、プッシュ通知配信するには、配信される側のアプリをご用意いただく必要があります。下記に各プラットフォームごとのサンプルを用意しましたので、リンク先の手順にしたがって、ご準備をお願いします。

  * iOS:　[Objective-C](https://github.com/NIFTYCloud-mbaas/ObjcPushApp) / [Swift](https://github.com/NIFTYCloud-mbaas/SwiftPushApp)
  * [Android](https://github.com/NIFTYCloud-mbaas/android_push_demo)
  * [Unity](https://github.com/NIFTYCloud-mbaas/UnityFirstApp)
  * [Monaca](https://github.com/NIFTYCloud-mbaas/MonacaPushApp)

#### 確認事項
上記サンプルを利用時に[ニフティクラウドmobile backend](http://mb.cloud.nifty.com/)のコントロールパネル上で作成したアプリのAPIキー（アプリケーションキー・クライアントキー）をこの後使用します。

### 1. 登録された端末情報の確認
[0. 受信するアプリの準備](https://github.com/NIFTYCloud-mbaas/RESTAPI_PushDemo_PHP#0-受信するアプリの準備)でビルドしたアプリを起動することで、端末情報が登録されます。「データストア」>「installation」クラスで確認できます。

* 端末登録時、１端末毎に「objectId」が割り振られます。複数の端末が登録されている場合は、このobjectIdを使用し、特定の端末に絞った配信することが可能。

![画像installation](/readme-img/installation.png)

* objectIdは後ほど使用します

### 2. GitHubからサンプルをダウンロード
下記リンクをクリックしてプロジェクトをダウンロードし、ファイルを解凍します。

__[RESTAPI_PushDemo_PHP](https://github.com/NIFTYCloud-mbaas/RESTAPI_PushDemo_PHP/archive/master.zip)__

ダウンロードしたプロジェクトには以下の２つファイルが入っていることを確認してください。
 * `send_push_all.php`：REST APIを使って全配信するサンプル
 * `send_push_condition.php`：REST APIを使って端末をobjectIdで絞り込んで配信するサンプル

### 3. サンプルコードにAPIキーを設定
`send_push_all.php`および `send_push_condition.php` ファイルをエディターで編集し、[ニフティクラウドmobile backend](http://mb.cloud.nifty.com/)のダッシュボード上で確認したAPIキーを貼り付けます(参照：[確認事項](https://github.com/NIFTYCloud-mbaas/RESTAPI_PushDemo_PHP/tree/hotfix/README#%E7%A2%BA%E8%AA%8D%E4%BA%8B%E9%A0%85))

* 「`//APIキーの設定`」の部分を編集します

![画像07](/readme-img/007.png)

* それぞれ`APPLICATION_KEY`と`CLIENT_KEY`の部分を書き換えます
 * このとき、シングルクォーテーション（`'`）を消さないように注意してください！
* 書き換え終わったら保存をします

### 4. 動作確認（全配信）
`send_push_all.php` ファイルを実行します。

* このサンプルでは**登録された全ての端末**に、次の内容でプッシュ通知を配信します。
 * 配信日時：`今すぐ配信`
 * タイトル：`タイトル`
 * メッセージ：`メッセージ`

* コマンドライン（ターミナル）を使って、解凍したフォルダのダイレクトリに移動し、`php`コマンドを利用して、ファイルを実行します

```bash
php send_push_all.php
```

* 正常に登録されると、登録したプッシュ通知の「プッシュ通知ID」（プッシュ通知毎に付与されるID）が表示されます

例）

```bash
{"createDate":"2016-10-19T01:33:39.724Z","objectId":"UBug5A********"}
```

* [ニフティクラウドmobile backend](http://mb.cloud.nifty.com/)のダッシュボードから、登録したプッシュ通知を確認します(「プッシュ通知」>「一覧」)

![画像4](/readme-img/kakunin.png)

* 端末にもプッシュ通知が配信されていることが確認できます

![端末1](/readme-img/iPhone_1.png)

* 下記のようなエラーが発生場合、[エラーコード一覧](http://mb.cloud.nifty.com/doc/current/rest/common/error.html)をご参照ください

```bash
{"code":"E404005","error":"No such application."}
```

#### 編集して試してみましょう！
配信時間・タイトル・メッセージを編集してプッシュ通知を配信してみましょう。

* `send_push_all.php` ファイルは `sendPush ($time, $message, $title, $installations)` メソッドの定義および、そのメソッドを呼び出す内容が実装されています。メソッドの各変数は以下のように指定可能です。
 * `$time`: 配信時間を指定します。設定可能項目は`"now"`,もしくは`"2016-10-18T18:46:57.046Z"`のようなUTC時間形式の文字列を指定します。
 * `$message`: 配信メッセージの文字列を指定します。
 * `$title`: 配信タイトルの文字列を指定します。

* 上記メソッドの呼び出し部分を編集します。`send_push_all.php` ファイルで次コードの`now`, `メッセージ`, `タイトル`を例に習って編集します。

![php1](/readme-img/php_1.png)

例)

```php
sendPush ("now", "ニフティクラウド mobile backend でプッシュ通知プッシュ通知！", "PHPでプッシュ通知配信");
```

* 保存をして`php`コマンドで実行してみてください。指定した通りプッシュ通知が配信されることが確認できます。

![ダッシュボード1](/readme-img/dashboard_1.png)
![端末2](/readme-img/iPhone_2.png)

### 5. 動作確認（絞り込み配信）
`send_push_condition.php` ファイルを実行します。

* このサンプルでは登録された端末から特定の端末を絞り込んでプッシュ通知を配信できます。
* `send_push_all.php` ファイルと同様に、メソッドの定義および、そのメソッドを呼び出す内容が実装されていますが、次のようにメソッドに変数を１つ増やし、端末の指定を可能にしています。

 ```php
 sendPush ($time, $message, $title, $installations)
 ```

* 追加された `$installations` 変数には以下のように指定可能です
 * `$installations`: 配信端末のobjectIdを指定します。objectIdの文字列、もしくは、objectIdの配列を指定できます。

* 上記メソッドの呼び出し部分を編集します。 ファイルで次コードの`now`, `メッセージ`, `タイトル`, `objectId` を例に習って編集します。

 ![php2](/readme-img/php_2.png)

 例)登録した端末の「objectId」が「`lOIwqBa*********`」の場合（参照：[objectIdの確認方法](https://github.com/NIFTYCloud-mbaas/RESTAPI_PushDemo_PHP/tree/hotfix/README#1-%E7%99%BB%E9%8C%B2%E3%81%95%E3%82%8C%E3%81%9F%E7%AB%AF%E6%9C%AB%E6%83%85%E5%A0%B1%E3%81%AE%E7%A2%BA%E8%AA%8D)）

 ```php
 sendPush ("now", "ニフティクラウド mobile backend でプッシュ通知プッシュ通知！", "PHPでプッシュ通知配信", "lOIwqBa*********");
 sendPush ("now", "ニフティクラウド mobile backend でプッシュ通知プッシュ通知！", "PHPでプッシュ通知配信", [ "df3fdDE2******" , "XDYYdDE2******" ])
 ```

* コマンドライン（ターミナル）を使って、解凍したフォルダのダイレクトリに移動し、`php`コマンドを利用して、ファイルを実行します

```bash
php send_push_condition.php
```

* 正常に登録されると、登録したプッシュ通知の「プッシュ通知ID」（プッシュ通知毎に付与されるID）が表示されます

例）

```bash
{"createDate":"2016-10-19T01:33:39.724Z","objectId":"UBug5A********"}
```

* [ニフティクラウドmobile backend](http://mb.cloud.nifty.com/)のダッシュボードから、登録したプッシュ通知を確認します(「プッシュ通知」>「一覧」)
* `searchCondition`に指定した`objectId`が確認できます
![画像5](/readme-img/kakunin2.png)

* 端末にもプッシュ通知が配信されていることが確認できます

![端末2](/readme-img/iPhone_2.png)

* 下記のようなエラーが発生場合、[エラーコード一覧](http://mb.cloud.nifty.com/doc/current/rest/common/error.html)をご参照ください

```bash
{"code":"E404005","error":"No such application."}
```

## 解説

### REST API実装について

* ニフティクラウド mobile backend は REST API を提供しているため、外部サーバからデータストアや会員管理などすべての機能をご利用いただけます。

* REST APIを利用するため、共通フォーマットを従って、リクエスト作成する必要があります。
共通フォーマットは[こちら](http://mb.cloud.nifty.com/doc/current/rest/common/format.html)をご確認ください。

* 共通フォーマットにて、セキュリティを守るための独自仕様としてリクエストヘッダーに毎回シグネチャー作成し、ヘッダーに付ける必要があります。シグネチャーの作成は[こちら](http://mb.cloud.nifty.com/doc/current/rest/common/signature.html)をご参照ください。

* シグネチャーの実装は以下のようになっています。

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

* REST APIでヘッダー情報を設定実装は以下のようになっています。

```php
//ヘッダー指定
$headers = array(
    'Content-Type: application/json',
    'X-NCMB-Application-Key: '.$application_key,
    'X-NCMB-Signature: '.$signature,
    'X-NCMB-Timestamp: '.$timestamp
);
```

### REST APIでプッシュ通知の登録について
* プッシュ通知を登録するためのREST APIを利用することで、サーバからプッシュ通知登録可能です
 * プッシュ通知登録REST APIについて詳しくは[こちら](http://mb.cloud.nifty.com/doc/current/rest/push/pushRegistration.html)をご参照ください
* プッシュ通知登録REST APIで、`immediateDeliveryFlag` か `deliveryTime`, `message` ,`title`を指定する必要があります

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

* 端末を絞り込みするために、`searchCondition`を指定する必要があります (`send_push_condition.php`)

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

* 登録リクエスト実装は以下のようになっています

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

## 参考
* [ニフティクラウドmobile backendのREST API](http://mb.cloud.nifty.com/doc/current/rest/common/format.html)
* [ニフティクラウドmobile backendのプッシュ通知登録](http://mb.cloud.nifty.com/doc/current/rest/push/pushRegistration.html)
