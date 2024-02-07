# Proxy de nginx
Cuando webman necesita proporcionar acceso directo a Internet, se recomienda agregar un proxy de nginx delante de webman, con los siguientes beneficios:

- Los recursos estáticos son manejados por nginx, lo que permite que webman se enfoque en el procesamiento lógico del negocio.
- Permite que múltiples webman compartan los puertos 80 y 443, diferenciándolos a través del nombre de dominio, lo que permite implementar múltiples sitios en un solo servidor.
- Puede coexistir con la arquitectura php-fpm y webman.
- El proxy de nginx puede implementar SSL para HTTPS, siendo más simple y eficiente.
- Puede filtrar estrictamente algunas solicitudes externas no válidas.

## Ejemplo de proxy de nginx
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

server {
  server_name sitio_dominio;
  listen 80;
  access_log off;
  root /tu/ruta/webman/public;

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

Por lo general, con la configuración anterior, los desarrolladores solo necesitan configurar el server_name y root con los valores reales; los demás campos no necesitan ser configurados.
