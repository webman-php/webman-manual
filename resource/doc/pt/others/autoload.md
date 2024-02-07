# Carregamento automático

## Carregamento de arquivos PSR-0 usando o composer
O webman segue a especificação de carregamento automático `PSR-4`. Se o seu aplicativo precisar carregar bibliotecas de código seguindo a especificação `PSR-0`, siga as etapas abaixo.

- Crie o diretório `extend` para armazenar as bibliotecas seguindo a especificação `PSR-0`.
- Edite o arquivo `composer.json` e adicione o seguinte conteúdo dentro de `autoload`:

```js
"psr-0" : {
    "": "extend/"
}
```
O resultado final será semelhante a:
![](../../assets/img/psr0.png)

- Execute `composer dumpautoload`
- Execute `php start.php restart` para reiniciar o webman (observe que é necessário reiniciar para que as alterações tenham efeito)

## Carregamento de arquivos usando o composer

- Edite o arquivo `composer.json` e adicione os arquivos que deseja carregar dentro de `autoload.files`:

```json
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Execute `composer dumpautoload`
- Execute `php start.php restart` para reiniciar o webman (observe que é necessário reiniciar para que as alterações tenham efeito)

> **Observação**
> Os arquivos configurados em `autoload.files` do `composer.json` serão carregados antes do início do webman. Os arquivos carregados por meio do arquivo `config/autoload.php` do framework serão carregados após o início do webman.
> As alterações nos arquivos carregados por meio de `autoload.files` do `composer.json` só terão efeito após a reinicialização, não serão aplicadas com o recarregamento. Já os arquivos carregados por meio do arquivo `config/autoload.php` do framework suportam recarregamento, ou seja, as alterações nesses arquivos serão aplicadas com o recarregamento.

## Carregamento de arquivos usando o framework
Alguns arquivos podem não seguir a especificação PSR, impossibilitando o carregamento automático. Nesses casos, podemos configurar o carregamento desses arquivos por meio do arquivo `config/autoload.php`, como no exemplo abaixo:

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
 > No arquivo `autoload.php`, configuramos o carregamento dos arquivos `support/Request.php` e `support/Response.php`, pois esses arquivos também estão presentes em `vendor/workerman/webman-framework/src/support/`. Ao priorizar o carregamento dos arquivos localizados no diretório raiz do projeto, podemos personalizar o conteúdo desses arquivos sem precisar modificar os arquivos presentes no diretório `vendor`. Se não for necessário personalizá-los, essas configurações podem ser ignoradas.
