ملف تكوين الطرق
يقع ملف تكوين الطرق للمكون في `plugin/اسم_المكون/config/route.php`

الطريقة الافتراضية
يبدأ مسار عناوين URL لتطبيق المكونات بـ `/app` ، على سبيل المثال `plugin\foo\app\controller\UserController` يكون عنوان الرابط هو `http://127.0.0.1:8787/app/foo/user`

تعطيل الطريق الافتراضي
إذا كنت ترغب في تعطيل الطريق الافتراضي لأحد تطبيقات المكونات ، يمكنك ذلك من خلال ضبط شيفرة مماثلة لهذه
```php
Route::disableDefaultRoute('foo');
```

معالجة مردود الخطأ 404
إذا كنت ترغب في تعيين fallback لأحد تطبيقات المكونات ، فإنه يجب عليك تمرير اسم المكون كمعامل ثانوي كما في
```
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```