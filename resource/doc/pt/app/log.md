# Registo
O uso da classe de registo é semelhante ao uso de base de dados:
```php
use support\Log;
Log::channel('plugin.admin.default')->info('teste');
```

Se pretender reutilizar a configuração de registo do projeto principal, simplesmente utilize:
```php
use support\Log;
Log::info('Conteúdo do registo');
// Supondo que o projeto principal tenha uma configuração de registo chamada test
Log::channel('test')->info('Conteúdo do registo');
```
