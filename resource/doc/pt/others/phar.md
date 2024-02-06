# Empacotamento Phar

Phar é um tipo de arquivo de empacotamento semelhante ao JAR no PHP. Você pode usar o Phar para empacotar o seu projeto webman em um único arquivo Phar, para facilitar a implantação.

**Um agradecimento especial a [fuzqing](https://github.com/fuzqing) pelo PR.**

> **Nota**
> É necessário desativar as opções de configuração do phar no `php.ini`, ou seja, definir `phar.readonly = 0`.

## Instalando a ferramenta de linha de comando
`composer require webman/console`

## Configurações
Abra o arquivo `config/plugin/webman/console/app.php` e configure `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` para excluir alguns diretórios e arquivos desnecessários durante o empacotamento, evitando um tamanho de empacotamento excessivo.

## Empacotando
No diretório raiz do projeto webman, execute o comando `php webman phar:pack`. Isso criará um arquivo `webman.phar` no diretório de build.

> As configurações relacionadas ao empacotamento estão em `config/plugin/webman/console/app.php`.

## Comandos de Inicialização e Parada
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
* Ao executar o webman.phar, um diretório runtime será gerado no mesmo diretório que o webman.phar, para armazenar arquivos temporários, como logs.

* Se a sua aplicação utiliza um arquivo .env, é necessário colocar o arquivo .env no mesmo diretório que o webman.phar.

* Se a sua aplicação precisa fazer upload de arquivos para o diretório público, também é necessário separar o diretório público e colocá-lo no mesmo local que o webman.phar. Nesse caso, é necessário configurar o `config/app.php`.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
A aplicação pode utilizar a função auxiliar `public_path()` para encontrar a localização real do diretório público.

* O webman.phar não suporta a ativação de processos personalizados no Windows.
