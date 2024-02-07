# 圖像處理元件

## intervention/image

### 專案地址

https://github.com/Intervention/image
  
### 安裝
 
```php
composer require intervention/image
```
  
### 使用

**上傳頁面片段**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="提交">
  </form>
```

**新增 `app/controller/UserController.php`**

```php
<?php
namespace app\controller;
use support\Request;
use Intervention\Image\ImageManagerStatic as Image;

class UserController
{
    public function img(Request $request)
    {
        $file = $request->file('file');
        if ($file && $file->isValid()) {
            $image = Image::make($file)->resize(100, 100);
            return response($image->encode('png'), 200, ['Content-Type' => 'image/png']);
        }
        return response('檔案未找到');
    }
    
}
```
  
### 更多內容

訪問 http://image.intervention.io/getting_started/introduction
