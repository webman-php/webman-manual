# Temel Eklenti Oluşturma ve Yayınlama Süreci

## İlke
1. CORS eklentisi örneği olarak, eklentiler üç bölüme ayrılır: CORS middleware program dosyası, middleware.php adlı middleware yapılandırma dosyası ve komutla otomatik oluşturulan Install.php.
2. Üç dosyayı bir araya getirip ve composer'a yayınlamak için bir komut kullanırız.
3. Kullanıcı CORS eklentisini composer ile yüklediğinde, Install.php dosyası, CORS middleware program dosyalarını ve yapılandırma dosyalarını `{ana proje}/config/plugin` altına kopyalar ve webman tarafından yüklenmesini sağlar. Otomatik olarak CORS middleware dosyasının yapılandırılmasını sağlar.
4. Kullanıcı eklentiyi composer ile kaldırdığında, Install.php ilgili CORS middleware program dosyalarını ve yapılandırma dosyalarını kaldırarak eklentinin otomatik olarak kaldırılmasını sağlar.

## Standart
1. Eklenti adı, `üretici` ve `eklenti adı` olmak üzere iki kısımdan oluşur, örneğin `webman/push`, bu, composer paket adıyla eşleşir.
2. Eklenti yapılandırma dosyaları genellikle `config/plugin/üretici/eklenti adı/` içine yerleştirilir (console komutları otomatik olarak yapılandırma dizini oluşturacaktır). Eğer eklenti yapılandırmaya ihtiyaç duymuyorsa, otomatik oluşturulan yapılandırma dizinini kaldırmanız gerekir.
3. Eklenti yapılandırma dizini, sadece app.php eklenti ana yapılandırması, bootstrap.php işlem başlatma yapılandırması, route.php yönlendirme yapılandırması, middleware.php middleware yapılandırması, process.php özel işlem yapılandırması, database.php veritabanı yapılandırması, redis.php redis yapılandırması, thinkorm.php thinkorm yapılandırması gibi yapılandırmaları desteklemektedir. Bu yapılandırmalar otomatik olarak webman tarafından tanınır.
4. Eklenti, yapılandırmaya erişmek için `config('plugin.üretici.eklenti adı. yapılandırma dosyası. belirli yapılandırma öğesi');` gibi bir yöntem kullanır, örneğin `config('plugin.webman.push.app.app_key')`
5. Eklentinin kendi veritabanı yapılandırması varsa, aşağıdaki yöntemlerle erişilir: `illuminate/database` için `Db::connection('plugin.üretici.eklenti adı. belirli bağlantı')`, `thinkrom` için `Db::connct('plugin.üretici.eklenti adı. belirli bağlantı')`
6. Eğer eklentinin `app/` dizinine iş dosyaları koyması gerekiyorsa, bu dosyaların ana proje ve diğer eklentilerle çakışmadığından emin olun.
7. Eklentinin mümkün olduğunca ana projeye dosya veya dizin kopyalamaktan kaçınması önerilir, örneğin, CORS eklentisi yapılandırma dosyalarının yanı sıra, middleware dosyaları da ana projeye kopyalanacaksa, bu dosyaların `vendor/webman/cros/src` altında olması ve ana projeye kopyalanmaması gerekir.
8. Eklenti ad alanı büyük harf kullanılması önerilir, örneğin Webman/Console.

## Örnek

**`webman/console` komut satırı yükleme**

`composer require webman/console`

#### Eklenti Oluştur

Örneğin, oluşturulan eklenti adı `foo/admin` (ad aynı zamanda sonraki olarak yayımlanacak proje adıdır, ad küçük harfle yazılmalıdır)
Aşağıdaki komutu çalıştırın
`php webman plugin:create --name=foo/admin`

Eklenti oluşturulduktan sonra `vendor/foo/admin` (eklentiyle ilgili dosyaları depolamak için) ve `config/plugin/foo/admin` (eklentiyle ilgili yapılandırmaları depolamak için) dizinlerini oluşturulur.

> Not
> `config/plugin/foo/admin` şu yapılandırmaları destekler: app.php eklenti ana yapılandırması, bootstrap.php işlem başlatma yapılandırması, route.php yönlendirme yapılandırması, middleware.php middleware yapılandırması, process.php özel işlem yapılandırması, database.php veritabanı yapılandırması, redis.php redis yapılandırması, thinkorm.php thinkorm yapılandırması. Yapılandırma biçimi webman ile aynıdır ve bu yapılandırmalar otomatik olarak webman tarafından tanınarak yapılandırma içine birleştirilir.
`plugin` önekiyle erişilir, örneğin config('plugin.foo.admin.app');

