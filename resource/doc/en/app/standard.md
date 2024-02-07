# Application Plugin Development Specification

## Requirements for Application Plugins
- Plugins cannot contain infringing code, icons, images, etc.
- The plugin source code must be complete and cannot be encrypted.
- Plugins must provide complete functionality and cannot have simple functions.
- Complete function introduction and documentation must be provided.
- Plugins cannot contain sub-markets.
- No text or promotional links are allowed in the plugin.

## Application Plugin Identifier
Each application plugin has a unique identifier composed of letters. This identifier affects the source code directory of the application plugin, the namespace of the class, and the prefix of the plugin database table.

Assuming the developer uses "foo" as the plugin identifier, the source code directory for the plugin will be `{main project}/plugin/foo`, the corresponding plugin namespace will be `plugin\foo`, and the table prefix will be `foo_`.

Since the identifier is unique throughout the network, developers need to check if the identifier is available before development. The check address is [Application Identifier Check](https://www.workerman.net/app/check).

## Database
- Table names consist of lowercase letters `a-z` and underscores `_`.
- The plugin data table should have the plugin identifier as a prefix. For example, the "article" table for the "foo" plugin is `foo_article`.
- The primary key of the table should be indexed as "id".
- Use the InnoDB engine for storage.
- Use the utf8mb4_general_ci character set.
- Database ORM can use Laravel or ThinkORM.
- Time fields are recommended to use DateTime.

## Coding Standards

#### PSR Standards
Code should comply with the PSR-4 autoloading standards.

#### Class Names 
Class names should be in PascalCase with an uppercase first letter.

```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Properties and Methods
Properties and methods in a class should be in camelCase with a lowercase first letter.

```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Methods that do not require authentication
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * Get comments
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### Comments
Properties and methods in a class must include comments including overview, parameters, and return types.

#### Indentation
Code should be indented using 4 spaces, not tabs.

#### Flow Control
Flow control keywords (if, for, while, foreach, etc.) should be followed by a space, and the starting brace should be on the same line as the flow control keyword.

```php
foreach ($users as $uid => $user) {

}
```

#### Temporary Variable Names
Use camelCase with a lowercase first letter for temporary variable names (not mandatory).

```php
$articleCount = 100;
```
