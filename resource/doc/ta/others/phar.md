# phar பேக்

phar என்பது PHP இல் JAR போன்ற ஒரு பொது பெயரிட கோப்பு, நீங்கள் phar ஐ உபயோகிப்பாக webman உங்கள் திட்டத்தை ஒற்றை phar கோப்புக்காக பொருந்தவலாம், அழகாக வைக்க.

**இங்கு மிகவும் நன்றி [fuzqing](https://github.com/fuzqing) இன் பிஆரைக்கு.**

> **குறிக்கவும்**
> `php.ini` இல் phar கட்டுப்பாடு விரைவு தூண்டி, `phar.readonly = 0` ஐ அமைக்க வேண்டும்.

## கட்டமை செயலி நிறுவல்
`composer require webman/console`

## கட்டமை அமைப்பு
`config/plugin/webman/console/app.php` கோப்பைத் திறக்கவும், `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` ஐ அமைக்க, பயனர் கோப்புகள் மற்றும் கோப்புகளை விட்டுக்கொண்டு பொதுவாகவே பொருத்தப்படாத கோப்புகளை மறைத்து வைப்பதனையும் பொருத்தப்படவும் வெளிப்படுத்தும்.

## பேக் செய்து உருவாக்கு
webman திட்ட மூல ஆவணத்தில் குறித்து கொள்க `php webman phar:pack`
ஆற்கள் படில் ஒரு `webman.phar` கோப்பு உருவாக்கும்.

> பேக் குறிக்கின்ற கட்டமைகள் `config/plugin/webman/console/app.php` இல் உள்ளன.

## ஏற்று நிறுத்த செயல்கள்
**ஏற்று**
`php webman.phar start` அல்லது `php webman.phar start -d`

**நிறுத்து**
`php webman.phar stop`

**நிலையை அறிக்கையாக**
`php webman.phar status`

**இணைந்த நிலையை அறிக்கையாக**
`php webman.phar connections`

**மீள-ஆரம்பிக்க**
`php webman.phar restart` அல்லது `php webman.phar restart -d`

## விளக்கம்
* webman.phar உடன் இயங்கும் பிறகு runtime அடையாளப்படுத்தல்களை சேமிக்கும் அடல், உள்ளடக்கத்தைக் கூடுதலாக பெரும்பாலாகக் கொண்டிருக்கும்.

* உங்கள் திட்டத்தில் .env கோப்பு இருந்தால், .env கோப்பை webman.phar கோப்பின் இருப்பின் வைக்க வேண்டும்.

* உங்கள் வணிக உத்தரவக் கோப்புகளை public அடையாளப்படுத்த வேண்டும், public கோப்பினை அல்லதுஅடல் முகளல் புரட்சி ஆக்கிகளின் வணிகக் கோப்புகளை, இந்த போது `config/app.php` ஐ உள்ளதாக்க வேண்டும்.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
வணிகம் `public_path()` உதவி செயக் குடி செய்கையாக புரட்சி கோப்பு இருப்பைக் கண்டறி கொள்வதாகயும்.

* webman.phar உடன் வெண்டோஸில் உயர்ந் ஆய்வாக்கின்ற தனிப்படசிய நகவுக் கோப்பைக் கொண்டிருக்கவில்லை.
