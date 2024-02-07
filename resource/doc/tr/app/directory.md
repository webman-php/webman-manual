# Klasör Yapısı

```plaintext
plugin/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    ├── public
    └── api
```

Görüldüğü gibi, bir uygulama eklentisi webman ile aynı klasör yapısına ve yapılandırma dosyalarına sahiptir. Aslında, bir eklenti geliştirme deneyimi, webman normal bir uygulama geliştirmeden neredeyse hiçbir farklılık göstermez.
Eklenti klasörleri ve isimlendirme PSR4 standardını takip eder; bu nedenle, eklentiler plugin klasörü içine yerleştirildiği için, namespace'lerin hepsi plugin ile başlar, örneğin `plugin\foo\app\controller\UserController`.

## api Klasörü Hakkında
Her bir eklentide bir api klasörü bulunur; uygulamanız diğer uygulamaların kullanması için bazı içsel arabirimler sağlıyorsa, bu arabirimleri api klasörüne koymalısınız.
Burada bahsedilen arabirimler, ağ üzerinden yapılan çağrılar değil, fonksiyon çağrısı arabirimleridir.
Örneğin `Mail eklentisi`, `plugin/email/api/Email.php` dosyasında diğer uygulamaların kullanması için `Email::send()` arabirimini sağlar.
Ayrıca, `plugin/email/api/Install.php`, webman-admin eklenti marketinin yükleyici veya kaldırıcı işlemleri çağırması için otomatik olarak oluşturulmuştur.
