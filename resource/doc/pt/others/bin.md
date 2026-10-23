# Empacotamento Binário

O webman suporta empacotar o projeto em um único arquivo binário, permitindo execução em Linux sem ambiente PHP.

> **Atenção**
> O arquivo empacotado atualmente suporta execução apenas em sistemas Linux x86_64. Windows e macOS não são suportados.
> É necessário desativar a opção phar em `php.ini`, definindo `phar.readonly = 0`.

## Instalar ferramenta de linha de comando
`composer require webman/console`

## Empacotamento
Execute o comando
```
php webman build:bin
```
Também é possível especificar a versão do PHP para empacotar, por exemplo
```
php webman build:bin 8.3
```

Após o empacotamento, um arquivo `webman.bin` será gerado no diretório build.

## Inicialização
Envie webman.bin ao servidor Linux e execute `./webman.bin start` ou `./webman.bin start -d` para iniciar.

## Princípio
* Primeiro o projeto webman local é empacotado em um arquivo phar
* Em seguida php8.x.micro.sfx é baixado remotamente
* php8.x.micro.sfx e o arquivo phar são concatenados em um único arquivo binário

## Observações
* É altamente recomendável usar a mesma versão de PHP local e para empacotamento (ex.: PHP 8.3 para ambos) para evitar problemas de compatibilidade
* O empacotamento baixa o código-fonte do PHP 8 mas não o instala localmente, sem afetar o ambiente PHP local
* webman.bin atualmente suporta apenas Linux x86_64 e não suporta macOS
* Projetos empacotados não suportam reload; atualizações de código requerem reinício
* Por padrão o arquivo env não é empacotado (controlado por exclude_files em `config/plugin/webman/console/app.php`). Na inicialização o arquivo env deve estar no mesmo diretório de webman.bin
* Durante a execução é criado um diretório runtime no diretório de webman.bin para armazenar logs
* webman.bin atualmente não lê arquivos php.ini externos. Para personalizar php.ini, configure custom_ini em `config/plugin/webman/console/app.php`
* Alguns arquivos não precisam ser empacotados; configure exclusões em `config/plugin/webman/console/app.php` para evitar pacotes excessivamente grandes
* O empacotamento binário não suporta corrotinas Swoole
* Nunca armazene arquivos enviados por usuários no pacote binário. Operar neles via o protocolo `phar://` é muito perigoso (vulnerabilidade de desserialização phar). Arquivos enviados devem ficar em disco separado fora do pacote
* Se sua aplicação precisa enviar arquivos para o diretório public, coloque o diretório public junto a webman.bin, configure `config/app.php` assim e empacote novamente:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## Baixar PHP estático separadamente
Se você só precisa de um executável PHP sem implantar ambiente PHP completo, [baixe PHP estático aqui](https://www.workerman.net/download).

> **Dica**
> Para especificar php.ini para PHP estático: `php -c /your/path/php.ini start.php start -d`

## Extensões suportadas
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## Fonte do projeto

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
