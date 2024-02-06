## Arquivo de Configuração de Rota
O arquivo de configuração de rota do plugin está em `plugin/nome_do_plugin/config/route.php`

## Rota Padrão
Os caminhos dos URLs do aplicativo do plugin começam com `/app`, por exemplo, o URL do `plugin\foo\app\controller\UserController` é `http://127.0.0.1:8787/app/foo/user`

## Desativar Rota Padrão
Se desejar desativar a rota padrão de um determinado aplicativo de plugin, defina no arquivo de configuração de rota o seguinte:
```php
Route::disableDefaultRoute('foo');
```

## Tratamento de Callback 404
Se desejar definir um fallback para um determinado aplicativo de plugin, é necessário passar o nome do plugin como segundo parâmetro, por exemplo:
```
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
