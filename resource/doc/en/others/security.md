# Security

## Running User
It is recommended to set the running user to a user with lower privileges, such as the user running nginx. The running user can be set in `config/server.php` using the `user` and `group` parameters. Similar custom processes specify the user through `user` and `group` in `config/process.php`. It should be noted that the monitor process should not be set to run as a user, as it requires high permissions to function properly.

## Controller Specification
Only controller files are allowed in the `controller` directory or its subdirectories. Placing other types of files is prohibited. Otherwise, when the [controller suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) is not enabled, class files may be illegally accessed via the URL, leading to unpredictable consequences. For example, `app/controller/model/User.php` is actually a Model class, but it is erroneously placed in the `controller` directory. Without the [controller suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) enabled, users can access any method in `User.php` via URLs resembling `/model/user/xxx`. To completely prevent this situation, it is strongly recommended to use the [controller suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) to clearly mark which files are controller files.

## XSS Filtering
For the sake of versatility, webman does not perform XSS escaping on requests. webman strongly recommends performing XSS escaping during rendering rather than before data is inserted into the database. Furthermore, templates such as twig, blade, and think-template automatically perform XSS escaping, eliminating the need for manual escaping, which is very convenient.

> **Note**
> If you perform XSS escaping before data insertion, it may cause compatibility issues with some application plugins.

## Preventing SQL Injection
To prevent SQL injection, it is strongly advised to use ORM as much as possible, such as [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) and [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html), and avoid assembling SQL on your own.

## NGINX Proxy
When your application needs to be exposed to external users, it is highly recommended to add an NGINX proxy in front of webman. This can filter out some illegal HTTP requests and improve security. For more information, please refer to [NGINX Proxy](nginx-proxy.md).
