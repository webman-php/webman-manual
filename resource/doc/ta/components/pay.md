# பணம் SDK

### திட்ட இடம்

 https://github.com/yansongda/pay

### நிறைவேற்ற

```php
composer require yansongda/pay -vvv
```

### பயன்பாடு

**அலிபாய்**

```php
<? php
பொருளடக்கம் App \ வலை \ கண்ட்ரோல்லர்கள்;

use Yansongda \ பாய் \ பாய்;
பயன்பாட்டில் வரிசை;

பணிமுகவர் PayController
{
protected $ config = [
'app_id' => '2016082000295641',
'notify_url' => 'http://yansongda.cn/notify.php',
'return_url' => 'http://yansongda.cn/return.php',
'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuWJKrQ6SWvS6niI+4vEVZiYfjkCfLQfoFI2nCp9ZLDS42QtiL4Ccyx8scgc3nhVwmVRte8f57TFvGhvJD0upT4O5O/lRxmTjechXAorirVdAODpOu0mFfQV9y/T9o9hHnU+VmO5spoVb3umqpq6D/Pt8p25Yk852/w01VTIczrXC4QlrbOEe3sr1E9auoC7rgYjjCO6lZUIDjX/oBmNXZxhRDrYx4Yf5X7y8FRBFvygIE2FgxV4Yw+SL3QAa2m5MLcbusJpxOml9YVQfP8iSurx41PvvXUMo49JG3BDVernaCYXQCoUJv9fJwbnfZd7J5YByC+5KM4sblJTq7bXZWQIDAQAB',
// மறைப்பகுதி: ** ரசா2 **ஐ பயன்படுத்துக்கொள்ளுங்கள்
'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
// பொதுவாக நகலியான வகை: ** ஆர்எஸ்ஏ 2 ஸ்டைல் குறியீடு **
'app_cert_public_key' => './cert/appCertPublicKey.crt', // முன்னோட்ட பாரமரிப்பு
'அலிபாய்_ரூட்_சானட்' => './cert/alipayRootCert.crt', // அலிபாய் ரூட் சானட் பாரமரிப்பு
'பதில்' => [// விதிகளால்
'file' => './logs/alipay.log',
'நிலை' => 'info', // ஆல்சிட்டமாக வருமானப் பருவம் அமைக்க பட்டியலை மாற்றம் செய்யப்படுகிறது, மேலதிக வடிவத்தில் debug அமைப்பு
'வகை' => 'தனிநபர்', // விதி மாற்ற பைத்தியமுலகத்தில் மட்டும் daily.
'அதிக_கோப்பு' => 30, // பைத்தியமுலகத்தில் daily என்பதால் முதலில் 30 நாட்கள் சரியாகும்
],
'http' => [// தேவைப்படும்
'தளவரப்பூர்வம்' => 5.0,
'இணைத்தல் தளம்' => 5.0,
// மேலும் அமைக்குகின்ற பல உள்ளடக்கங்களுக்கு குழுநியமன மற்றும் அல்லது காரணங்களைப் பார்வையிடவும் [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
],
'mode' => 'டெவ்', // விருப்பமாக, இதை அமைக்க ஛ங்கா விருப்பத்தில் அலசாபிக்கின்றன. 
];

public function index ()
{
ஆராய்ந்தது = [
'வெளியோட்டு_trade_no' => நேரம் (),
'மொத்த_அளவு' => '1',
'முடிவு' => 'சோதனைக்கு - சோதனை',
];

$ alipay = கடைசிப்படி: "அலிபாய் ($this-> config) -> web ($ஆராய்ந்தது);

திரும்ப அலிபாய்-> அனுப்பு ();
}
public function return ()
{
$ தரவு = கடைசிப்படி: "அலிபாய் ($this-> config) -> சரிபார் ();
// ஆர்டர் பெயர்: $ data-> out_trade_no
// அலிபாப்பாய் முதல் பரிவர்த்தனை எண்: $ data-> trade_no
// ஒரு சரக்கு மொத்த மொத்தம்: $ data-> மொத்தம் _அளவு
}

public function notify ()
{
$ அலிபாய் = கடைசிப்படி: "அலிபாய் ($this-> config);

முயற்சி {
$ தரவு = அலிபாய்-> சரிபார் (); // ஆம், இது ஆபத்தியாக இருக்கிறது!

// 1. தயவுசெய்து out_trade_no தனிக்கான ஆர்டரை இந்த அறிவிப்பு தரவுகளில் சரிபார்க்கவும்;
// 2. தெரிந்துகொள்ளும் மொத்த_அளவு இந்த ஆர்டரின் உண்மையான மதிப்புகொள்ளுதல் (முதன்மொத்தம் ஆர்டர் உருப்படியானபோது);
// 3. அறிக்கைப்படுத்தும் விலையில் பெற வேண்டியிருக்கும் seller_id ஐ (அல்) அடையாளிப்பது சரிபார்க்கப்பட்டதுக்காக (செயலாளர் எண் அத்துடன் உள்ளது) ஆர்டரில்;
// 4. பயன்பாடு_ஐ இந்த மெர்சன்ட் தன்னைத் தானியங்கி வேண்டும் என்பதை சரிபார்க்கவும்.
// 5. மற்ற வணிக வாழ்வினிப்பு நிபுணமான நிலைப்படுத்துகிறது

ைைிழக்க பாடு :: பயன்படுத்துகின்றது ('அலிபாய் அறிவிப்பு', $ data-> அனைத்து ());
} பிழையை பிடித்தவர் ($ முறை) {
// $ முறை-> சொல் ();
}

ைைை அலிபாய் - > சகோதரிக் திரும்பி ();
}
}

ைைை நான் சேவையாளர் என ஆவணி ஆய்வு செய்கின்றது (); இந்தத் தோற்றத்தை நீதி நீங்கள் தோன்றினால் திணிக்குகிறேன்்கள்! மேலும் உள்ளடக்கம் [அணுகவும்] (https://pay.yanda.net.cn/docs/2.x/overview) பார்க்க.
