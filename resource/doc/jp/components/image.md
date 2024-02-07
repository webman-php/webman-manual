# 画像処理コンポーネント

## intervention/image

### プロジェクトのリンク

https://github.com/Intervention/image
  
### インストール
 
```php
composer require intervention/image
```
  
### 使用方法

**アップロードページの断片**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="提出">
  </form>
```

**`app/controller/UserController.php` を新規作成**

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
        return response('ファイルが見つかりません');
    }
    
}
```
  
### 追加情報

http://image.intervention.io/getting_started/introduction をご覧ください