#### Eklenti Dışa Aktar

Eklenti geliştirildikten sonra, aşağıdaki komutla eklenti dışa aktarılır
`php webman plugin:export --name=foo/admin`

> Açıklama
Dışa aktarıldıktan sonra, config/plugin/foo/admin dizini vendor/foo/admin/src dizinine kopyalanır ve aynı zamanda otomatik olarak bir Install.php oluşturulur; Install.php, otomatik yükleme ve kaldırma sırasında bazı işlemleri gerçekleştirmek üzere kullanılır.
Varsayılan yükleme işlemi, vendor/foo/admin/src dizinindeki yapılandırmaları mevcut projenin config/plugin altına kopyalamaktır.
Kaldırma işlemi varsayılan olarak mevcut projenin config/plugin dizinindeki yapılandırma dosyalarını siler.
Install.php dosyasını, kurulum ve kaldırma sırasında özelleştirilmiş işlemler yapmak için değiştirebilirsiniz.

#### Eklenti Gönder
* Varsayalım ki [github](https://github.com) ve [packagist](https://packagist.org) hesabınız var.
* [github](https://github.com)'da bir admin projesi oluşturun ve kodları yükleyin, projenin adresi varsayılan olarak `https://github.com/yourusername/admin` olsun.
* `https://github.com/yourusername/admin/releases/new` adresine giderek `v1.0.0` gibi bir sürüm yayınlayın.
* [packagist](https://packagist.org)'e gidin ve gezinme çubuğunda `Submit`e tıklayarak github proje adresinizi, `https://github.com/yourusername/admin`, gönderin. Böylece bir eklenti yayınlama işlemi tamamlanmış olur.

> İpucu
> `Packagist`te eklenti gönderirken çakışma hatası görünürse, başka bir üretici adı seçebilirsiniz. Örneğin, `foo/admin` yerine `myfoo/admin`.

Eklenti proje kodunuzu güncellediğinizde, kodları github'a senkronize etmeniz ve tekrar `https://github.com/yourusername/admin/releases/new` adresine giderek bir sürüm yayınlamanız ve ardından `https://packagist.org/packages/foo/admin` sayfasında `Update` düğmesine tıklamanız gerekecektir.
## Eklentiye Komut Ekleme
Bazı durumlarda eklentilerimiz, bazı yardımcı işlevleri sağlamak için özel komutlara ihtiyaç duyabilir. Örneğin, `webman/redis-queue` eklentisini yükledikten sonra, projeye otomatik olarak `redis-queue:consumer` komutu eklenir. Kullanıcılar sadece `php webman redis-queue:consumer send-mail` komutunu çalıştırdıklarında, projede SendMail.php adında bir tüketici sınıfı oluşturulacaktır. Bu, hızlı bir şekilde geliştirme yapmaya yardımcı olur.

Örneğin `foo/admin` eklentisi, `foo-admin:add` komutunu eklemesi gerektiğini varsayalım. Aşağıdaki adımları takip edin.

#### Komut Oluşturma

**Komut dosyasını oluşturun `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = 'Bu komutun tanımı burada yer alır';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Eklenecek isim');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Admin ekle $name");
        return self::SUCCESS;
    }

}
```

> **Not**
> Eklentiler arasında komut çakışmasını önlemek için komut satırı formatı önerilir `üretici-eklentiadı:belirli komut` örneğin `foo/admin` eklentisi için tüm komutlarının öneki olarak `foo-admin:` olmalıdır, örneğin `foo-admin:add`.

#### Yapılandırma Ekleme
**Yapılandırma ekleyin `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....birden fazla yapılandırma eklenebilir...
];
```

> **İpucu**
> `command.php` eklentiye özel komutlar eklemek için kullanılır, dizide her bir öğe bir komut dosyasını temsil eder, her bir dosya bir komut ile ilişkilidir. Kullanıcı komut satırını çalıştırdığında, `webman/console` otomatik olarak her eklentinin `command.php` dosyasında belirtilen özel komutları yükler. Daha fazla komut satırıyla ilgili bilgi için [Komut Satırı](console.md) sayfasına bakabilirsiniz.

#### Dışa Aktarma İşlemini Gerçekleştirme
`php webman plugin:export --name=foo/admin` komutunu çalıştırarak, eklentiyi dışa aktarın ve `packagist`'e gönderin. Kullanıcılar `foo/admin` eklentisini yükledikten sonra, `foo-admin:add` komutu otomatik olarak eklenir. `php webman foo-admin:add jerry` komutunun çalıştırılması durumunda `Admin ekle jerry` yazdırılır.
