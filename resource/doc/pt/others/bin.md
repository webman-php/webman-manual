# Empacotamento binário

O webman suporta empacotar o projeto em um arquivo binário, o que permite que o webman seja executado em sistemas Linux sem a necessidade de um ambiente PHP.

> **Observação**
> O arquivo empacotado atualmente só suporta a execução em sistemas Linux com arquitetura x86_64, não suporta o sistema Mac.
> É necessário desativar a opção de configuração phar do `php.ini`, ou seja, configurar `phar.readonly = 0`.

## Instalação da ferramenta de linha de comando
`composer require webman/console ^1.2.24`

## Configurações
Abra o arquivo `config/plugin/webman/console/app.php` e defina
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
para excluir alguns diretórios e arquivos inúteis durante o empacotamento, evitando assim um volume de empacotamento muito grande.

## Empacotamento
Execute o comando
```
php webman build:bin
```
Também é possível especificar a versão do PHP para o empacotamento, por exemplo
```
php webman build:bin 8.1
```

Após o empacotamento, um arquivo `webman.bin` será gerado no diretório build.

## Inicialização
Faça o upload do webman.bin para o servidor Linux, execute `./webman.bin start` ou `./webman.bin start -d` para iniciar.

## Princípio
* Primeiramente, empacote o projeto webman local em um arquivo phar
* Em seguida, baixe o php8.x.micro.sfx remotamente para o local
* Combine o php8.x.micro.sfx e o arquivo phar em um arquivo binário

## Observações
* O PHP local com versão >= 7.2 pode executar o comando de empacotamento
* No entanto, somente é possível empacotar em um arquivo binário do PHP8
* É altamente recomendável que a versão do PHP local seja compatível com a versão de empacotamento, ou seja, se o PHP local for 8.0, o empacotamento também deve ser feito com o PHP 8.0, para evitar problemas de compatibilidade
* O empacotamento fará o download do código-fonte do PHP8, mas não o instalará localmente, não afetando o ambiente PHP local
* O webman.bin atualmente só é compatível com a execução em sistemas Linux com arquitetura x86_64, não sendo compatível com sistemas Mac
* Por padrão, o arquivo env não é empacotado (`config/plugin/webman/console/app.php` controla os arquivos excluídos), portanto, o arquivo env deve ser colocado no mesmo diretório que o webman.bin ao iniciar
* Durante a execução, um diretório runtime será gerado no diretório onde está o webman.bin, para armazenar os arquivos de log
* Atualmente, o webman.bin não lerá um arquivo php.ini externo. Se for necessário personalizar o php.ini, defina-o no arquivo `/config/plugin/webman/console/app.php` em custom_ini

## Download do PHP estático separadamente
Às vezes, você só precisa do executável do PHP e não quer implantar um ambiente PHP completo. Para isso, clique [aqui para baixar o PHP estático](https://www.workerman.net/download)

> **Dica**
> Se precisar especificar um arquivo php.ini para o PHP estático, use o seguinte comando `php -c /seu/caminho/php.ini start.php start -d`

## Extensões suportadas
bcmath
calendar
Core
ctype
curl
date
dom
event
exif
FFI
fileinfo
filter
gd
hash
iconv
json
libxml
mbstring
mongodb
mysqlnd
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
posix
readline
redis
Reflection
session
shmop
SimpleXML
soap
sockets
SPL
sqlite3
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

## Origem do projeto

https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
