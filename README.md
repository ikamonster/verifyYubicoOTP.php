# verifyYubicoOTP.php

YubiKey による Yubico OTP（ワンタイムパスワード）のシンプルなPHP用検証関数です。  

## 関数

```PHP
function verifyYubicoOTP(string $otp, string $apiClientId, string $apiSecretKey) : ?string
```

### 引数
+ string $otp … YubiKey にタッチすると出力されるワンタイムパスワード
+ string $apiClientId … YubiCloud API に利用登録すると得られるクライアントID
+ string $apiSecretKey … YubiCloud API に利用登録すると得られるシークレットキー

### 返値

+ string … 検証に成功した Yubico OTP Public ID
+ null … 検証失敗

## 使用例

```PHP
<?php
require_once('verifyYubicoOTP.php');

$otp = $_REQUEST['otp'];
$id = verifyYubicoOTP($otp, '12345', 'FooBarBaz=');
echo ($id)? 'Public ID: ' . $id : 'failed';
```

## 説明

Yubico OTP・YubiCloud の説明や利用法、そもそも YubiKey とは何かについては割愛します。この関数を欲する方はご存じでしょう。

同様のPHP用ライブラリーとして、Yubico公式開発サイトですでに「[Auth_Yubico PHP class](https://developers.yubico.com/php-yubico/)」が紹介されています。しかし導入や使い方が面倒そうだったため、仕組みの勉強を兼ねて自作してみました。

この関数は通信リトライしなかったり詳細なエラーコードを返さなかったりといった短所はあるものの（必要に応じて改良してください）、気軽に使いやすいと思います。特別な依存関係のない単独の関数なので、わざわざファイルをインクルードせず、自分のコードにコピペしてもよいでしょう。

具体的な処理の内容としては、複数ある YubiCloud サーバーのうちどれかひとつにワンタイムパスワードを問い合わせ、応答のHMAC-SHA-1署名とナンスだけを確認しています。これを第三者が欺くのは難しく、検証として十分と考えますが、仕様上はもっと厳密な検証をする余地もあります。気になる方は改良するか、より信頼性の高そうな別の検証ライブラリーをお探しください。

curlモジュール入り PHP8.1 で動作確認済み。  
ライセンスは MIT です。自己責任においてご自由にお使いください。
