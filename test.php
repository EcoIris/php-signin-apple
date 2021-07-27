<?php
require_once './vendor/autoload.php';

use AppleSign\ASDecoder;

// 客户端集成苹果授权，授权后可以获取到以下信息
$userId = $_POST['userId'];
$identityToken = $_POST['identityToken'];
$email = $_POST['email'];
$fullName = $_POST['fullName'];

$appleSignInPayload = ASDecoder::getAppleSignInPayload($identityToken);
$isValid = $appleSignInPayload->verifyUser($userId);
if ($isValid) {// 验证通过
    print_r('验证通过后的逻辑');
    return;
}
print_r('验证失败后的逻辑');
return;