# Empacotamento Binário

O webman suporta a compactação do projeto em um arquivo binário, permitindo que o webman seja executado em sistemas Linux sem a necessidade de um ambiente PHP.

> **Atenção**  
> O arquivo compactado atualmente só é compatível com sistemas Linux com arquitetura x86_64 e não é compatível com sistemas Mac.  
> É necessário desativar a opção de configuração `phar` no arquivo `php.ini`, ou seja, defina `phar.readonly = 0`.

## Instalação da Ferramenta de Linha de Comando
`composer require webman/console ^1.2.24`

## Configurações
Abra o arquivo `config/plugin/webman/console/app.php` e defina
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
Para excluir alguns diretórios e arquivos inúteis ao compactar o projeto, evitando que o arquivo compactado tenha um tamanho excessivo.

## Compactação
Execute o comando
```sh
php webman build:bin
```
Também é possível especificar a versão do PHP para a compactação, por exemplo
```sh
php webman build:bin 8.1
```

Após a compactação, o arquivo `webman.bin` será gerado no diretório `build`.

## Inicialização
Faça o upload do arquivo webman.bin para o servidor Linux e execute `./webman.bin start` ou `./webman.bin start -d` para iniciar.

## Princípio
* Primeiro, o projeto webman local é compactado em um arquivo phar.
* Em seguida, o arquivo php8.x.micro.sfx é baixado remotamente.
* O arquivo php8.x.micro.sfx e o arquivo phar são concatenados em um arquivo binário.

## Observações
* Qualquer versão do PHP local a partir de 7.2 pode executar o comando de compactação.
* No entanto, somente é possível compactar o arquivo binário para o PHP 8.
* É altamente recomendável que a versão local do PHP e a versão de compactação sejam compatíveis. Ou seja, se o PHP local for 8.0, a compactação também deve ser feita com o PHP 8.0, para evitar problemas de compatibilidade.
* A compactação fará o download do código-fonte do PHP 8, mas não instalará localmente, não afetando o ambiente PHP local.
* Atualmente, o webman.bin só é compatível com sistemas Linux com arquitetura x86_64 e não é compatível com sistemas Mac.
* Por padrão, o arquivo env não é compactado (definido por exclude_files em `config/plugin/webman/console/app.php`), portanto, ao iniciar, o arquivo env deve ser colocado no mesmo diretório que o webman.bin.
* Durante a execução, será criado um diretório runtime no mesmo diretório do webman.bin para armazenar arquivos de log.
* Atualmente, o webman.bin não lerá um arquivo php.ini externo; se for necessário personalizar o php.ini, defina-o em `/config/plugin/webman/console/app.php` no campo custom_ini.

## Download Separado do PHP Estático
Às vezes, você só precisa do arquivo executável do PHP sem implementar o ambiente PHP, para isso, clique [aqui para baixar o PHP estático](https://www.workerman.net/download).

> **Dica**  
> Se você precisar especificar o arquivo php.ini para o PHP estático, use o seguinte comando `php -c /seu/caminho/php.ini start.php start -d`.

## Extensões Suportadas
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

## Fonte do Projeto
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
