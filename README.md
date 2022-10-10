# verifyYubicoOTP.php

YubiKey による Yubico OTP（ワンタイムパスワード）のPHP用検証関数です。  
curlモジュール入り PHP8.1 で動作確認済み。

## 関数

verifyYubicoOTP($otp, $apiClientId, $apiSecretKey)

### 引数
+ $otp … YubiKey にタッチすると出力されるワンタイムパスワード
+ $apiClientId … YubiCloud API に利用登録すると得られるクライアントID
+ $apiSecretKey … YubiCloud API に利用登録すると得られるシークレットキー

### 返値

+ String … 検証に成功した Yubico OTP Public ID
+ false … 検証失敗

## 使用例

```PHP
<?php
define('YUBICLOUD_API_CLIENT_ID', '12345');
define('YUBICLOUD_API_SECRET_KEY', 'FooBarBaz=');
require_once('verifyYubicoOTP.php');

$otp = $_POST['otp'];
$id = verifyYubicoOTP($otp, YUBICLOUD_API_CLIENT_ID, YUBICLOUD_API_SECRET_KEY);
if ($id !== false) {
    echo 'success: Public ID = ' . $id;
} else {
    echo 'failed';
}
```

## 説明

Yubico OTP や YubiCloud の説明や利用法、そもそも YubiKey とは何かについては割愛します。この関数を欲する方には自明でしょう。

同様のPHP用ライブラリーとして、Yubico公式開発サイトですでに「[Auth_Yubico PHP class](https://developers.yubico.com/php-yubico/)」が紹介されています。が、非推奨だったり導入が面倒そうだったりしたため自作してみました。

こちらの関数は通信リトライしなかったり具体的なエラーコードを返さなかったりと短所はあるものの（必要に応じて改良してください）、気軽に使えると思います。わざわざファイルをインクルードせず、自分のコードにちょいとコピペしてもよいでしょう。

具体的な処理の内容としては、複数ある YubiCloud サーバーのうちどれか１つに問い合わせ、HMAC-SHA-1署名の検証、ナンスの一致、statusが"OK"か否か、だけを確認しています。最低限十分な処理ですが、正直にいえば最高の処理ではありません。気になる方はより信頼性の高い別の検証ライブラリーを探してお使いください。

ライセンスは MIT です。自己責任においてご自由にお使いください。
