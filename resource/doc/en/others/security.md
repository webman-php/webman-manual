# Security

## Running User
It is recommended to set the running user to a lower privilege user, such as the user running nginx. The running user can be set in `config/server.php` under `user` and `group`. Similarly, the user for custom processes is specified in `config/process.php` under `user` and `group`. It is important to note that the monitor process should not have a running user set, as it requires higher privileges to function properly.

## Controller Specification
Only controller files should be placed in the `controller` directory or its subdirectories. Placing other types of files is prohibited. Otherwise, when the [controller suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) is not enabled, class files may be illegally accessed through URLs, leading to unpredictable consequences. For example, `app/controller/model/User.php` may actually be a Model class but is mistakenly placed in the `controller` directory. When the [controller suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) is not enabled, this may allow users to access any method in `User.php` through URLs like `/model/user/xxx`. To completely eliminate this situation, it is strongly recommended to use [controller suffix](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) to clearly indicate which are controller files.

## XSS Filtering
For the sake of generality, webman does not perform XSS escaping on requests. Webman strongly recommends performing XSS escaping during rendering, rather than before data entry. Additionally, templates such as twig, blade, think-template, etc., automatically execute XSS escaping, making it very convenient without the need for manual escaping.

> **Note**
> If you perform XSS escaping before data entry, it is very likely to cause incompatibility issues with some application plugins.

## Preventing SQL Injection
To prevent SQL injection, it is advisable to use an ORM, such as [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) or [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html) and avoid manually assembling SQL.

## Nginx Proxy
When exposing your application to external users, it is strongly recommended to add an nginx proxy in front of webman. This can filter out some illegal HTTP requests and improve security. For more details, please refer to [nginx proxy](nginx-proxy.md).