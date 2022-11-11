# Basic Plugins

The base plugins are generally generic components that are installed using composer and the code is placed under vendor. During installation, some custom configurations (middleware, process, routing, etc.) can be automatically copied to the `{main project}config/plugin` directory. webman will automatically recognize the directory configuration and merge the configuration into the main configuration, thus allowing the plugin to intervene in any lifecycle of webman。


Other databases[If you want to use](create.md)