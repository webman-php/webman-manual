# Criar um plugin de aplicativo

## Identificação única

Cada plugin possui uma identificação única de aplicativo. Antes de desenvolver, os desenvolvedores precisam escolher uma identificação e verificar se ela não está em uso. Verifique a identificação em [Verificação de Identificação de Aplicativo](https://www.workerman.net/app/check)

## Criação

Execute `composer require webman/console` para instalar o comando webman

Use o comando `php webman app-plugin:create {identificação do plugin}` para criar um plugin de aplicativo localmente

Por exemplo, `php webman app-plugin:create foo`

Reinicie o webman

Acesse `http://127.0.0.1:8787/app/foo` Se houver conteúdo retornado, significa que foi criado com sucesso.
