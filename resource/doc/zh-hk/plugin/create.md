# 基本插件生成及發布流程

## 原理
1、以跨域插件為例，插件分為三個部分，一個是跨域中間件程式文件，一個是中間件配置文件middleware.php，還有一個是通過命令自动生成的Install.php。
2、我們使用命令將三個文件打包並發布到composer。
3、當用戶使用composer安裝跨域插件時，插件中的Install.php會將跨域中間件程式文件以及配置文件拷貝到`{主項目}/config/plugin`下，讓webman加載。實現了跨域中間件文件自動配置生效。
4、當用戶使用composer刪除該插件時，Install.php會刪除相應的跨域中間件程式文件和配置文件，實現插件自動卸載。

## 規範
1、插件名由兩部分組成，`廠商`和`插件名`，例如 `webman/push`，這個與composer包名對應。
2、插件配置文件統一放在 `config/plugin/廠商/插件名/` 下(console命令會自動創建配置目錄)。如果插件不需要配置，則需要刪除自動創建的配置目錄。
3、插件配置目錄僅支持 app.php插件主配置，bootstrap.php 過程啟動配置，route.php 路由配置，middleware.php 中間件配置，process.php 自定義過程配置，database.php數據庫配置，redis.php redis配置，thinkorm.php thinkorm配置。這些配置會自動被webman識別。
4、插件使用以下方法獲取配置`config('plugin.廠商.插件名.配置文件.具體配置項');`，例如`config('plugin.webman.push.app.app_key')`
5、插件如果有自己的數據庫配置，則通過以下方式訪問。`illuminate/database`為`Db::connection('plugin.廠商.插件名.具體的連接')`，`thinkrom`為`Db::connct('plugin.廠商.插件名.具體的連接')`
6、如果插件需要在`app/`目錄下放置業務文件，需要確保不與用戶項目以及其它插件衝突。
7、插件應該盡量避免向主項目拷貝文件或目錄，例如跨域插件除了配置文件需要拷貝到主項目，中間件文件應該放在`vendor/webman/cros/src`下，不必拷貝到主項目。
8、插件命名空間建議使用大寫，例如 Webman/Console。

## 示例

**安裝`webman/console`命令行**

`composer require webman/console`

#### 創建插件

假設創建的插件名字叫 `foo/admin` (名稱也就是後面composer要發布的專案名，名稱需要小寫)
運行命令
`php webman plugin:create --name=foo/admin`

創建插件後會生成目錄 `vendor/foo/admin` 用於存放插件相關文件 和 `config/plugin/foo/admin` 用於存放插件相關配置。

> 注意
> `config/plugin/foo/admin` 支持以下配置，app.php插件主配置，bootstrap.php 過程啟動配置，route.php路由配置，middleware.php中間件配置，process.php自定義過程配置，database.php數據庫配置，redis.php redis配置，thinkorm.php thinkorm配置。配置格式與webman相同，這些配置會自動被webman識別合併到配置當中。
使用時以 `plugin` 為前綴訪問，例如 config('plugin.foo.admin.app');


#### 導出插件

當我們開發完插件後，執行以下命令導出插件
`php webman plugin:export --name=foo/admin`
導出

> 說明
> 導出後會將config/plugin/foo/admin目錄拷貝到vendor/foo/admin/src下，同時自動生成一個Install.php，Install.php用於自動安裝和自動卸載時執行一些操作。
安裝默認操作是將 vendor/foo/admin/src 下的配置拷貝到當前項目config/plugin下
移除時默認操作是將當前項目config/plugin下的配置文件刪除
你可以修改Install.php以便在安裝和卸載插件時做一些自定義操作。

#### 提交插件
* 假設你已經有 [github](https://github.com) 和 [packagist](https://packagist.org) 賬號
* 在[github](https://github.com)上創建一個admin專案並將代碼上傳，專案地址假設是 `https://github.com/你的用户名/admin`
* 進入地址`https://github.com/你的用户名/admin/releases/new`發布一個release如 `v1.0.0`
* 進入[packagist](https://packagist.org)點擊導航裡`Submit`，將你的github專案地址`https://github.com/你的用户名/admin`提交上去這樣就完成了一個插件的發布

> **提示**
> 如果在 `packagist` 里提交插件顯示字衝突，可以重新取一個廠商的名稱，比如`foo/admin`改成`myfoo/admin`

後續當你的插件項目代碼有更新時，需要將代碼同步到github，並再次進入地址`https://github.com/你的用户名/admin/releases/new`重新發布一個release，然後到 `https://packagist.org/packages/foo/admin` 頁面點擊 `Update` 按鈕更新版本
## 給插件添加命令
有時候我哋嘅插件需要一啲自定嘅命令嚟提供啲輔助功能，例如安裝 `webman/redis-queue` 插件之後，專案就會自動增加一個 `redis-queue:consumer` 命令，用戶只需要執行 `php webman redis-queue:consumer send-mail` 就會喺專案裏生成一個SendMail.php嘅消費者類，咁樣有助於快速開發。

假設 `foo/admin` 插件需要加入 `foo-admin:add` 命令，參考以下步驟。

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
    protected static $defaultDescription = '這裡是命令行描述';

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
> 為咗避免插件間命令衝突，命令行格式建議為 `廠商-插件名:具體命令`，例如 `foo/admin` 插件嘅所有命令都應該以 `foo-admin:` 為前綴，例如 `foo-admin:add`。

#### 增加配置
**新建配置 `config/plugin/foo/admin/command.php`**

```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....可以添加多個配置...
];
```

> **提示**
> `command.php` 用嚟俾插件配置自定嘅命令，陣列中每個元素對應一個命令行類文件，每個類文件對應一個命令。當用戶執行命令行時 `webman/console` 會自動加載每個插件 `command.php` 裏設置嘅自定嘅命令。想了解更多命令行相關請參考[命令行](console.md)。

#### 執行導出
執行命令 `php webman plugin:export --name=foo/admin` 導出插件，同時提交到 `packagist`。咁樣用戶安裝`foo/admin`插件之後，就會增加一個 `foo-admin:add` 命令。執行 `php webman foo-admin:add jerry` 就會打印 `Admin add jerry`。
