# Programming Notes

## Operating System
webman supports both Linux and Windows systems. However, because workerman does not support multi-process settings and daemon processes on Windows, it is recommended to use Windows only for development and debugging purposes. For production environments, please use Linux.

## Startup method
On Linux, use the command `php start.php start` (debug mode) or `php start.php start -d` (daemon mode) to start the server.
On Windows, execute `windows.bat` or use the command `php windows.php` to start the server, and press Ctrl+C to stop it. Windows does not support commands such as stop, reload, status, and connections.

## Resident memory
webman is a resident memory framework, which means that once a PHP file is loaded into memory, it will be reused and not read from the disk again (except for template files). Therefore, in a production environment, changes to business code or configurations require executing `php start.php reload` to take effect. If you have changed process-related configurations or installed new composer packages, you need to restart with `php start.php restart`.

> For the convenience of development, webman comes with a monitor custom process for monitoring updates to business files. When a business file is updated, it will automatically execute a reload. This feature is only enabled when workerman is running in debug mode (without the `-d` flag) and is only available for Windows users by executing `windows.bat` or `php windows.php`.

## About output statements
In traditional PHP-FPM projects, using functions like `echo` or `var_dump` will directly display the data on the page. However, in webman, these outputs are often displayed in the terminal and not in the page (except for outputs in template files).

## Do not execute `exit` or `die` statements
Executing `die` or `exit` will cause the process to exit and restart, resulting in the current request being unable to be responded correctly.

## Do not execute the `pcntl_fork` function
Using `pcntl_fork` to create a process is not allowed in webman.
