# Logging
The usage of the logging class is similar to the database class.

```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```
If you want to reuse the logging configuration of the main project, you can simply use:

```php
use support\Log;
Log::info('Log content');
// Assuming the main project has a logging configuration named 'test'
Log::channel('test')->info('Log content');
```
