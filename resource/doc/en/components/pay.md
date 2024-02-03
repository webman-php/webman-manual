# webman

- [Introduction](#introduction)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Testing](#testing)
- [License](#license)

## Introduction

webman is an efficient PHP framework based on Workerman, which is used to develop high-performance web services. webman is well suited for developing long connection real-time web services, as well as an asynchronous task and message queue system.
It is very lightweight and is easy to use. Furthermore, the performance is excellent.

## Features

- Fully compatible with Workerman, which can handle high concurrency scenarios very well.
- Define the route and handler separately, which is more readable and maintainable.
- Support multi-process mode and Coroutine within a Worker process.
- Provides a variety of in-built components and supports custom components.

## Requirements

- PHP >= 7.1
- Workerman >= 4.0

## Installation

The recommended way to install webman is through Composer.

```sh
composer require workerman/webman -vvv
```

## Usage

Here is a simple example showing how to create a simple HTTP server using webman.

```php
<?php
use Workerman\Worker;
use Webman\Application;

require_once __DIR__.'/vendor/autoload.php';

$worker = new Worker('http://0.0.0.0:8686');

$worker->onMessage = function ($connection, $data) {
    $app = new Application($connection, $data);
    $app->run();
};

Worker::runAll();
```

## Testing

```sh
composer test
```

## License

webman is an open-source framework licensed under the [MIT license](https://opensource.org/licenses/MIT).