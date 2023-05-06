# 应用插件开发规范

## 应用插件要求
* 插件不能包含侵权的代码、图标、图片等
* 插件源码保证是完整的代码，且不能加密
* 插件必须是完整的功能，不能是简单的功能
* 必须提供完整的功能介绍、文档
* 插件不能包含子市场
* 插件内不能任何文字或推广连接

## 应用插件标识
每个应用插件都有一个唯一标识，这个标识由字母组成。这个标识影响应用插件所在源码目录名、类的命名空间、插件数据库表前缀。

假设开发者以foo为插件标识，那么插件源码所在目录为`{主项目}/plugin/foo`，相应的插件的命名空间为`plugin\foo`，表前缀为`foo_`。

由于标识全网唯一，所以开发者在开发前需要检测标识是否可用，检测地址[应用标识检测](https://www.workerman.net/app/check)。

## 数据库
* 表名由小写字母`a-z`以及下划线`_`组成
* 插件数据表应该以插件标识为前缀，例如foo插件article表为`foo_article`
* 表主键应该id为索引
* 存储引擎统一使用innodb引擎
* 字符集统一使用utf8mb4_general_ci
* 数据库ORM使用laravel或者think-orm都可以
* 时间字段建议使用DateTime

## 代码规范

#### PSR规范
代码应符合PSR4加载规范

#### 类的命名为大写开头的驼峰式
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### 类的属性及方法以小写开头驼峰式
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * 不需要鉴权的方法
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * 获得评论
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### 注释
类的属性以及函数必须包含注释，包括概述、参数、返回类型

#### 缩进
代码应该使用4个空格符来缩进，而不是使用制表符

#### 流程控制
流程控制关键字(if for while foreach等)后面紧跟一个空格，流程控制代码开始花括号应该与结束圆括号在同一行。
```php
foreach ($users as $uid => $user) {

}
```

#### 临时变量名
建议以小写开头驼峰式命名(不强制)

```php
$articleCount = 100;
```

