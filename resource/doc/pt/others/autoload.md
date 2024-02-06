# Carregamento automático

## Carregamento de arquivos seguindo a especificação PSR-0 usando o composer
O webman segue a especificação de carregamento automático `PSR-4`. Se o seu aplicativo precisar carregar bibliotecas de código que seguem a especificação `PSR-0`, siga as instruções a seguir.

- Crie um diretório chamado `extend` para armazenar as bibliotecas que seguem a especificação `PSR-0`.
- Edite o arquivo `composer.json` e adicione o seguinte conteúdo sob o `autoload`:

```js
"psr-0" : {
    "": "extend/"
}
```
O resultado final será semelhante a:
![](../../assets/img/psr0.png)

- Execute `composer dumpautoload`.
- Execute `php start.php restart` para reiniciar o webman (observe que é necessário reiniciar para que as alterações tenham efeito).

## Carregamento de arquivos usando o composer

- Edite o arquivo `composer.json` e adicione os arquivos que deseja carregar sob `autoload.files`:
```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Execute `composer dumpautoload`.
- Execute `php start.php restart` para reiniciar o webman (observe que é necessário reiniciar para que as alterações tenham efeito).

> **Observação**
> Os arquivos especificados em `autoload.files` no arquivo composer.json serão carregados antes mesmo do início do webman. Por outro lado, os arquivos carregados usando o arquivo `config/autoload.php` do framework serão carregados após o início do webman.
> As alterações nos arquivos carregados com base no `autoload.files` do arquivo composer.json requerem uma reinicialização para ter efeito, enquanto as alterações nos arquivos carregados usando o arquivo `config/autoload.php` do framework suportam recarregamento, ou seja, as alterações terão efeito após um recarregamento.

## Carregamento de arquivos usando o framework
Alguns arquivos podem não seguir a especificação SPR e, portanto, não podem ser carregados automaticamente. Nesses casos, podemos usar a configuração do arquivo `config/autoload.php` para carregar esses arquivos, por exemplo:
```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
 > **Observação**
 > Percebemos que o arquivo `autoload.php` foi configurado para carregar dois arquivos, `support/Request.php` e `support/Response.php`. Isso ocorre porque também existe um arquivo com o mesmo nome em `vendor/workerman/webman-framework/src/support/`. Ao configurar o `autoload.php`, priorizamos o carregamento dos arquivos `support/Request.php` e `support/Response.php` do diretório raiz do projeto, permitindo-nos personalizar o conteúdo desses dois arquivos sem precisar modificar os arquivos no diretório `vendor`. Se você não precisar personalizá-los, pode ignorar essa configuração.
