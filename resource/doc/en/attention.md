# Programming notes

## Operating System
webman supports running on both Linux and Windows systems. However, due to the inability of Workerman to support multi-process settings and daemon processes on Windows, it is recommended to use Windows only for development and debugging. For a production environment, please use Linux.

## Startup Method
**For Linux system**, use the command `php start.php start` (debug mode) or `php start.php start -d` (daemon mode) to start.
**For Windows system**, run `windows.bat` or use the command `php windows.php` to start, and stop by pressing ctrl c. Windows system does not support commands such as stop, reload, status, and connections.

## Resident Memory
webman is a resident memory framework. Generally, after a PHP file is loaded into memory, it will be reused and not read from the disk again (except for template files). Therefore, in the production environment, business code or configuration changes need to execute `php start.php reload` to take effect. If there are changes to process-related configurations or installation of new composer packages, you need to restart using `php start.php restart`.

> For development convenience, webman comes with a monitor custom process to monitor the updates of business files. When there are updates to business files, a reload will be automatically triggered. This feature is only enabled when Workerman is running in debug mode (without `-d` when starting). Windows users need to run `windows.bat` or `php windows.php` to enable this feature.

## Output Statement
In traditional PHP-FPM projects, using functions like `echo` and `var_dump` to output data will be displayed directly on the page. However, in webman, these outputs are often displayed on the terminal and will not appear on the page (except for outputs in template files).

## Avoid Using `exit` and `die` Statements
Executing `die` or `exit` will cause the process to exit and restart, leading to the inability to respond correctly to the current request.

## Avoid Using the `pcntl_fork` Function
Creating a process using `pcntl_fork` is not allowed in webman.