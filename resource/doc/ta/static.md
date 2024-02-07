## நிலையான கோப்புகளை செயலாக்குவது
webman நிலையான கோப்பு அணுவை ஆதரிக்கிறது, நிலையான கோப்புகள் அனைத்தும் `public` அடைவில் உள்ளன, உதாரணம், `http://127.0.0.8787/upload/avatar.png` ஐ அணுவைப் பார்க்கும் போது உண்மையில் `{மொத்த திட்ட உறைவு}/public/upload/avatar.png` ஐ அணுவைப் பார்க்கிறது.

> **குறிப்பு**
> webman 1.4 இல் பயன்பாட்டு சேர்க்கைகளை ஆதரிக்கிறது, `/app/கோப்புப் பெயர்/கோப்பு பெயர்` வரையதாக்குவது உண்மையில் பயன்படுத்தும் வரையதாக்குவது, யானை webman >=1.4.0 இல் `{மொத்த திட்ட உறைவு}/public/app/` கீழ் அணுவைப் பார்வையிடம் ஆதரிக்கப்பட்டில்லை.
> மேலும் கூட விவரங்களைப் பாருங்கள்[பயன்பாடு சேர்க்கை](./plugin/app.md)

### நிலையான கோப்பு ஆதரவை மூடு
நிலையான கோப்பு ஆதரவை தொற்றுநீக்க, `config/static.php` ஐ திற.
பின்னர் அணுவைப் பார்வையிடும் அனைத்து நிலையான கோப்புகளும் 404 ஐ திருப்பும்.

### நிலையான கோப்பு அடைவை மாற்று
webman இயல்புநிலையில் நிலையான கோப்புகளையும் பூஜ்ஜிகக் கூட்டுக்களை பயன்படுத்துகிறது. பயன்படுத்த விரும்பினால் `support/helpers.php` இல் `public_path()` உதவி செயலி ஐ மாற்றவும்.

### நிலையான கோப்பு நடுமாரி
webman உலகு ஒரு நிலையான கோப்பு நடுமாரி ஐக் கொண்டுள்ளது, நிலையான கோப்பு`அவன்/static/கோப்பு பெயர்` என்று உள்ள பயன்பாடுகளைப் பயன்படுத்தி, `app/middleware/StaticFile.php` இடத்தில் உள்ளது.
சில நேரத்திற்கு நாம் நிலையான கோப்புகளுக்கு சில செயல்படுத்த, உதானம் நிலையான கோப்புகளுக்கு வழக்காவல்httpதொகுக்க, தடுமாற்கள் அளவீச்சட்டம் சேர்த்திருக்க, இந்த நடுமாரிக்கு பயன்படுத்த வேண்டியது

`app/middleware/StaticFile.php` உள்ள உள்ளடக்கம் பிணைய:
```php
<?php
namespace support\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class StaticFile implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : Response
    {
        // மறையாக. தொடர்களாய் வரைவு
        if (strpos($request->path(), '/.') !== false) {
            return response('<h1>403 தடுமாற்பட்டது</h1>', 403);
        }
        /** @var Response $response */
        $response = $next($request);
        // சில வீச்சு http தலைப்புகளைச் சேர்க்க
        /*$response->withHeaders([
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);*/
        return $response;
    }
}
```
இந்த தமிழ் நாட்டில் இது தேவையெனில், `config/static.php` உள்ள `middleware` உரையில் இணயும்.
