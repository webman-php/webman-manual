# 이미지 처리 구성 요소

## intervention/image

### 프로젝트 주소

https://github.com/Intervention/image

### 설치

```php
composer require intervention/image
```

### 사용

**업로드 페이지 조각**

```html
  <form method="post" action="/user/img" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="제출">
  </form>
```

**`app/controller/UserController.php`에서 새로 만들기**

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
        return response('파일을 찾을 수 없음');
    }
}
```

### 더 많은 내용

http://image.intervention.io/getting_started/introduction을 방문하세요.
