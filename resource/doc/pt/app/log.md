# Registos
O uso da classe de registo é semelhante ao uso de base de dados.
```php
use support\Log;
Log::channel('plugin.admin.default')->info('teste');
```

Se desejar reutilizar a configuração de registo do projeto principal, use simplesmente
```php
use support\Log;
Log::info('Conteúdo do registo');
// Supondo que o projeto principal tenha uma configuração de registo para "teste"
Log::channel('teste')->info('Conteúdo do registo');
```
