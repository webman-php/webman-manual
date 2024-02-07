# Execution Flow

## Process Startup Flow

When executing `php start.php start`, the execution flow is as follows:

1. Load the configurations under the config/ directory.
2. Set the relevant configurations for the Worker, such as `pid_file`, `stdout_file`, `log_file`, `max_package_size`, etc.
3. Create the webman process and start listening on the port (default: 8787).
4. Create custom processes based on the configuration.
5. After the webman process and custom processes are started, the following logic is executed (all within the `onWorkerStart` method):
    ① Load the files set in `config/autoload.php`, such as `app/functions.php`.
    ② Load the middlewares set in `config/middleware.php` (including `config/plugin/*/*/middleware.php`).
    ③ Execute the `start` method of the classes set in `config/bootstrap.php` (including `config/plugin/*/*/bootstrap.php`) for initializing some modules, such as Laravel database connection.
    ④ Load the routes defined in `config/route.php` (including `config/plugin/*/*/route.php`).

## Request Handling Flow
1. Check if the requested URL corresponds to a static file under the public directory. If yes, return the file (end of the request). If not, proceed to step 2.
2. Determine if the URL matches a certain route. If not matched, proceed to step 3. If matched, proceed to step 4.
3. Check if the default route is disabled. If yes, return 404 (end of the request). If not, proceed to step 4.
4. Find the middlewares for the requested controller, execute the middleware pre-operations in order (onion model request phase), execute the controller's business logic, execute the middleware post-operations (onion model response phase), and conclude the request (refer to [Middleware Onion Model](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B)).
