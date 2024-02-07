# Proxy do nginx
Quando o webman precisa fornecer acesso direto à Internet, é recomendável adicionar um proxy nginx na frente do webman, com os seguintes benefícios:

- Os recursos estáticos são tratados pelo nginx, permitindo que o webman se concentre no processamento lógico do negócio.
- Permite que vários webman compartilhem as portas 80 e 443, diferenciando os sites por nomes de domínio e possibilitando a implantação de vários sites em um único servidor.
- Pode coexistir com a estrutura php-fpm e webman.
- O proxy do nginx realiza SSL para implementar https de forma mais simples e eficiente.
- Pode filtrar rigorosamente algumas solicitações externas ilegítimas.

## Exemplo de Proxy do nginx
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name domain_do_site;
  listen 80;
  access_log off;
  root /seu/diretorio/webman/public;

  location ^~ / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $http_host;
      proxy_set_header X-Forwarded-Proto $scheme;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```

Geralmente, com a configuração acima, os desenvolvedores só precisam configurar o server_name e o root com os valores reais, os outros campos não precisam ser configurados.
