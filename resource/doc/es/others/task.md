# Procesamiento de tareas lentas

A veces necesitamos manejar tareas lentas para evitar que afecten el procesamiento de otras solicitudes en webman. Dependiendo de la situación, estas tareas pueden ser manejadas utilizando diferentes enfoques.

## Uso de colas de mensajes
Consulte [cola de Redis](https://www.workerman.net/plugin/12) [cola de Stomp](https://www.workerman.net/plugin/13)

### Ventajas
Puede hacer frente a solicitudes repentinas de procesamiento masivo.

### Desventajas
No se pueden devolver resultados directamente al cliente. Si se requiere la entrega de resultados, es necesario coordinar con otros servicios, por ejemplo, utilizando [webman/push](https://www.workerman.net/plugin/2) para enviar los resultados del procesamiento.

## Adición de puerto HTTP

> **Nota**
> Esta característica requiere webman-framework>=1.4

Se agrega un puerto HTTP para manejar solicitudes lentas. Estas solicitudes lentas serán dirigidas a un conjunto específico de procesos a través de este puerto, y una vez procesadas, se devolverán directamente al cliente.

### Ventajas
Permite devolver datos directamente al cliente.

### Desventajas
No puede hacer frente a solicitudes repentinas de procesamiento masivo.

### Pasos de implementación
Agregue la siguiente configuración en `config/process.php`.
```php
return [
    // ... otras configuraciones se omiten aquí ...
    
    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // número de procesos
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

De esta manera, las interfaces lentas pueden usar el grupo de procesos en `http://127.0.0.1:8686/`, sin afectar el procesamiento de otras interfaces.

Para que el cliente no note la diferencia en los puertos, puede agregar un proxy al puerto 8686 en nginx. Suponiendo que las solicitudes de interfaz lenta comienzan con `/tast`, la configuración completa de nginx sería similar a la siguiente:
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Agregar un upstream 8686
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # Las solicitudes que comienzan con /tast van al puerto 8686, ajuste /tast según sea necesario
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Las demás solicitudes utilizan el puerto original 8787
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

De esta manera, cuando el cliente accede a `dominio.com/tast/xxx`, será manejado por el puerto 8686 de manera independiente, sin afectar el procesamiento de solicitudes en el puerto 8787.
