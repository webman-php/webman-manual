# Rate Limiter

webman Rate Limiter mit Annotation-basierter Begrenzung.
Unterstützt apcu-, redis- und memory-Treiber.

## Quellcode

https://github.com/webman-php/limiter

## Installation

```
composer require webman/limiter
```

## Verwendung

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // Standard ist IP-basierte Begrenzung, Standard-Zeitfenster 1 Sekunde
        return 'Maximal 10 Anfragen pro IP pro Sekunde';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, Begrenzung nach Benutzer-ID, erfordert session('user.id') nicht leer
        return 'Maximal 100 Suchen pro Benutzer pro 60 Sekunden';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: 'Nur 1 E-Mail pro Person pro Minute')]
    public function sendMail(): string
    {
        // key: Limit::SID, Begrenzung nach session_id
        return 'E-Mail erfolgreich gesendet';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'Heutige Gutscheine ausverkauft, bitte morgen erneut versuchen')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'Jeder Benutzer kann nur einen Gutschein pro Tag einlösen')]
    public function coupon(): string
    {
        // key: 'coupon', benutzerdefinierter Key für globale Begrenzung, max. 100 Gutscheine pro Tag
        // Auch Begrenzung nach Benutzer-ID, jeder Benutzer nur einen Gutschein pro Tag
        return 'Gutschein erfolgreich gesendet';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'Maximal 5 SMS pro Telefonnummer pro Tag')]
    public function sendSms2(): string
    {
        // Bei variablem Key: [Klasse, statische_Methode], z.B. [UserController::class, 'getMobile'] verwendet Rückgabewert von UserController::getMobile() als Key
        return 'SMS erfolgreich gesendet';
    }

    /**
     * Benutzerdefinierter Key, Telefonnummer abrufen, muss statische Methode sein
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'Rate begrenzt', exception: RuntimeException::class)]
    public function testException(): string
    {
        // Standard-Exception bei Überschreitung: support\limiter\RateLimitException, änderbar über exception-Parameter
        return 'ok';
    }

}
```

**Hinweise**

* Verwendet Fixed-Window-Algorithmus
* Standard-ttl-Zeitfenster ist 1 Sekunde
* Zeitfenster über ttl setzen, z.B. `ttl:60` für 60 Sekunden
* Standard-Begrenzungsdimension ist IP (Standard `127.0.0.1` nicht begrenzt, siehe Konfiguration unten)
* Integriert: IP-, UID- (erfordert `session('user.id')` nicht leer) und SID-Begrenzung (nach `session_id`)
* Bei nginx-Proxy `X-Forwarded-For`-Header für IP-Begrenzung übergeben, siehe [nginx Proxy](../others/nginx-proxy.md)
* Löst `support\limiter\RateLimitException` bei Überschreitung aus, benutzerdefinierte Exception-Klasse über `exception:xx`
* Standard-Fehlermeldung bei Überschreitung ist `Too Many Requests`, benutzerdefinierte Meldung über `message:xx`
* Standard-Fehlermeldung auch über [Übersetzung](translation.md) änderbar, Linux-Referenz:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

Manchmal möchten Entwickler den Rate Limiter direkt im Code aufrufen, siehe folgendes Beispiel:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // mobile wird hier als Key verwendet
        Limiter::check($mobile, 5, 24*60*60, 'Maximal 5 SMS pro Telefonnummer pro Tag');
        return 'SMS erfolgreich gesendet';
    }
}
```

## Konfiguration

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // Diese IPs werden nicht begrenzt (nur wirksam wenn key Limit::IP ist)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: Rate Limiting aktivieren
* **driver**: Einer von `auto`, `apcu`, `memory`, `redis`; `auto` wählt automatisch zwischen `apcu` (bevorzugt) und `memory`
* **stores**: Redis-Konfiguration, `connection` entspricht dem Key in `config/redis.php`
* **ip_whitelist**: Whitelist-IPs werden nicht begrenzt (nur wirksam wenn key `Limit::IP` ist)

## Driver-Auswahl

**memory**

* Einführung
  Keine Erweiterungen nötig, beste Leistung.

* Einschränkungen
  Begrenzung nur für aktuellen Prozess, keine Datenfreigabe zwischen Prozessen, Cluster-Begrenzung nicht unterstützt.

* Anwendungsfälle
  Windows-Entwicklungsumgebung; Geschäftsszenarien ohne strenge Begrenzung; CC-Angriffsabwehr.

**apcu**

* Erweiterungsinstallation
  apcu-Erweiterung erforderlich, php.ini-Einstellungen:

```
apc.enabled=1
apc.enable_cli=1
```

php.ini-Pfad mit `php --ini` ermitteln

* Einführung
  Sehr gute Leistung, unterstützt Multi-Prozess-Datenfreigabe.

* Einschränkungen
  Cluster nicht unterstützt

* Anwendungsfälle
  Jede Entwicklungsumgebung; Produktions-Einzel-Server-Begrenzung; Cluster ohne strenge Begrenzung; CC-Angriffsabwehr.

**redis**

* Abhängigkeiten
  redis-Erweiterung und Redis-Komponente erforderlich, Installation:

```
composer require -W webman/redis illuminate/events
```

* Einführung
  Geringere Leistung als apcu, unterstützt präzise Begrenzung für Einzel-Server und Cluster

* Anwendungsfälle
  Entwicklungsumgebung; Produktions-Einzel-Server; Cluster-Umgebung
