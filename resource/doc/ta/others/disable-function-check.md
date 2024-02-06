# செயல்முறைகள் முடக்கு

இந்த ஸ்கிரிப்ட் மூலம் முடக்கப்பட்ட செயல்முறைகள் உள்ளது என்பதை சரிபார்க்க வேண்டும். கமாண்ட் லைண் ரன் செய்தல்```curl -Ss https://www.workerman.net/webman/check | php``` 

உங்கள் webman உபயோகப்படுத்தினல் முடக்கப்பட்ட செயல்முறைகளை உள்ளிடுகின்றது என்பதைச் சரிசெய்யும். webman உபயோகிக்கும் துணைகளை சொந்தமாக்க வேண்டும் என்பதற்கான விதியும்.

## விதி ஒன்று
`webman/console` ஐ நிறுவுக
```
composer require webman/console ^v1.2.35
```

கட்டளை இயக்கு
```
php webman fix-disable-functions
```

## விதி இரண்டு

ஸ்கிரிப்ட்பை இயக்கு `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` முடக்கும்

## விதி மூன்று

`php --ini` ஐ இயக்கு மொழிபெயர்ப்பு cli பயன்பாட்டின் php.ini கோப்பு இடத்தை கண்டுபிடிக்கவும்.

php.ini ஐ திற, `disable_functions` ஐ கண்டுபிடிக்கவும், கீழே குறிப்பிட்ட செயல்முறைகளை நீக்குவதற்கான வழிகளை மூலம்.
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
