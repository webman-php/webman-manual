# 異常處理

## 配置
`config/exception.php`
```php
return [
    // 在這裡配置異常處理類
    '' => support\exception\Handler::class,
];
```
在多應用模式下，您可以為每個應用單獨配置異常處理類，請參閱[多應用](multiapp.md)


## 預設異常處理類
在webman中，異常默認由 `support\exception\Handler` 類來處理。 您可以修改配置文件`config/exception.php`來更改默認異常處理類。異常處理類必須實現`Webman\Exception\ExceptionHandlerInterface` 接口。
```php
interface ExceptionHandlerInterface
{
    /**
     * 記錄日誌
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * 渲染返回
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e) : Response;
}
```



## 渲染響應
異常處理類中的`render`方法用於渲染響應。

如果在配置文件`config/app.php`中`debug`值設為`true`（以下簡稱`app.debug=true`），將返回詳細的異常信息，否則將返回簡略的異常信息。

如果請求期待返回json數據，則異常信息將以json格式返回，如下所示：
```json
{
    "code": "500",
    "msg": "異常信息"
}
```
如果 `app.debug=true`，json數據中將附加一個 `trace` 字段以返回詳細的調用堆棧。

您可以編寫自己的異常處理類來更改默認的異常處理邏輯。

# 業務異常 BusinessException
有時我們希望在某個嵌套函數中終止請求並向客戶端返回錯誤信息，這時可以通過拋出`BusinessException`來實現這一點。
例如：

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('參數錯誤', 3000);
        }
    }
}
```

上述示例將返回一個
```json
{"code": 3000, "msg": "參數錯誤"}
```

> **注意**
> 業務異常 BusinessException 不需要在業務中使用try catch捕獲，框架將自動捕獲並根據請求類型返回適合的輸出。

## 自定義業務異常

如果上述響應不符合您的需求，例如想將 `msg` 改為 `message`，您可以自定義一個 `MyBusinessException`

創建 `app/exception/MyBusinessException.php` 文件，其內容如下：
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // json請求返回json數據
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // 非json請求則返回一個頁面
        return new Response(200, [], $this->getMessage());
    }
}
```

這樣，當業務調用
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('參數錯誤', 3000);
```
json請求將收到一個類似如下的json返回
```json
{"code": 3000, "message": "參數錯誤"}
```

> **提示**
> 因為 BusinessException 異常屬於可預知的業務異常（例如用戶輸入參數錯誤），所以框架不會認為它是致命錯誤，並不會記錄日誌。

## 總結
在任何需要中斷當前請求並向客戶端返回信息的時候，可以考慮使用 `BusinessException` 異常。