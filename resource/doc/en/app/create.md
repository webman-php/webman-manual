# Creating Application Plugins

## Unique Identifier

Each plugin has a unique identifier, and developers need to come up with an identifier before development and check that the identifier is not already taken. You can check the availability of an identifier [here](https://www.workerman.net/app/check).

## Creation

Install the webman command line by running `composer require webman/console`.

Use the command `php webman app-plugin:create {plugin_identifier}` to create a local application plugin. 

For example, `php webman app-plugin:create foo`.

Restart webman.

Visit `http://127.0.0.1:8787/app/foo`. If there is a returned content, it means the creation was successful.
