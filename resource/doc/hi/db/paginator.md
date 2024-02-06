# पेजिनेशन

# 1. लारावेल की ORM पर आधारित पेजिनेशन विधि
लारावेल का `illuminate/database` पेजिनेशन की सुविधा प्रदान करता है।

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

## पेजिनेटर इंस्टेंस विधियाँ
|  विधि   | विवरण  |
|  ----  |-----|
|$paginator->count()|वर्तमान पेज पर डेटा की कुल संख्या प्राप्त करें|
|$paginator->currentPage()|वर्तमान पेज कोड प्राप्त करें|
|$paginator->firstItem()|परिणाम सेट में पहले आइटम का संख्यात्मक मान प्राप्त करें|
|$paginator->getOptions()|पेजिनेटर विकल्प प्राप्त करें|
|$paginator->getUrlRange($start, $end)|निर्दिष्ट पेज रेंज के URL बनाएं|
|$paginator->hasPages()|क्या पाने के लिए पर्याप्त संख्या में डेटा है|
|$paginator->hasMorePages()|क्या और पेज दिखाने के लिए कोई अधिक पेज है|
|$paginator->items()|वर्तमान पेज के डेटा आइटम प्राप्त करें|
|$paginator->lastItem()|परिणाम सेट में अंतिम आइटम का संख्यात्मक मान प्राप्त करें|
|$paginator->lastPage()|अंतिम पृष्ठ कोड प्राप्त करें (simplePaginate में उपलब्ध नहीं है)|
|$paginator->nextPageUrl()|अगले पृष्ठ का URL प्राप्त करें|
|$paginator->onFirstPage()|क्या वर्तमान पेज पहले पृष्ठ है|
|$paginator->perPage()|प्रति पृष्ठ प्रदर्शित संख्या की कुल संख्या प्राप्त करें|
|$paginator->previousPageUrl()|पिछले पृष्ठ का URL प्राप्त करें|
|$paginator->total()|परिणाम सेट में डेटा की कुल संख्या प्राप्त करें (simplePaginate में उपलब्ध नहीं है)|
|$paginator->url($page)|निर्दिष्ट पृष्ठ का URL प्राप्त करें|
|$paginator->getPageName()|पेज कोड को संग्रहीत करने के लिए पूछताछ पैरामीटर नाम प्राप्त करें|
|$paginator->setPageName($name)|पेज कोड को संग्रहीत करने के लिए पूछताछ पैरामीटर नाम सेट करें|

> **नोट**
> `$paginator->links()` विधि का समर्थन नहीं है।

## पेजिनेशन कॉम्पोनेंट
webman में `$paginator->links()` विधि का उपयोग पेजिनेशन बटनों को रेंडर करने के लिए नहीं किया जा सकता है, लेकिन हम अन्य कंपोनेंट का उपयोग करके रेंडर कर सकते हैं, जैसे `jasongrimes/php-paginator`।

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

**टेम्पलेट (PHP मूल)**
नया टेम्पलेट बनाएं app/view/user/get.html
```html
<html>
<head>
  <!-- इंबेडेड Bootstrap पेजिनेशन स्टाइल समर्थन -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?= $paginator;?>

</body>
</html>
```

**टेम्पलेट (त्विग)**
नया टेम्पलेट बनाएं app/view/user/get.html
```html
<html>
<head>
  <!-- इंबेडेड Bootstrap पेजिनेशन स्टाइल समर्थन -->
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
नया टेम्पलेट बनाएं app/view/user/get.blade.php
```html
<html>
<head>
  <!-- इंबेडेड Bootstrap पेजिनेशन स्टाइल समर्थन -->
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{!! $paginator !!}

</body>
</html>
```

**टेम्पलेट (थिंकपीएचपी)**
नया टेम्पलेट बनाएं app/view/user/get.html
```html
<html>
<head>
    <!-- इंबेडेड Bootstrap पेजिनेशन स्टाइल समर्थन -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

<?=$paginator?>

</body>
</html>
```

परिणाम निम्नलिखित रहेगा:
![](../../assets/img/paginator.png)

# 2. थिंकपीएचपी की ORM पर आधारित पेजिनेशन विधि
अतिरिक्त कोई वर्णनात्मक विधि की आवश्यकता नहीं है, सिर्फ think-orm को स्थापित करने के बाद आपको इसका उपयोग कर सकते हैं।

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
    <!-- इंबेडेड Bootstrap पेजिनेशन स्टाइल समर्थन -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
</head>
<body>

{$users|raw}

</body>
</html>
```
