```php
# ফাংশন নিষেধ চেক

এই স্ক্রিপ্টটি ব্যবহার করে চেক করুন যে কোনও নিষিদ্ধ ফাংশন আছে কি না। কমান্ড লাইনে রান করুন```curl -Ss https://www.workerman.net/webman/check | php```

যদি কোনও ম্যাসেজ প্রদর্শিত হয়```Functions ফাংশনের_নাম has be disabled. Please check disable_functions in php.ini```, তাহলে বোঝায় যে webman অপরিবর্তিতভাবে ব্যবহার করতে নিষিদ্ধ ফাংশন নিয়ন্ত্রিত করতে হয়, php.ini ফাইলে নিষিদ্ধকরণ_ফাংশন বন্ধ করতে হবে।
নিষিদ্ধকরণ বিপরীত করার জন্য নিম্নলিখিত পদক্ষেপ গ্রহণ করুন।

## পদক্ষেপ প্রথম
'webman/console' ইনস্টল করুন
```
composer require webman/console ^v1.2.35
```

কমান্ড এটি সাক্ষাৎ করুন
```
php webman fix-disable-functions
```

## পদক্ষেপ দ্বিতীয়

স্ক্রিপ্ট চালান ```curl -Ss https://www.workerman.net/webman/fix-disable-functions | php``` যাতে নিষিদ্ধ বিপরীত হতে পারে।

## পদক্ষেপ তৃতীয়

চালান ```php --ini```, php cli এর php.ini ফাইল এর অবস্থান খুঁজুন

php.ini খোলুন, 'disable_functions' খুঁজুন, নিম্নলিখিত ফাংশনগুলির কল বন্ধ করুন
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
```
