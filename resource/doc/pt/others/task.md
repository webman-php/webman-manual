# Processamento de Tarefas Lentas

Às vezes, precisamos lidar com tarefas lentas para evitar que afetem o processamento de outras solicitações no webman. Dependendo da situação, essas tarefas podem ser tratadas de diferentes maneiras.

## Usando Fila de Mensagens
Consulte [fila Redis](../queue/redis.md) [fila STOMP](../queue/stomp.md)

### Vantagens
Capacidade de lidar com picos de solicitações de processamento de alto volume.

### Desvantagens
Incapacidade de retornar diretamente resultados para o cliente. Para enviar os resultados, é necessário utilizar outros serviços, como o [webman/push](https://www.workerman.net/plugin/2) para o envio dos resultados do processamento.

## Adicionando uma Porta HTTP

> **Observação**
> Este recurso requer webman-framework>=1.4

Adicione uma porta HTTP para lidar com solicitações lentas, de forma que essas solicitações sejam encaminhadas para um grupo específico de processos que as processarão e retornarão diretamente o resultado para o cliente.

### Vantagens
Capacidade de retornar os dados diretamente para o cliente.

### Desvantagens
Incapacidade de lidar com picos de solicitações de alto volume.

### Passos para Implementação
Adicione a seguinte configuração ao arquivo `config/process.php`.
```php
return [
    // ... outras configurações omitidas ...

    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // número de processos
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // configuração da classe de solicitação
            'logger' => \support\Log::channel('default'), // instância de log
            'app_path' => app_path(), // localização do diretório app
            'public_path' => public_path() // localização do diretório public
        ]
    ]
];
```

Dessa forma, as interfaces lentas podem ser acessadas através do conjunto de processos em `http://127.0.0.1:8686/` sem afetar o processamento de outras interfaces.

Para garantir que o cliente não perceba a diferença entre as portas, pode-se adicionar um proxy para a porta 8686 no nginx. Supondo que o caminho das solicitações de interface lenta comece com `/tast`, a configuração completa do nginx seria semelhante ao seguinte exemplo:
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Adicionar um upstream para 8686
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # Solicitações iniciadas com /tast são encaminhadas para a porta 8686. Altere /tast para o prefixo desejado conforme necessário.
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Outras solicitações são encaminhadas para a porta 8787 original
  location / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```

Dessa forma, ao acessar `dominio.com/tast/xxx`, o acesso será direcionado para a porta 8686, sem afetar o processamento das solicitações na porta 8787.
