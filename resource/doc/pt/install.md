# Requisitos do Ambiente

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0

### 1. Criar um Projeto

```php
composer create-project workerman/webman
```

### 2. Execução

Navegue até o diretório webman

#### Usuários Windows
Clique duas vezes em `windows.bat` ou execute `php windows.php` para iniciar

> **Nota**
> Se houver algum erro, é possível que algumas funções estejam desativadas. Consulte [Verificação de Funções Desativadas](others/disable-function-check.md) para ativá-las.

#### Usuários Linux
Execute em modo `debug` (para desenvolvimento e depuração)

```php
php start.php start
```

Execute em modo `daemon` (para ambiente de produção)

```php
php start.php start -d
```

> **Nota**
> Se houver algum erro, é possível que algumas funções estejam desativadas. Consulte [Verificação de Funções Desativadas](others/disable-function-check.md) para ativá-las.

### 3. Acesso

Acesse pelo navegador: `http://endereço IP:8787`
