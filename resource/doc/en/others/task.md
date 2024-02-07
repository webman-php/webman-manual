# Slow Business Processing

Sometimes we need to handle slow business processes. To avoid slow business processes affecting other request handling in webman, different processing methods can be used for these businesses depending on the situation.

## Using Message Queue
Refer to [Redis Queue](../queue/redis.md) [Stomp Queue](../queue/stomp.md)

### Advantages
Can handle a sudden surge of business processing requests

### Disadvantages
Cannot directly return the results to the client. If pushing the results is needed, it needs to be coordinated with other services, such as using [webman/push](https://www.workerman.net/plugin/2) to push the processing results.

## Adding a new HTTP port

> **Note**
> This feature requires webman-framework>=1.4

Adding an HTTP port to handle slow requests, these slow requests are processed by accessing this port and then directly returning the results to the client.

### Advantages
Can directly return data to the client

### Disadvantages
Cannot handle a sudden surge of requests

### Implementation Steps
Add the following configuration in `config/process.php`.
```php
return [
    // ... Other configurations are omitted here ...

    'task' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Number of processes
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Set request class
            'logger' => \support\Log::channel('default'), // Logger instance
            'app_path' => app_path(), // App directory location
            'public_path' => public_path() // Public directory location
        ]
    ]
];
```

This way, slow interfaces can go through the group of processes at `http://127.0.0.1:8686/` without affecting the business processing of other processes.

To make the front-end unaware of the difference in ports, you can add a proxy to the 8686 port in nginx. Assuming that the paths for the slow interface requests all start with `/task`, the entire nginx configuration would be similar to the following:
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# Add a new upstream for 8686
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # Requests starting with /task go to the 8686 port, please change /task to the desired prefix according to the actual situation
  location /task {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # Other requests go to the original 8787 port
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

This way, when the client accesses `domain.com/task/xxx`, it will go through the separate 8686 port for processing without affecting the processing of requests on port 8787.
