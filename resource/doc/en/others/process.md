# Execution Flow

## Process Start Flow

After executing `php start.php start`, the execution flow is as follows:

1. Load configurations under the `config/` directory.
2. Set relevant configurations for Worker, such as `pid_file`, `stdout_file`, `log_file`, `max_package_size`, etc.
3. Create the webman process and listen to the port (default: 8787).
4. Create custom processes based on the configurations.
5. After the webman process and custom processes are started, the following logic is executed (all within `onWorkerStart`):
   ① Load files set in `config/autoload.php`, such as `app/functions.php`.
   ② Load middleware settings in `config/middleware.php` (including `config/plugin/*/*/middleware.php`).
   ③ Execute the `start` method of classes set in `config/bootstrap.php` (including `config/plugin/*/*/bootstrap.php`) to initialize certain modules, such as initializing connections to the Laravel database.
   ④ Load routes defined in `config/route.php` (including `config/plugin/*/*/route.php`).

## Request Handling Flow

1. Check if the requested URL corresponds to a static file under the `public` directory. If yes, return the file (end request). If not, proceed to step 2.
2. Based on the URL, determine if it matches a certain route. If not, proceed to step 3. If it matches, proceed to step 4.
3. Check if the default route has been disabled. If yes, return a 404 (end request). If not, proceed to step 4.
4. Find the middleware for the requested controller, execute the middleware's pre-processing operations in order (onion model request phase), execute the controller's business logic, and then execute the middleware's post-processing operations (onion model response phase) to end the request. (Refer to [Middleware Onion Model](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))