# 正确使用事务
webman使用数据库事务与其它框架是一样的，这里列举出需要注意的地方

## 代码结构

代码结构和其它框架是一样的，例如Laravel用法(think-orm类似)
```php
Db::beginTransaction();
try {
    // ..业务处理略...
    
    Db::commit();
} catch (\Throwable $exception) {
    Db::rollBack();
}
```

这里特别需要注意的是**必须使用**`\Throwable`而**不能使用**`\Exception`，因为业务处理过程中可能触发`Error`，它并不属于`Exception`

## 数据库连接

当事务中操作模型时，特别需要注意模型是否设置了连接。如果模型设置了连接开启事务的时候要指定连接，否则事务无效（think-orm类似）。例如

```php
<?php

namespace app\model;
use support\Model;

class User extends Model
{

    // 这里给模型指定了连接
    protected $connection = 'mysql';

    protected $table = 'users';

    protected $primaryKey = 'id';

}
```

当模型指定了连接时，开启事务、提交事务、回滚事务必须指定连接

```php
Db::connection('mysql')->beginTransaction();
try {
    // 业务处理
    $user = new User;
    $user->name = 'webman';
    $user->save();
    Db::connection('mysql')->commit();
} catch (\Throwable $exception) {
    Db::connection('mysql')->rollBack();
}
```

## 查找未提交事务的请求
有时候业务代码bug导致某个请求事务没有提交，为了快速定位哪个控制器方法未提交事务，可以安装`webman/log`组件，该组件在请求完毕后会自动检查是否有未提交的事务，并记录日志，日志关键字为`Uncommitted transactions`

**webman/log安装方法**

`composer require webman/log`

> **注意**
> 安装后需要restart重启，reload不生效
