# पेजिनेशन

# 1. Laravel के ORM पर आधारित पेजिनेशन तरीका
लारवेल का `illuminate/database` सुविधाएँ उपयुक्त पेजिनेशन का वितरण करता है।

## स्थापना
`composer require illuminate/pagination`

## उपयोग
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate($per_page);
    return view('index/index', ['users' => $users]);
}
```

## पेजिनेटर इंस्टेंस फ़ंक्शन
|  फ़ंक्शन   | विवरण  |
|  ----  |-----|
|$paginator->count()|वर्तमान पृष्ठ पर डेटा की कुल संख्या प्राप्त करें|
|$paginator->currentPage()|वर्तमान पृष्ठ संख्या प्राप्त करें|
|$paginator->firstItem()|परिणाम सेट में पहला आइटम क्रमांक प्राप्त करें|
|$paginator->getOptions()|पेजिनेटर विकल्प प्राप्त करें|
|$paginator->getUrlRange($start, $end)|निर्दिष्ट पृष्ठ संख्या सीमा के URL बनाएं|
|$paginator->hasPages()|क्या पर्याप्त डेटा है जिससे कई पृष्ठ बनाए जा सकें|
|$paginator->hasMorePages()|क्या और पृष्ठ देखने के लिए और हैं|
|$paginator->items()|वर्तमान पृष्ठ के डेटा आइटम प्राप्त करें|
|$paginator->lastItem()|परिणाम सेट में आखिरी आइटम क्रमांक प्राप्त करें|
|$paginator->lastPage()|अंतिम पृष्ठ की पृष्ठ संख्या प्राप्त करें (simplePaginate में उपयोग नहीं कर सकते)|
|$paginator->nextPageUrl()|अगले पृष्ठ का URL प्राप्त करें|
|$paginator->onFirstPage()|क्या वर्तमान पृष्ठ पहला पृष्ठ है|
|$paginator->perPage()|प्रत्येक पृष्ठ पर प्रदर्शित होने वाली संख्या को प्राप्त करें|
|$paginator->previousPageUrl()|पिछले पृष्ठ का URL प्राप्त करें|
|$paginator->total()|परिणाम सेट में कुल डेटा संख्या प्राप्त करें (simplePaginate में उपयोग नहीं कर सकते)|
|$paginator->url($page)|निर्दिष्ट पृष्ठ का URL प्राप्त करें|
|$paginator->getPageName()|पृष्ठ संख्या को संग्रहण के लिए प्रश्न प्राप्त करें|
|$paginator->setPageName($name)|पृष्ठ संख्या को संग्रहण के लिए प्रश्न नाम सेट करें|

> **ध्यान दें**
> `$paginator->links()` फ़ंक्शन का समर्थन नहीं है

## पेजिनेशन कंपोनेंट
webman में हम `$paginator->links()` फ़ंक्शन का उपयोग करके पेजिनेटर बटन रेंडर नहीं कर सकते हैं, लेकिन हम अन्य कंपोनेंट का उपयोग करके रेंडर कर सकते हैं, जैसे `jasongrimes/php-paginator`।

**स्थापना**
`composer require "jasongrimes/paginator:~1.0"`

**बैकएंड**
```php
<?php
namespace app\controller;

use JasonGrimes\Paginator;
use support\Request;
use support\Db;

class UserController
{
    public function get(Request $request)
    {
        $per_page = 10;
        $current_page = $request->input('page', 1);
        $users = Db::table('user')->paginate($per_page, '*', 'page', $current_page);
        $paginator = new Paginator($users->total(), $per_page, $current_page, '/user/get?page=(:num)');
        return view('user/get', ['users' => $users, 'paginator'  => $paginator]);
    }
}
```

**टेम्पलेट (PHP मूल भाषा)**
नया टेम्पलेट बनाएँ app/view/user/get.html
```html
<html>
<head>
  <!-- इंबेडेड Bootstrap पेजिनेटर स्टाइल समर्थन -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**टेम्पलेट (twig)** 
नया टेम्पलेट बनाएँ app/view/user/get.html
```html
<html>
<head>
  <!-- इंबेडेड Bootstrap पेजिनेटर स्टाइल समर्थन -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{% autoescape false %}
{{paginator}}
{% endautoescape %}

</body>
</html>
```

**टेम्पलेट (ब्लेड)** 
नया टेम्पलेट बनाएँ app/view/user/get.blade.php
```html
<html>
<head>
  <!-- इंबेडेड Bootstrap पेजिनेटर स्टाइल समर्थन -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**टेम्पलेट (थिंकपीएचपी)**
नया टेम्पलेट बनाएँ app/view/user/get.html
```html
<html>
<head>
    <!-- इंबेडेड Bootstrap पेजिनेटर स्टाइल समर्थन -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

परिणाम:
![](../../assets/img/paginator.png)

# 2. Thinkphp के ORM पर आधारित पेजिनेशन तरीका
कोई अतिरिक्त लाइब्रेरी की स्थापना की आवश्यकता नहीं है, सिर्फ think-orm की स्थापना कर दी जाए।
## उपयोग
```php
public function index(Request $request)
{
    $per_page = 10;
    $users = Db::table('user')->paginate(['list_rows' => $per_page, 'page' => $request->get('page', 1), 'path' => $request->path()]);
    return view('index/index', ['users' => $users]);
}
```

**टेम्पलेट (थिंकपीएचपी)**
```html
<html>
<head>
    <!-- इंबेडेड Bootstrap पेजिनेटर स्टाइल समर्थन -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
