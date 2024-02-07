# ফাংশন নিষেধ চেক করুন

দিয়ে স্ক্রিপ্ট ব্যবহার করে নিষিদ্ধ ফাংশনের জন্য পরীক্ষা করুন। ```curl -Ss https://www.workerman.net/webman/check | php``` কমান্ড লাইন দিয়ে চালান।

যদি আপনি ফাংশন নাম "অন" বন্ধ করা হয়েছে বলে তাহলে এর মাধ্যমে বুঝা যাবে। পরিষেবাসমূহ ফাঙ্শনের নিষিদ্ধ করা হয়েছে, তাই webman প্রচুর ব্যবহার করতে সমস্যা হয়। এটি ঠিক করার জন্য php.ini থেকে নিষিদ্ধকরণ সরাতে হয়। নিষিদ্ধকরণ সরাতে নিম্নলিখিত পদ্ধতির মধ্যে যে কোনটি একটি নির্বাচন করুন।

## বিধান এক

`webman/console` ইনস্টল করুন
```
composer require webman/console ^v1.2.35
```

কমান্ড ব্যবহার করুন
```
php webman fix-disable-functions
```

## বিধান দুই

স্ক্রিপ্ট ব্যবহার করুন `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` নিষিদ্ধকরণ সরাতে।

## বিধান তিন

`php --ini` চালান, php cli ফাইলের স্থান খুঁজুন

php.ini খুলুন, 'disable_functions' খুঁজুন, নিম্নলিখিত ফাংশনগুলি আপনি বাতিল করে দিন
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
