# Basic Plugins

Basic plugins are generally common components that are usually installed using Composer and placed in the vendor directory. During installation, custom configurations (such as middleware, processes, routes, etc.) can be automatically copied to the `{main project}config/plugin` directory. Webman will automatically recognize the configurations in this directory and merge them into the main configuration, allowing the plugins to intervene in any phase of webman's lifecycle.

For more information, refer to [Creating Basic Plugins](create.md).
