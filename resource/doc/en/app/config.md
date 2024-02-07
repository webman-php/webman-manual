# Configuration File

The configuration of plugins is similar to normal webman projects, but the configuration of plugins generally only affects the current plugin and has no impact on the main project.
For example, the value of `plugin.foo.app.controller_suffix` only affects the controller suffix of the plugin and has no impact on the main project.
For example, the value of `plugin.foo.app.controller_reuse` only affects whether the plugin reuses controllers and has no impact on the main project.
For example, the value of `plugin.foo.middleware` only affects the middleware of the plugin and has no impact on the main project.
For example, the value of `plugin.foo.view` only affects the views used by the plugin and has no impact on the main project.
For example, the value of `plugin.foo.container` only affects the container used by the plugin and has no impact on the main project.
For example, the value of `plugin.foo.exception` only affects the exception handling class of the plugin and has no impact on the main project.

However, since routing is global, the plugin's routing configuration also affects the global routing.

## Getting Configuration
To get the configuration of a specific plugin, use the method `config('plugin.{plugin}.{specific_config}');`, for example, to get all configurations of `plugin/foo/config/app.php`, use `config('plugin.foo.app')`.
Similarly, the main project or other plugins can use `config('plugin.foo.xxx')` to get the configuration of the foo plugin.

## Unsupported Configurations
Application plugins do not support the `server.php` and `session.php` configurations. They also do not support the `app.request_class`, `app.public_path`, and `app.runtime_path` configurations.
