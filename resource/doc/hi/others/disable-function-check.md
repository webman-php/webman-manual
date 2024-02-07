# फ़ंक्शन परिवर्तन निषेधित करें

इस स्क्रिप्ट का उपयोग करके देखें कि क्या कोई निषेधित फ़ंक्शन है। कमांड लाइन पर चलाएं `curl -Ss https://www.workerman.net/webman/check | php`

यदि किसी प्रकार से `Functions फ़ंक्शन_नाम has been disabled. Please check disable_functions in php.ini` दिखता है तो इसका अर्थ है कि webman जो फ़ंक्शन का उपयोग करता है वह निषेधित है, इसे सही ढंग से उपयोग करने के लिए php.ini में निषेधित को खोलना होगा।
निषेधित को खोलने के लिए निम्नलिखित विधियां चुनें।

## विधि एक
`webman/console` इंस्टाल करें
```bash
composer require webman/console ^v1.2.35
```

निष्पादन कमांड
```bash
php webman fix-disable-functions
```

## विधि दो
स्क्रिप्ट निषेध करने के लिए `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` का निष्पादन करें

## विधि तीन
चलाएं `php --ini` और जांचें कि php cli का उपयोग करने वाले php.ini फ़ाइल का स्थान

php.ini खोलें, `disable_functions` खोजें और निम्नलिखित फ़ंक्शनों का उपयोग को खोलें
```ini
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
