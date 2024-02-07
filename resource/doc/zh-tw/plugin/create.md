# 基礎外掛生成及發佈流程

## 原理
1、以跨域外掛為例，外掛分為三個部分，一個是跨域中介軟體程式檔，一個是中介軟體配置檔middleware.php，還有一個是通過命令自動生成的Install.php。
2、我們使用命令將三個檔案打包並發佈到composer。
3、當使用者使用composer安裝跨域外掛時，外掛中的Install.php會將跨域中介軟體程式檔以及配置檔拷貝到`{主專案}/config/plugin`下，讓webman加載。實現了跨域中介軟體檔自動配置生效。
4、當使用者使用composer刪除該外掛時，Install.php會刪除相應的跨域中介軟體程式檔和配置檔，實現外掛自動卸載。

## 規範
1、外掛名由兩部分組成，`廠商`和`外掛名`，例如 `webman/push`，這個與composer包名對應。
2、外掛配置檔統一放在 `config/plugin/廠商/外掛名/` 下(console命令會自動創建配置目錄)。如果外掛不需要配置，則需要刪除自動創建的配置目錄。
3、外掛配置目錄僅支持 app.php外掛主配置，bootstrap.php進程啟動配置，route.php路由配置，middleware.php中介軟體配置，process.php自定義進程配置，database.php資料庫配置，redis.php redis配置，thinkorm.php thinkorm配置。這些配置會自動被webman識別。
4、外掛使用以下方法獲取配置`config('plugin.廠商.外掛名.配置檔.具體配置項');`，例如`config('plugin.webman.push.app.app_key')`
5、外掛如果有自己的資料庫配置，則通過以下方式訪問。`illuminate/database`為`Db::connection('plugin.廠商.外掛名.具體的連線')`，`thinkrom`為`Db::connct('plugin.廠商.外掛名.具體的連線')`
6、如果外掛需要在`app/`目錄下放置業務檔案，需要確保不與使用者專案以及其他外掛衝突。
7、外掛應該盡量避免向主專案拷貝檔案或目錄，例如跨域外掛除了配置檔需要拷貝到主專案，中介軟體檔案應該放在`vendor/webman/cros/src`下，不必拷貝到主專案。
8、外掛命名空間建議使用大寫，例如 Webman/Console。

## 範例

**安裝`webman/console`命令行**

`composer require webman/console`

#### 創建外掛

假設創建的外掛名字叫 `foo/admin` (名稱也就是後面composer要發佈的專案名，名稱需要小寫)
運行命令
`php webman plugin:create --name=foo/admin`

創建外掛後會生成目錄 `vendor/foo/admin` 用於存放外掛相關檔案 和 `config/plugin/foo/admin` 用於存放外掛相關配置。

> 注意
> `config/plugin/foo/admin` 支持以下配置，app.php外掛主配置，bootstrap.php進程啟動配置，route.php路由配置，middleware.php中介軟體配置，process.php自定義進程配置，database.php資料庫配置，redis.php redis配置，thinkorm.php thinkorm配置。配置格式與webman相同，這些配置會自動被webman識別合併到配置當中。
使用時以 `plugin` 為前綴訪問，例如 config('plugin.foo.admin.app');


#### 導出外掛

當我們開發完外掛後，執行以下命令導出外掛
`php webman plugin:export --name=foo/admin`
導出

> 說明
> 導出後會將config/plugin/foo/admin目錄拷貝到vendor/foo/admin/src下，同時自動生成一個Install.php，Install.php用於自動安裝和自動卸載時執行一些操作。
> 安裝預設操作是將 vendor/foo/admin/src 下的配置拷貝到當前專案config/plugin下
> 移除時預設操作是將 當前專案config/plugin 下的配置檔案刪除
> 你可以修改Install.php以便在安裝和卸載外掛時做一些自定義操作。

#### 提交外掛
* 假設你已經有 [github](https://github.com) 和 [packagist](https://packagist.org) 帳號
* 在[github](https://github.com)上創建一個admin專案並將代碼上傳，專案地址假設是 `https://github.com/你的用戶名/admin`
* 進入地址`https://github.com/你的用戶名/admin/releases/new`發佈一個release如 `v1.0.0`
* 進入[packagist](https://packagist.org)點擊導航裡`Submit`，將你的github專案地址`https://github.com/你的用戶名/admin`提交上去這樣就完成了一個外掛的發佈

> **提示**
> 如果在 `packagist` 里提交外掛顯示字衝突，可以重新取一個廠商的名字，比如`foo/admin`改成`myfoo/admin`

後續當你的外掛專案代碼有更新時，需要將代碼同步到github，並再次進入地址`https://github.com/你的用戶名/admin/releases/new`重新發佈一個release，然後到 `https://packagist.org/packages/foo/admin` 頁面點擊 `Update` 按鈕更新版本
## 給插件新增命令
有時候我們的插件需要一些自定義命令來提供一些輔助功能，例如安裝 `webman/redis-queue` 插件後，專案將會自動增加一個 `redis-queue:consumer` 命令，用戶只要運行 `php webman redis-queue:consumer send-mail` 就會在專案裡生成一個SendMail.php的消費者類，這樣有助於快速開發。

假設 `foo/admin` 插件需要新增 `foo-admin:add` 命令，參考如下步驟。

#### 新建命令

**新建命令檔案 `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = '這裡是命令列描述';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, '新增名稱');
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
> 為了避免插件間命令衝突，命令列格式建議為 `廠商-插件名:具體命令`，例如 `foo/admin` 插件的所有命令都應該以 `foo-admin:` 為前綴，例如 `foo-admin:add`。

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
> `command.php` 用於給插件配置自定義命令，陣列中每個元素對應一個命令列類別檔案，每個類檔對應一個命令。當用戶運行命令列時`webman/console`會自動加載每個插件`command.php`裡設定的自定義命令。 想了解更多命令列相關請參考[命令列](console.md)

#### 執行匯出
執行命令 `php webman plugin:export --name=foo/admin` 匯出插件，並提交到`packagist`。這樣用戶安裝 `foo/admin` 插件後，就會增加一個 `foo-admin:add` 命令。執行 `php webman foo-admin:add jerry` 將會列印 `Admin add jerry`
