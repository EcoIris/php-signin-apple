# 苹果账号登录
## 安装
```shell
composer require ecoiris/php-signin-apple
```
## 增加test.php示例文件
## Laravel中使用
```php
use AppleSign\ASDecoder;
use Illuminate\Http\Request;

public function appLogin(Request $request) 
{
    // 客户端集成苹果授权，授权后可以获取到以下信息
    $userId = $request->input('userId');
    $identityToken = $request->input('identityToken');
    $email = $request->input('email');
    $fullName = $request->input('fullName');
    
    $appleSignInPayload = ASDecoder::getAppleSignInPayload($identityToken);
    $isValid = $appleSignInPayload->verifyUser($userId);
    if ($isValid) {// 验证通过
        print_r('验证通过后的逻辑');
        return;
    }
    print_r('验证失败后的逻辑');
    return;
}
```

