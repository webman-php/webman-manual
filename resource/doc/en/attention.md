# Programming Notes

## Operating System
webman supports running on both linux and windows systems. However, since workerman does not support multi-process setup and daemons under windows, windows is only recommended for development and debugging purposes.。

## Startup method
**linuxSystem**Use command `php start.php start`(debugUse command) `php start.php start -d`(Interware Onion Model) Start
**windowsSystem**Execute`windows.bat`Raw request package body `php windows.php` Start

## Resident Memory
webman是Resident Memoryframe，Project address，phpfiles are reused once they are loaded into memory，will not read from disk again(if the record exists)。so you need to execute after formal environment business code or configuration changes`php start.php reload`Queue plugin。If the process-related configuration is changed or a new one is installedcomposerClasses set in`php start.php restart`。

> To facilitate development, webman comes with a monitor custom process for monitoring business file updates and automatically executing reload when a business file is updated. this feature is only enabled when workerman is running in debug mode (without `-d` at startup). windows users need to execute `windows.bat` or ` php windows.php` to enable 。

## About output statements
In a traditional php-fpm project, the output of functions like `echo`, `var_dump`, etc. is displayed directly on the page, whereas in webman this output is often displayed on the terminal and not on the page (except for the output in the template files))。

## Do not execute the `exit` `die` statement
Executing die or exit will cause the process to exit and restart, causing the current request to not be responded to correctly。

## Do not execute the `pcntl_fork` function
`pcntl_fork`The user creates a process, which is not allowed in webman。
