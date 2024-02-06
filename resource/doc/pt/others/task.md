# Processamento de solicitações lentas

Às vezes, precisamos lidar com solicitações lentas para evitar que elas afetem o processamento de outras solicitações pelo webman. Dependendo da situação, essas solicitações podem ser tratadas usando diferentes abordagens.

## Utilizando filas de mensagens
Consulte [Fila Redis](https://www.workerman.net/plugin/12) e [Fila STOMP](https://www.workerman.net/plugin/13).

### Vantagens
Capacidade de lidar com solicitações repentinas de processamento de grande volume.

### Desvantagens
Incapacidade de retornar diretamente os resultados para o cliente. Se for necessário enviar os resultados, é preciso combinar com outros serviços, como usar [webman/push](https://www.workerman.net/plugin/2) para enviar os resultados do processamento.

## Adicionando uma porta HTTP

> **Observação**
> Essa funcionalidade requer webman-framework>=1.4

Adiciona uma porta HTTP para lidar com solicitações lentas. Essas solicitações são encaminhadas para um grupo específico de processos ao acessar essa porta, e os resultados são retornados diretamente para o cliente.

### Vantagens
Capacidade de retornar diretamente os dados para o cliente.

### Desvantagens
Incapacidade de lidar com solicitações repentinas de grande volume.

### Procedimento de Implementação
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
            'request_class' => \support\Request::class, // definir a classe de solicitação
            'logger' => \support\Log::channel('default'), // instância de log
            'app_path' => app_path(), // localização do diretório do aplicativo
            'public_path' => public_path() // localização do diretório público
        ]
    ]
];
```

Dessa forma, as solicitações lentas podem ser processadas por meio do grupo de processos em `http://127.0.0.1:8686/`, sem afetar o processamento de outras solicitações pelos demais processos.

Para que o cliente não perceba a diferença entre as portas, é possível adicionar um proxy para a porta 8686 no nginx. Supondo que as solicitações de interface lenta comecem com `/tast`, a configuração completa do nginx seria semelhante ao seguinte exemplo:
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Adicionar um upstream 8686
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # As solicitações começando com /tast vão para a porta 8686, ajuste /tast conforme necessário
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # As demais solicitações vão para a porta 8787 padrão
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
Desse modo, quando o cliente acessar `domínio.com/tast/xxx`, a solicitação será encaminhada para a porta 8686, sem afetar o processamento de solicitações na porta 8787.
