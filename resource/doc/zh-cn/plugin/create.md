# 基础插件生成及发布流程

## 原理
1、以跨域插件为例，插件分为三部分，一个是跨域中间件程序文件，一个是中间件配置文件middleware.php，还有一个是通过命令自动生成的Install.php。
2、我们使用命令将三个文件打包并发布到composer。
3、当用户使用composer安装跨域插件时，插件中的Install.php会将跨域中间件程序文件以及配置文件拷贝到`{主项目}/config/plugin`下，让webman加载。实现了跨域中间件文件自动配置生效。
4、当用户使用composer删除该插件时，Install.php会删除相应的跨域中间件程序文件和配置文件，实现插件自动卸载。

## 规范
1、插件名由两部分组成，`厂商`和`插件名`，例如 `webman/push`，这个与composer包名对应。
2、插件配置文件统一放在 `config/plugin/厂商/插件名/` 下(console命令会自动创建配置目录)。如果插件不需要配置，则需要删除自动创建的配置目录。
3、插件配置目录仅支持 app.php插件主配置，bootstrap.php 进程启动配置，route.php 路由配置，middleware.php 中间件配置，process.php 自定义进程配置，database.php数据库配置，redis.php redis配置，thinkorm.php thinkorm配置。这些配置会自动被webman识别。
4、插件使用以下方法获取配置`config('plugin.厂商.插件名.配置文件.具体配置项');`，例如`config('plugin.webman.push.app.app_key')`
5、插件如果有自己的数据库配置，则通过以下方式访问。`illuminate/database`为`Db::connection('plugin.厂商.插件名.具体的连接')`，`thinkrom`为`Db::connct('plugin.厂商.插件名.具体的连接')`
6、如果插件需要在`app/`目录下放置业务文件，需要确保不与用户项目以及其它插件冲突。
7、插件应该尽量避免向主项目拷贝文件或目录，例如跨域插件除了配置文件需要拷贝到主项目，中间件文件应该放在`vendor/webman/cros/src`下，不必拷贝到主项目。
8、插件命名空间建议使用大写，例如 Webman/Console。

## 示例

**安装`webman/console`命令行**

`composer require webman/console`

#### 创建插件

假设创建的插件名字叫 `foo/admin` (名称也就是后面composer要发布的项目名，名称需要小写)
运行命令
`php webman plugin:create --name=foo/admin`

创建插件后会生成目录 `vendor/foo/admin` 用于存放插件相关文件 和 `config/plugin/foo/admin` 用于存放插件相关配置。

> 注意
> `config/plugin/foo/admin` 支持以下配置，app.php插件主配置，bootstrap.php 进程启动配置，route.php 路由配置，middleware.php 中间件配置，process.php 自定义进程配置，database.php数据库配置，redis.php redis配置，thinkorm.php thinkorm配置。配置格式与webman相同，这些配置会自动被webman识别合并到配置当中。
使用时以 `plugin` 为前缀访问，例如 config('plugin.foo.admin.app');


#### 导出插件

当我们开发完插件后，执行以下命令导出插件
`php webman plugin:export --name=foo/admin`
导出

> 说明
> 导出后会将config/plugin/foo/admin目录拷贝到vendor/foo/admin/src下，同时自动生成一个Install.php，Install.php用于自动安装和自动卸载时执行一些操作。
> 安装默认操作是将 vendor/foo/admin/src 下的配置拷贝到当前项目config/plugin下
> 移除时默认操作是将 当前项目config/plugin 下的配置文件删除
> 你可以修改Install.php以便在安装和卸载插件时做一些自定义操作。

#### 提交插件
* 假设你已经有 [github](https://github.com) 和 [packagist](https://packagist.org) 账号
* 在[github](https://github.com)上创建一个admin项目并将代码上传，项目地址假设是 `https://github.com/你的用户名/admin`
* 进入地址`https://github.com/你的用户名/admin/releases/new`发布一个release如 `v1.0.0`
* 进入[packagist](https://packagist.org)点击导航里`Submit`，将你的github项目地址`https://github.com/你的用户名/admin`提交上去这样就完成了一个插件的发布

> **提示**
> 如果在 `packagist` 里提交插件显示字冲突，可以重新取一个厂商的名字，比如`foo/admin`改成`myfoo/admin`

后续当你的插件项目代码有更新时，需要将代码同步到github，并再次进入地址`https://github.com/你的用户名/admin/releases/new`重新发布一个release，然后到 `https://packagist.org/packages/foo/admin` 页面点击 `Update` 按钮更新版本

## 给插件添加命令
有时候我们的插件需要一些自定义命令来提供一些辅助功能，例如安装 `webman/redis-queue`插件后，项目将会自动增加一个`redis-queue:consumer`命令，用户只要运行 `php webman redis-queue:consumer send-mail` 就会在项目里生成一个SendMail.php的消费者类，这样有助于快速开发。

假设`foo/admin`插件需要添加`foo-admin:add`命令，参考如下步骤。 

#### 新建命令

**新建命令文件 `vendor/foo/admin/src/FooAdminAddCommand.php`**

```php
<?php

namespace Foo\Admin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command
{
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = '这里是命令行描述';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Add name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Admin add $name");
        return self::SUCCESS;
    }

}
```

> **注意**
> 为了避免插件间命令冲突，命令行格式建议为 `厂商-插件名:具体命令`，例如 `foo/admin` 插件的所有命令都应该以 `foo-admin:` 为前缀，例如 `foo-admin:add`。

#### 增加配置
**新建配置 `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....可以添加多个配置...
];
```

> **提示**
> `command.php` 用于给插件配置自定义命令，数组中每个元素对应一个命令行类文件，每个类文件对应一个命令。当用户运行命令行时`webman/console`会自动加载每个插件`command.php`里设置的自定义命令。 想了解更多命令行相关请参考[命令行](console.md)

#### 执行导出
执行命令 `php webman plugin:export --name=foo/admin` 导出插件，并提交到`packagist`。这样用户安装 `foo/admin` 插件后，就会增加一个 `foo-admin:add` 命令。执行 `php webman foo-admin:add jerry` 将打印 `Admin add jerry`
