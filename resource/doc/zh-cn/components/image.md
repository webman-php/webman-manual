# 图像处理组件

## intervention/image

### 项目地址

https://github.com/Intervention/image
  
### 安装
 
```php
composer require intervention/image
```
  
### 使用

**上传页面片段**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="提交">
  </form>
```

**新建 `app/controller/UserController.php`**

```php
<?php
namespace app\controller;
use support\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UserController
{
    public function img(Request $request)
    {
        $file = $request->file('file');
        if ($file && $file->isValid()) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file)->scale(100, 100);
            return response($image->encode(), 200, ['Content-Type' => 'image/png']);
        }
        return response('file not found');
    }
    
}
```

> **注意**
> 以上是v3版本用法

### 更多内容

访问 https://image.intervention.io/v3
  

