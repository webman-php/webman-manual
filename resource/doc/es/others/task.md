# Manejo de operaciones lentas

A veces necesitamos manejar operaciones lentas para evitar que afecten el procesamiento de otras solicitudes en webman. Estas operaciones pueden utilizar diferentes estrategias de manejo según la situación.

## Uso de colas de mensajes
Consulte la [cola de Redis](../queue/redis.md) [cola de STOMP](../queue/stomp.md)

### Ventajas
Puede manejar solicitudes de procesamiento de operaciones masivas repentinas.

### Desventajas
No puede devolver directamente resultados al cliente. Si necesita enviar resultados, debe estar integrado con otros servicios, como el uso de [webman/push](https://www.workerman.net/plugin/2) para enviar los resultados del procesamiento.

## Agregar un puerto HTTP

> **Nota**
> Esta característica requiere webman-framework>=1.4

La adición de un puerto HTTP para manejar las solicitudes lentas permite que estas solicitudes sean procesadas por un grupo específico de procesos a través de ese puerto, y una vez procesadas, los resultados se devuelven directamente al cliente.

### Ventajas
Puede devolver los datos directamente al cliente.

### Desventajas
No puede manejar solicitudes masivas repentinas.

### Pasos de implementación
Agregue la siguiente configuración en `config/process.php`.
```php
return [
    // ... otras configuraciones omitidas aquí ...

    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Número de procesos
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // configuración de la clase de solicitud
            'logger' => \support\Log::channel('default'), // instancia de registro
            'app_path' => app_path(), // ubicación del directorio app
            'public_path' => public_path() // ubicación del directorio public
        ]
    ]
];
```

De esta manera, las solicitudes lentas pueden ingresar al grupo de procesos a través de `http://127.0.0.1:8686/`, sin afectar el procesamiento de otras operaciones en los demás procesos.

Para que el cliente no perciba la diferencia en los puertos, puede agregar un proxy al puerto 8686 en nginx. Suponiendo que las solicitudes de operaciones lentas comienzan con `/tast`, la configuración completa de nginx sería similar a la siguiente:
``` 
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Agregar un upstream para 8686
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # Las solicitudes que comienzan con /tast van al puerto 8686, cambie /tast según sus necesidades
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Las demás solicitudes van al puerto 8787 original
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

De esta manera, cuando el cliente acceda a `dominio.com/tast/xxx`, será dirigido al puerto 8686 para su procesamiento, sin afectar el procesamiento de solicitudes en el puerto 8787.
