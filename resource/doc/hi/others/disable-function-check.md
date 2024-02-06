# प्रतिबंधित फ़ंक्शन की जाँच अक्षम करें

इस स्क्रिप्ट का उपयोग करके जाँचें कि क्या कोई फ़ंक्शन प्रतिबंधित है। कमांड लाइन पर निम्न आदेश द्वारा चलाएं: ```curl -Ss https://www.workerman.net/webman/check | php```

यदि इसमें ```Functions फ़ंक्शन_नाम has be disabled. Please check disable_functions in php.ini``` का संकेत है, तो इसका अर्थ है कि webman की आवश्यकता वाला फ़ंक्शन प्रतिबंधित है, और वेबमैन को सही ढंग से उपयोग करने के लिए php.ini में प्रतिबंध को हटाना होगा।
निषेधों को हटाने के लिए निम्नलिखित विधियाँ में से कोई भी चुनें।

## विधि एक
`webman/console` को स्थापित करें 
```
composer require webman/console ^v1.2.35
```

आज्ञा की आवश्यकता है
```
php webman fix-disable-functions
```

## विधि दो

निषेधों को हटाने के लिए स्क्रिप्ट चलाएं `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php`

## विधि तीन

`php --ini`  चलाएं और php cli का प्रयोग करते समय php.ini फ़ाइल का स्थान ढूंढें

php.ini खोलें, `disable_functions` ढूंढें, और निम्नलिखित फ़ंक्शनों को असक्षम करें
```
stream_socket_server
stream_socket_client
pcntl_signal_dispatch
pcntl_signal
pcntl_alarm
pcntl_fork
posix_getuid
posix_getpwuid
posix_kill
posix_setsid
posix_getpid
posix_getpwnam
posix_getgrnam
posix_getgid
posix_setgid
posix_initgroups
posix_setuid
posix_isatty
proc_open
proc_get_status
proc_close
shell_exec
```
