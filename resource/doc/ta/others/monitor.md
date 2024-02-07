# கணினியை கண்காணிக்கும்
webman-டை தல் ஒரு கணிப்பு கண்காணிக்கும் துணைக்குறிகளுடன் வருந்திக் கொண்டுள்ளது, அது இரண்டு அம்சங்களை ஆதரிக்கின்றது
1. கோப்பு மேம்படுத்தல் மற்றும் தானாக புதிய தொகுப்பு குறியீட்டை reload பண்ணுதல் (முதல் நேரம் படியைப் பயன்படுத்தும்)
2. அனைத்து முனைய நினைவுகளையும் கண்காணிக்கி, ஒவ்வெ ாசம் ஒன்று php.ini இல் எணியாக்கப்பட்டது அளவுக்கு memory_limit வரை சேரும்போது தானாக பாதுகாக்குவது (வணிகம் மாற்றப்படுத்த விலக்கல்)

## கண்காணிப்பு உள்ளகரவு
உள்ளகரம் கோரிக்கை கோப்பு 'config/process.php' உள்ள`monitor` உள்ளது கோரிக்கை
```php

global $argv;

return [
    // File update detection and automatic reload
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitor these directories
            'monitorDir' => array_merge([    // எப்படி கொலைநிரல் கோப்புகளை கண்காணிக்க வேண்டும் என்று
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Files with these suffixes will be monitored
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // கோப்பு கண்காணிப்பை இயக்க வேண்டும் அல்லது வெற்றிகரமாக இயக்க (லினக்ஸ் கணினியில் - என்னும் முறை இனத்தில் இயக்க இன்னும் இயக்க பரிமாணம்),
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // நினைவப் பரவர்க்கத்தை இயக்கு (விண்டோஸ் கணினியில் நினைவப் பரவர்க்க ஆதரவு இல்லை)
            ]
        ]
    ]
];
```
`monitorDir` எதிர்காலத் தொகுப்புகளை எதிர்கொள்ளுவதாக அமைக்கும் (வலைப்பக்கங்களில் கோப்புகளை கண்காணித்தல் குறித்திருக்கக்கூடாது).
`monitorExtensions` `monitorDir` கொலைக்குறியை எப்படி கட்டனாய் கண்காணிக்க வேண்டும் என்பதை அமைக்கும்.
`options.enable_file_monitor` மதிப்பு 'உணராமை' அளவுக்கு சேர்க்கப்படும் உள்சாரத்தை இயக்குகின்றது (லினக்ஸ் முறையில் இயக்க இயலுமை -d இல்லாதபான் அல்லது DIRECTORY_SEPARATOR === '/').
`options.enable_memory_monitor` மதிப்பு 'உணராமை' அளவுக்கு சேர்க்கப்படும் நினைவப் பரவர்க்கம் (விண்டோஸ் மொழியில் நினைவப் பரவர்க்கம் ஆதரவு இல்லை).

> **குறிக்கோள்**
> விண்டோஸ் அமைப்பில் 'விண்டோஸ்.பயந்த' அல்லது 'பிஎச்பி விண்டோஸ்.பயந்த' ஆகும் பேடவுக்கான கட்டளைகளை இயக்குவதானப் படுத்தல் கோரிக்கை.
