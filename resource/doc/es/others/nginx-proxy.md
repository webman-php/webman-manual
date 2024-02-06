# Proxy de nginx
Cuando webman necesita proporcionar acceso directo a Internet, se recomienda agregar un proxy de nginx antes de webman, con los siguientes beneficios.

- Los recursos estáticos son manejados por nginx, permitiendo que webman se enfoque en el procesamiento lógico del negocio.
- Permite que múltiples webman compartan los puertos 80 y 443, diferenciándose por nombre de dominio para implementar múltiples sitios en una sola máquina servidor.
- Puede coexistir php-fpm con la arquitectura de webman.
- El proxy de nginx permite implementar SSL para https de manera más simple y eficiente.
- Puede filtrar estrictamente algunas solicitudes no válidas desde Internet.

## Ejemplo de proxy de nginx
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name sitio_domain;
  listen 80;
  access_log off;
  root /your/webman/public;

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

Por lo general, con la configuración anterior, los desarrolladores solo necesitan configurar el server_name y root con los valores reales, y no es necesario configurar otros campos.
