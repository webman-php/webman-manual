# செயலி முறைகளை முடக்குதல் சோதனை

இந்த ஸ்கிரிப்டை பயன்படுத்தி முடக்குதல் சோதனைகள் உள்ளதா என்பதை சரிபார்க்கவும். கட்டளை வரிசையில் ```curl -Ss https://www.workerman.net/webman/check | php``` என்றால் கட்டளைகள் இணைப்பாதில் இருந்து செயலி முடக்கப்பட்டுள்ளன. இதன் மூலம் அற்புதமாக பயன்பாடு சேமிக்க பயன்படுத்துவதற்கு php.ini இல் முடக்கம் நீக்க வேண்டும்.

வசதிகள் நீக்குதல் குறிப்போடு
கீழே கூடுதல் ஒவ்வொரு முறையும் தேர்ந்தெடுக்கலாம்:

## முறை 1
`webman/console` ஐ நிறுத்து
``` 
composer require webman/console ^v1.2.35
``` 

கட்டளையை உருவாக்க
``` 
php webman fix-disable-functions
``` 

## முறை 2

ஸ்கிரிப்டை இயக்கு
 ``curl -Ss https://www.workerman.net/webman/fix-disable-functions | php`` முடக்கை நீக்க உள்ளிடவும்

## முறை 3

`php --ini`ஐ இயக்கவும் php cli பெயர் php.ini கோப்பு இடத்தைக் காண்பி

php.ini ஐ திற
`disable_functions` - ஐ கண்டுபிடிக்குக, கீழே வரும் செயல் கூறுதலுக்கு மேல் விரித்திருக்கும் function-களை நீக்குக
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
