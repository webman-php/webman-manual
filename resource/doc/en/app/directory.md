# Directory Structure

```
plugin/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    ├── public
    └── api
```

We can see that an application plugin has the same directory structure and configuration files as webman. In fact, the development experience is similar to developing a regular webman application.
The plugin directory and naming follow the PSR4 specification. Since the plugins are placed in the plugin directory, their namespaces start with `plugin`, for example `plugin\foo\app\controller\UserController`.

## About the api directory
Each plugin has an `api` directory. If your application provides some internal interfaces to be called by other applications, you should place the interfaces in the `api` directory.
Please note that these interfaces refer to function call interfaces, not network call interfaces.
For example, the `Email plugin` provides an `Email::send()` interface in `plugin/email/api/Email.php` to send emails to other applications.
In addition, `plugin/email/api/Install.php` is automatically generated and used for webman-admin plugin market to execute install or uninstall operations.
