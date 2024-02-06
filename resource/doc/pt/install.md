# Requisitos de ambiente

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0

### 1. Criando o projeto

```php
composer create-project workerman/webman
```

### 2. Executando

Acesse o diretório webman

#### Usuários do Windows
Clique duas vezes em `windows.bat` ou execute `php windows.php` para iniciar

> **Observação** 
> Se houver erros, é possível que algumas funções estejam desativadas. Consulte [Verificação de Funções Desativadas](others/disable-function-check.md) para ativá-las

#### Usuários do Linux
Executar em modo `debug` (para desenvolvimento e depuração)

```php
php start.php start
```

Executar em modo `daemon` (para ambiente de produção)

```php
php start.php start -d
```

> **Observação**
> Se houver erros, é possível que algumas funções estejam desativadas. Consulte [Verificação de Funções Desativadas](others/disable-function-check.md) para ativá-las

### 3. Acesso

Acesse o navegador em `http://endereço_ip:8787`
