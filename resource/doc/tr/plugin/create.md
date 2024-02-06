# Temel Eklenti Oluşturma ve Yayınlama Süreci

## Prensip
1. Çapraz geçiş eklentisi örneği olarak, eklenti üç bölüme ayrılır: Birincisi, çapraz geçiş orta yazılım dosyası, ikincisi, middleware.php adlı orta yazılım yapılandırma dosyası ve üçüncüsü, komut kullanılarak otomatik olarak oluşturulan Install.php dosyası.
2. Üç dosyayı birleştirip ve onları composer'a yayınlamak için bir komut kullanıyoruz.
3. Kullanıcı, çapraz geçiş eklentisini composer ile yüklediğinde, Install.php dosyası çapraz geçiş orta yazılım dosyasını ve yapılandırma dosyasını `{ana proje}/config/plugin` klasörüne kopyalar ve webman tarafından yüklenmesini sağlar. Bu, çapraz geçiş orta yazılım dosyasının otomatik yapılandırma ile etkin hale gelmesini sağlar.
4. Kullanıcı, bu eklentiyi composer ile kaldırdığında, Install.php dosyası ilgili çapraz geçiş orta yazılım dosyasını ve yapılandırma dosyasını siler ve eklentinin otomatik olarak kaldırılmasını sağlar.

## Standart
1. Eklenti adı, `Üretici` ve `Eklenti Adı` olmak üzere iki bölümden oluşur, örneğin `webman/push`, bu, composer paket adıyla eşleşir.
2. Eklenti yapılandırma dosyaları genellikle `config/plugin/Üretici/Eklenti Adı/` klasörüne yerleştirilir (console komutu otomatik olarak yapılandırma dizinini oluşturur). Eğer eklenti yapılandırmaya ihtiyaç duymuyorsa, otomatik olarak oluşturulan yapılandırma klasörünü silmek gerekir.
3. Eklenti yapılandırma klasörü yalnızca app.php (eklenti ana yapılandırma), bootstrap.php (işlem başlatma yapılandırması), route.php (rota yapılandırması), middleware.php (orta yazılım yapılandırması), process.php (özel işlem yapılandırması), database.php (veritabanı yapılandırması), redis.php (redis yapılandırması) ve thinkorm.php (thinkorm yapılandırması) dosyalarını destekler. Bu yapılandırmalar otomatik olarak webman tarafından tanınır.
4. Eklenti yapılandırmasına aşağıdaki yöntemlerle erişilir: `config('plugin.Üretici.Eklenti Adı.ayar dosyası.belirli yapılandırma öğesi');` örneğin `config('plugin.webman.push.app.app_key')`
5. Eklentinin kendi veritabanı yapılandırması varsa, buna aşağıdaki şekilde erişilir: `illuminate/database` `Db::connection('eklenti.Üretici.Eklenti Adı.belirli bağlantı')`, `thinkrom` için `Db::connct('eklenti.Üretici.Eklenti Adı.belirli bağlantı')`
6. Eğer eklenti, `app/` klasörüne iş dosyası yerleştirmek istiyorsa, bu dosyaların kullanıcı projesi veya diğer eklentilerle çakışmadığından emin olmalıdır.
7. Eklentiler, ana projeye dosya veya klasör kopyalamaktan kaçınmalıdır. Örneğin, çapraz geçiş eklentisi, yapılandırma dosyasının yanı sıra middleware dosyasının da ana projeye kopyalanmasına gerek olmadan `vendor/webman/cros/src` klasörüne konulmalıdır.
8. Eklenti ad alanları büyük harf önerilir, örneğin Webman/Console.

## Örnek

**'webman/console' komut satırı kurulumu**

`composer require webman/console`

#### Eklenti Oluşturma

'Eğer oluşturulmak istenen eklentinin adı 'foo/admin' ise (ad ayrıca yayınlama projesi adı olmalıdır, ad küçük harf olmalıdır),
Aşağıdaki komutu çalıştırın:
`php webman plugin:create --name=foo/admin`

Eklenti oluşturulduktan sonra `vendor/foo/admin` klasörü, eklentiye ait dosyaların saklandığı yerdir ve `config/plugin/foo/admin` klasörü de eklenti yapılandırma dosyalarının saklandığı yerdir.

> Not
> `config/plugin/foo/admin` app.php (eklenti ana yapılandırma), bootstrap.php (işlem başlatma yapılandırması), route.php (rota yapılandırması), middleware.php (orta yazılım yapılandırması), process.php (özel işlem yapılandırması), database.php (veritabanı yapılandırması), redis.php (redis yapılandırması) ve thinkorm.php (thinkorm yapılandırması) yapılandırmalarını destekler. Bu yapılandırmalar webman tarafından otomatik olarak tanınır ve genel yapılandırmaya birleştirilir.

