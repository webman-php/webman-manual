# Slow Business Processing

Sometimes we need to handle slow business processes. In order to avoid the impact of slow business processes on other request processing in webman, these businesses can use different processing methods depending on the situation.

## Use Message Queue
Refer to [Redis Queue](https://www.workerman.net/plugin/12) and [STOMP Queue](https://www.workerman.net/plugin/13)

### Advantages
Can handle sudden massive business processing requests

### Disadvantages
Cannot directly return results to the client. If result needs to be pushed, it should be combined with other services, for example, using [webman/push](https://www.workerman.net/plugin/2) to push the processing results.

## Add HTTP Port

> **Note**
> This feature requires webman-framework>=1.4

Adding an HTTP port to handle slow requests, these slow requests enter a specific group of processes through this port for processing, and the results are returned directly to the client after processing.

### Advantages
Can directly return data to the client

### Disadvantages
Cannot handle sudden massive requests

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
            'logger' => \support\Log::channel('default'), // Log instance
            'app_path' => app_path(), // Location of the app directory
            'public_path' => public_path() // Location of the public directory
        ]
    ]
];
```

In this way, slow interfaces can go through the group of processes at `http://127.0.0.1:8686/`, without affecting the business processing of other processes.

To make the frontend unaware of the difference in ports, you can add a proxy to port 8686 in nginx. Assuming that the paths for slow interface requests all start with `/tast`, the entire nginx configuration would be similar to the following:
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

  # Requests starting with /tast go to port 8686, please change /tast to your desired prefix according to the actual situation
  location /tast {
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

In this way, when clients access `domain.com/tast/xxx`, it will go through a separate port 8686 for processing, without affecting the processing of requests on port 8787.