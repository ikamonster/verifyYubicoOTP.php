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

### [Yubico OTP](https://developers.yubico.com/OTP/) とは
ユビコ社の物理セキュリティーキー製品「[YubiKey](https://www.yubico.com/products/)」シリーズに備わっている、独自メカニズムによるワンタイムパスワード生成機能です。初期状態ではキーを短くタッチすることで出力されます。あらかじめ利用登録しておくことで、[YubiCloud](https://www.yubico.com/products/yubicloud/) と呼ばれるユビコ社のウェブサービスで検証が可能です（自前で検証サーバーを用意することも可能）。  

セキュリティーキーによるワンタイムパスワードは、認証の３要素「知識・所持・生体」のうち所持だけを証明する１要素認証です。これ単独では心もとないため、一般的には従来のID・パスワード（知識）認証に加える形で利用します。SMS認証やオーセンティケーターアプリと同様の位置づけですね。

ただし、１要素認証で十分と割り切ればこれ単独で認証を完了しても構いません。特に、Yubico OTP には Public ID と呼ばれる一意の固定文字列が含まれているのが特徴で、これをユーザーに紐付けておけば、ID入力すら不要で認証＆ユーザー識別が可能です。キーの紛失・盗難に弱いのが欠点ですが、使える局面さえあれば便利ではあります。

### verifyYubicoOTP 関数
Yubico OTP のPHP用検証ライブラリーとして、ユビコ社公式開発サイトですでに「[Auth_Yubico PHP class](https://developers.yubico.com/php-yubico/)」が紹介されています。しかし導入が面倒そうだったため、仕組みの勉強を兼ねて検証関数を自作してみました。それがこの verifyYubicoOTP 関数です。

この関数は通信リトライしなかったり詳細なエラーコードを返さなかったりといった短所はあるものの（必要に応じて改良してください）、気軽に使いやすいと思います。特別な依存関係のない単独の関数なので、わざわざファイルをインクルードせず、コードをコピペして使うのもよいでしょう。

具体的な処理ですが、YubiCloud API にワンタイムパスワードを問い合わせ、応答のHMAC-SHA-1署名とナンスだけを確認しています。これを第三者が欺くのは難しく、検証として十分と考えますが、仕様上はもっと厳密な検証をする余地もあります。気になる方は改良するか、より信頼性の高そうな別の検証ライブラリーをお探しください。ただ、YubiKey は WebAuthn で運用されるのが常であり、マイナーな Yubico OTP のためのライブラリーは少ないようです（正直いって自分自身も WebAuthn しか使っておらず、ユビコ社もOTPに積極的には見えない）。

curlモジュール入り PHP8.1 で動作確認済み。  
ライセンスは MIT です。自己責任においてご自由にお使いください。
