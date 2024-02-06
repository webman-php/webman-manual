# Klasör Yapısı

```
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

Biz, bir uygulama eklentisinin, webman ile aynı dizin yapısına ve yapılandırma dosyalarına sahip olduğunu görüyoruz. Aslında, eklenti geliştirme deneyimi, webman normal uygulama geliştirmeyle hemen hemen aynıdır.
Eklenti dizini ve adlandırma PSR4 standartlarına uygundur. Eklentiler plugin dizini altında bulunduğundan, namespace'ler plugin ile başlar, örneğin `plugin\foo\app\controller\UserController`.

## api Dizini Hakkında
Her eklentide bir api dizini bulunur. Eğer uygulamanız diğer uygulamaların çağrılmasına izin vermek için içsel arayüzler sağlıyorsa, bu arayüzleri api dizinine koymalısınız.
Burada bahsedilen arayüzler, ağ aramaları değil, fonksiyon çağrılarıdır.
Örneğin, `Email eklentisi`, `plugin/email/api/Email.php` içinde diğer uygulamaların e-posta göndermek için çagırabileceği bir `Email::send()` arayüzünü sağlar.
Ayrıca, `plugin/email/api/Install.php`, webman-admin eklenti pazarının kurulumu veya kaldırılmasını gerçekleştirmek için çağrılabilen otomatik olarak oluşturulmuş bir dosyadır.
