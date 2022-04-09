# 自动加载

## PSR-0
webman遵循`PSR-4`自动加载规范。如果你的业务需要加载`PSR-0`规范的代码库，参考以下操作。

- 新建 `extend` 目录用户存放`PSR-0`规范的代码库
- 编辑`composer.json`，在`"autoload"`下增加以下内容

```js
"psr-0" : {
    "": "extend/"
}
```
最终结果类似
![](../../assets/img/psr0.png)

- 执行 `composer dumpautoload`
- 执行 `php start.php restart` 重启webman (注意，必须重启才能生效) 

## 自动加载某个文件
通过配置`config/autoload.php`可以手动加载某个文件，例如：
```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```