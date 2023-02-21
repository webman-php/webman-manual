# 配置文件

## 位置
webman的配置文件在`config/`目录下，项目中可以通过`config()`函数来获取对应的配置。

## 获取配置

获取所有配置
```php
config();
```

获取`config/app.php`里的所有配置
```php
config('app');
```

获取`config/app.php`里的`debug`配置
```php
config('app.debug');
```

如果配置是数组，可以通过`.`来获取数组内部元素的值，例如
```php
config('file.key1.key2');
```

## 默认值
```php
config($key, $default);
```
config通过第二个参数传递默认值，如果配置不存在则返回默认值。
配置不存在且没有设置默认值则返回null。


## 自定义配置
开发者可以在`config/`目录下添加自己的配置文件，例如

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**获取配置时使用**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## 更改配置
webman不支持动态修改配置，所有配置必须手动修改对应的配置文件，并reload或restart重启

> **注意**
> 服务器配置`config/server.php`以及进程配置`config/process.php`不支持reload，需要restart重启才能生效