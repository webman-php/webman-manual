## Stomp Kuyruğu

Stomp, basit (akış) metin tabanlı mesaj yönlendirme protokolüdür ve istemci için herhangi bir STOMP mesaj aracısı (Broker) ile etkileşim sağlayan, işbirliği yapılabilir bir bağlantı düzeni sunar. [workerman/stomp](https://github.com/walkor/stomp), öncelikle RabbitMQ, Apollo, ActiveMQ vb. mesaj kuyruğu senaryoları için Stomp istemcisini gerçekleştirir.

## Kurulum
`composer require webman/stomp`

## Yapılandırma
Yapılandırma dosyası `config/plugin/webman/stomp` klasöründe bulunmaktadır.

## Mesaj Gönderme
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // Kuyruk
        $queue = 'örnekler';
        // Veri (dizi iletilirken serialize, json_encode vb. gibi kendi serileştirmeniz gerekmektedir)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // Gönderimi gerçekleştir
        Client::send($queue, $data);

        return response('redis kuyruk testi');
    }

}
```
> Diğer projelerle uyumlu olması için, Stomp bileşeni otomatik serialize/deserialize işlevi sağlamamaktadır. Eğer dizi verisi gönderiliyorsa, serialize işlemini kendiniz yapmalı ve tüketme işlemi sırasında kendiniz deserialize etmelisiniz.

## Mesaj Tüketme
Yeni bir `app/queue/stomp/MyMailSend.php` dosyası oluşturun (`class` adı isteğe bağlıdır, sadece psr4 kurallarına uymalıdır).
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // Kuyruk adı
    public $queue = 'örnekler';

    // Bağlantı adı, stomp.php dosyasındaki bağlantıya karşılık gelir
    public $connection = 'varsayılan';

    // Değer client ise, $ack_resolver->ack() çağrısının sunucuya başarıyla tüketildiğini bildirmesi gerekir
    // Değer auto ise, $ack_resolver->ack() çağrısına gerek yoktur
    public $ack = 'auto';

    // Tüketme
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // Eğer veri bir dizi ise, kendiniz deserialize etmelisiniz
        var_export(json_decode($data, true)); // Çıktı ['to' => 'tom@gmail.com', 'content' => 'hello']
        // Sunucuya, başarıyla tüketildiğini bildir
        $ack_resolver->ack(); // ack değeri auto ise bu çağrıyı atlayabilirsiniz
    }
}
```

# rabbitmq'da stomp protokolünü etkinleştirme
RabbitMQ varsayılan olarak stomp protokolünü etkinleştirmez, etkinleştirmek için aşağıdaki komutu kullanmanız gerekmektedir
```shell
rabbitmq-plugins enable rabbitmq_stomp
```           
Etkinleştirdikten sonra stomp portu varsayılan olarak 61613'tür.
