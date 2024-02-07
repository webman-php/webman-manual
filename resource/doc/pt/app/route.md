## Arquivo de Configuração de Rotas
O arquivo de configuração de rotas do plugin está em `plugin/nome_do_plugin/config/route.php`

## Rota Padrão
Os caminhos de URL do plugin de aplicativo começam com `/app`, por exemplo, o caminho da URL do `plugin\foo\app\controller\UserController` é `http://127.0.0.1:8787/app/foo/user`

## Desativar Rota Padrão
Se desejar desativar a rota padrão de um determinado plugin de aplicativo, configure no arquivo de rota da seguinte forma:
```php
Route::disableDefaultRoute('foo');
```

## Callback de Tratamento do Erro 404
Se desejar definir um fallback para um determinado plugin de aplicativo, é necessário passar o nome do plugin como segundo parâmetro, por exemplo:
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
