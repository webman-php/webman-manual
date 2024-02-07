# Empacotamento Phar

Phar é um tipo de arquivo de empacotamento semelhante ao JAR no PHP. Você pode usar o Phar para empacotar o seu projeto webman em um único arquivo Phar para facilitar a implantação.

**Um grande agradecimento para [fuzqing](https://github.com/fuzqing) pelo PR.**

> **Nota**
> Você precisa desativar as opções de configuração de Phar no `php.ini`, ou seja, definir `phar.readonly = 0`.

## Instale a ferramenta de linha de comando
`composer require webman/console`

## Configurações
Abra o arquivo `config/plugin/webman/console/app.php` e defina `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`. Isso permite excluir alguns diretórios e arquivos desnecessários durante o empacotamento, evitando um tamanho excessivo.

## Empacotamento
No diretório raiz do projeto webman, execute o comando `php webman phar:pack`. Isso criará um arquivo `webman.phar` no diretório "build".

> As configurações relacionadas ao empacotamento estão em `config/plugin/webman/console/app.php`.

## Comandos de início e parada relacionados
**Iniciar**
`php webman.phar start` ou `php webman.phar start -d`

**Parar**
`php webman.phar stop`

**Verificar o status**
`php webman.phar status`

**Verificar o status da conexão**
`php webman.phar connections`

**Reiniciar**
`php webman.phar restart` ou `php webman.phar restart -d`

## Observações
* Após executar o webman.phar, um diretório "runtime" será criado no mesmo diretório que o webman.phar, usado para armazenar arquivos temporários, como logs.

* Se o seu projeto usa um arquivo .env, você precisará colocar o arquivo .env no mesmo diretório que o webman.phar.

* Se o seu negócio precisa fazer upload de arquivos para o diretório "public", você precisará separar o diretório "public" e colocá-lo no mesmo diretório que o webman.phar. Nesse caso, você precisará configurar `config/app.php`.
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
O negócio pode usar a função auxiliar `public_path()` para encontrar a localização real do diretório "public".

* O webman.phar não suporta a execução de processos personalizados no Windows.
