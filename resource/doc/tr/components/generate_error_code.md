# Error Code Oluşturma Bileşeni

## Açıklama

Belirli kurallara göre otomatik olarak hata kodlarını oluşturmayı sağlar.

> Dönen veri içindeki code parametresi için, tüm özel kodlar, pozitif sayılar hizmetin normal olduğunu, negatif sayılar ise hizmette anormallik olduğunu temsil eder.

## Proje Adresi

https://github.com/teamones-open/response-code-msg

## Yükleme

```php
composer require teamones/response-code-msg
```

## Kullanım

### Boş ErrorCode Sınıf Dosyası

- Dosya yolu: ./support/ErrorCode.php

```php
<?php
/**
 * Otomatik oluşturulan dosya, lütfen manuel olarak düzeltmeyin.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### Yapılandırma Dosyası

Hata kodları otomatik olarak aşağıda yapılandırılan parametrelere göre artan bir şekilde oluşturulur. Örneğin, şu anda system_number = 201, start_min_number = 10000 ise, ilk hata kodu -20110001 olacaktır.

- Dosya yolu: ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode sınıf dosyası
    "root_path" => app_path(), // Mevcut kod kök dizini
    "system_number" => 201, // Sistem kimliği
    "start_min_number" => 10000 // Hata kodu oluşturma aralığı, örneğin 10000-99999
];
```

### start.php Dosyasına Hata Kodları Otomatik Oluşturmak İçin Başlatma Kodu Ekleyin

- Dosya yolu: ./start.php

```php
// Config::load(config_path(), ['route', 'container']); satırından sonra

// Hata kodlarını oluştur, sadece APP_DEBUG modunda oluştur
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Kod İçinde Kullanım

Aşağıdaki kodda **ErrorCode::ModelAddOptionsError** hata kodudur, burada **ModelAddOptionsError** kullanıcının mevcut gereksinimlere göre anlamlı bir şekilde baş harfi büyük olarak yazması gereken bir kod yazıdır.

> Kodu yazarsınız ancak hemen kullanamazsınız, bir sonraki yeniden başlatmadan sonra ilgili hata kodu otomatik olarak oluşturulacaktır. Bazı durumlarda iki kez yeniden başlatmanız gerekebilir.

```php
<?php
/**
 * Navigasyon ile ilgili işlemler servis sınıfı
 */

namespace app\service;

use app\model\Demo as DemoModel;

// ErrorCode sınıf dosyasını dahil et
use support\ErrorCode;

class Demo
{
    /**
     * Ekleme
     * @param $data
     * @return array|mixed
     * @throws \exception
     */
    public function add($data): array
    {
        try {
            $demo = new DemoModel();
            foreach ($data as $key => $value) {
                $demo->$key = $value;
            }

            $demo->save();

            return $demo->getData();
        } catch (\Throwable $e) {
            // Hata mesajını yazdır
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### Oluşturulduktan Sonra ./support/ErrorCode.php Dosyası

```php
<?php
/**
 * Otomatik oluşturulan dosya, lütfen manuel olarak düzeltmeyin.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
    const LoginNameOrPasswordError = -20110001;
    const UserNotExist = -20110002;
    const TokenNotExist = -20110003;
    const InvalidToken = -20110004;
    const ExpireToken = -20110005;
    const WrongToken = -20110006;
    const ClientIpNotEqual = -20110007;
    const TokenRecordNotFound = -20110008;
    const ModelAddUserError = -20110009;
    const NoInfoToModify = -20110010;
    const OnlyAdminPasswordCanBeModified = -20110011;
    const AdminAccountCannotBeDeleted = -20110012;
    const DbNotExist = -20110013;
    const ModelAddOptionsError = -20110014;
    const UnableToDeleteSystemConfig = -20110015;
    const ConfigParamKeyRequired = -20110016;
    const ExpiryCanNotGreaterThan7days = -20110017;
    const GetPresignedPutObjectUrlError = -20110018;
    const ObjectStorageConfigNotExist = -20110019;
    const UpdateNavIndexSortError = -20110020;
    const TagNameAttNotExist = -20110021;
    const ModelUpdateOptionsError = -20110022;
}
```
