# Execution flow

## Process startup flow

The flow of execution after executing php start.php start is as follows：

1. Load configuration under config/
2. Set Worker related configuration like `pid_file` `stdout_file` `log_file` `max_package_size` etc.
3. Create webman process and listen to the port (default8787）
4. Create custom process based on configuration
5. webmanThe following logic is executed after the process and custom processes start (the following are executed in onWorkerStart))：
  ① Load the file set in `config/autoload.php`, e.g. `app/functions.php`
  ② load `config/middleware.php` (include`config/plugin/*/*/middleware.php`)middleware set in
  ③ Execute `config/bootstrap.php` (include`config/plugin/*/*/bootstrap.php`)Modify in filestartMethod，Used to initialize some modules，for exampleLaravelDatabase initialization connection
  ④ load `config/route.php` (include`config/plugin/*/*/route.php`)Methods will not be checked

## Processing Request Flow
1. Determine if the request url corresponds to a static file under public, if so return the file (end of request), if not enter2
2. Determine if a route is hit based on the url, no hit into 3, hit into4
3. Is the default route turned off, if yes return 404 (end request), if not enter4
4. Find the middleware that requests the corresponding controller，performs middleware predecessor operations in order(Onion Model Request Phase)，Execute controller business logic，Perform middleware post-operation(Onion Model Response Phase)，So it is。(in reference[Development of high performance](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))

