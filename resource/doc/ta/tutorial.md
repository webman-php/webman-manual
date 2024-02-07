# எளிய மாதிரி

## சராசரி பதில்
**மேலதிக மேன்படுத்தி** 

புதிய கோண்ட்ரோல்லரை உருவாக்க படிகளைப் புதிய கோப்பில் `app/controller/UserController.php` எப்படியிருப்பதை முதன்முதலில் வழங்குகின்றோம்

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        // கேட்பதையும், அதன் இலக்கமைப்பையும் கொண்ட பெறுமானத்தைத் தெரியும்படி அல்லது பொருந்திய மாணவர்களுக்கு திரும்ப

        $name = $request->get('name', $default_name);
        // உலர்ந்த உலாவியினை மீண்டும் மீண்டும் திரும்பியவர்களுக்கு பின்கொடுக்க

        return response('hello ' . $name);
    }
}
```

**காண்க**

`http://127.0.0.1:8787/user/hello?name=tom` என்ற உலாவியில் உள்ளது.

முகவரிவில் `hello tom` என்ற அசைவை உங்கள் உலாவியில் அசைக்கும்.

## Json பதில்
கோப்பு `app/controller/UserController.php` மாற்றுக

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return json([
            'code' => 0, 
            'msg' => 'சரி', 
            'data' => $name
        ]);
    }
}
```


**காண்க**

`http://127.0.0.1:8787/user/hello?name=tom` என்ற உலாவியில் உள்ளது

உலளியப்படும் `{"code":0,"msg":"சரி","data":"tom""}`+-+-இன் பிரதிகையை உங்கள் உலாவியில் அழைக்கும்.

Json ஆட்களத்தை பதிவு செய்யும்படி தேர்வு செய்யும்படி கொண்டுள்ளார்கள் `Content-Type: application/json`.

## XML பதில்
அத்துடன் உதவி செயலாக்கை `XML ($ xml)` பயன்படுத்தி `text-xml` தலையுடன் ஒரு` xml` பதிலை மட்டுமே பதிலாக்குகிறது. ₹

இங்கே` $ xml``அளவிற்கு`ஒரு ஆதாரத்தை ஏற்படுத்தும் ஡ொம்பைக் குறிக்கலாம்.

## JsonP பதில்
அத்துடன் உதவி செயலாக்கை உபயோகிப்பதனால் `துண்டு (தகவல், அழிக்கு முன் = 'அழிப்போம் ')` அல்லது ஒரு` JsonP` பதிலைத் திருப்புகிறோம்.
## காட்டுயிர் மீட்டிமாகிய பதில்
`app/controller/UserController.php` கோப்பை பிரிக்கவும்

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $default_name = 'webman';
        $name = $request->get('name', $default_name);
        return view('user/hello', ['name' => $name]);
    }
}
```

`app/view/user/hello.html` என்பது புதிய கோப்பை உருவாக்குக

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

உலாவியில் `http://127.0.0.1:8787/user/hello?name=tom` பார்க்கவும்
 ஒரு உள்ளடக்கம் அருகில்`hello tom` என்ற ஒரு html பக்கம் திருப்பிக் கொடுக்கப்படும்.

குறிப்பு: வெப்மேன் இயல்புப் பயன்பாடுகளாக ஜாவா மூல மொழியை முதன்முமையாகப் பயன்படுத்துகின்றன. மற்ற காட்சிகளைப் பயன்படுத்த விரும்பும் எனவே காட்டுக்கள் [காட்சிகள்](view.md).