> Eklenti oluşturulduktan sonra, `config/plugin/foo/admin` klasörüne yukarıdaki yapılandırmalardan birini içeren 'app.php', 'middleware.php' vb. dosyalar eklenmelidir. Bu yapılandırmalar, eklenti yapılandırmasında otomatik olarak görüntülenir ve webman tarafından tanınan yapılandırma dosyalarına dahil edilir; bu da, yapılandırmaların webman içinde otomatik olarak birleşmesini sağlar.

#### Eklentiyi İhracat Etme

Eklenti geliştirme tamamlandıktan sonra, aşağıdaki komutu çalıştırarak eklentiyi ihracat edin:
`php webman plugin:export --name=foo/admin`

> Açıklama
> İhracat işlemi sonrasında, config/plugin/foo/admin klasörü vendor/foo/admin/src altına kopyalanır ve otomatik olarak Install.php dosyası oluşturulur. Install.php, eklenti otomatik kurulum ve kaldırma işlemlerinde özel işlemler yürütmek için kullanılır.
> Kurulumun varsayılan işlemi, vendor/foo/admin/src altındaki yapılandırmaların mevcut projenin config/plugin klasörüne kopyalanmasıdır.
> Kaldırmak için varsayılan işlem, mevcut projenin config/plugin klasöründeki yapılandırma dosyalarının silinmesidir.
> Özel işlemler yapmak için Install.php dosyasını özelleştirebilirsiniz.

#### Eklenti Gönderimi
* Varsayalım ki, zaten bir [github](https://github.com) ve [packagist](https://packagist.org) hesabınız var
* [github](https://github.com) üzerinde bir admin projesi oluşturun ve kodları yükleyin, projenin adresini varsayalım `https://github.com/yourusername/admin`
* `https://github.com/yourusername/admin/releases/new` adresine girin ve `v1.0.0` gibi bir sürüm yayınlayın
* [packagist](https://packagist.org) üzerinde 'Submit' düğmesine tıklayın, github proje adresinizi `https://github.com/yourusername/admin` adresini gönderin ve bu şekilde eklentinin yayına alım işlemini tamamlamış olacaksınız

> **İpucu**
> Eğer 'packagist' üzerinde eklenti gönderirken çakışma hatası alırsanız, başka bir üretici adı kullanabilirsiniz, örneğin `foo/admin` yerine `myfoo/admin`.

Eklenti projesinin kodları güncellendiğinde, kodları github'e senkronize etmeniz ve tekrar `https://github.com/yourusername/admin/releases/new` adresine girerek yeni bir sürüm yayınlamanız, ardından `https://packagist.org/packages/foo/admin` sayfasına gelerek `Güncelleme` butonuna tıklayarak sürümü güncellemeniz gerekmektedir.

## Eklentiye Komut Ekleme
Bazı durumlarda, eklentimiz özel işlevler sağlamak için özel komutlara ihtiyaç duyar, örneğin, `webman/redis-queue` eklentisi yükledikten sonra, projeye otomatik olarak `redis-queue:consumer` komutu eklenecektir. Kullanıcı, `php webman redis-queue:consumer send-mail` komutunu çalıştırarak projeye SendMail.php adında bir tüketici sınıfı oluşturabilir, bu hızlı gelişimi destekler.

'foo/admin' eklentisinin `foo-admin:add` komutu eklemesi gerektiğini varsayalım, aşağıdaki adımları inceleyin.

#### Komut Oluşturma

**Yeni komut dosyası oluşturma `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = 'Bu, komut hakkında bir açıklamadır';

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
        $output->writeln("Admin eklendi $name");
        return self::SUCCESS;
    }

}
```

> **Not**
> Eklentiler arası komut çakışmasını önlemek için, komut biçiminin `Üretici-Eklenti Adı:Özel Komut` şeklinde olması önerilir, örneğin, `foo/admin` eklentisinin tüm komutları `foo-admin:` ön ekiyle başlamalıdır, örneğin, `foo-admin:add`.

#### Yapılandırma Ekleme
**Yeni yapılandırma ekle `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....birden fazla yapılandırma eklenebilir....
];
```

> **İpucu**
> `command.php` özelleştirilmiş komutlar için eklenti yapılandırmasıdır, her bir öğe bir komut dosyasına karşılık gelir, her bir dosya da bir komuta karşılık gelir. Kullanıcı komut satırı çalıştırdığında, `webman/console` otomatik olarak her eklentinin `command.php` dosyasında tanımlanan özel komutlarını yükler. Daha fazla bilgi için [Command Line](console.md)'a bakabilirsiniz.

#### İhracatı Gerçekleştirme
`php webman plugin:export --name=foo/admin` komutunu çalıştırarak eklentinizi ihracat edin ve packagist'e gönderin. Bu sayede, `foo/admin` eklentisini yükleyen kullanıcılar `foo-admin:add` komutu ekleyecektir. `php webman foo-admin:add jerry` komutunu çalıştırarak `Admin eklendi jerry` yazısının çıktısını alabilirsiniz.
