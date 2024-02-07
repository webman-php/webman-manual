# Kurulum

Uygulama eklentisi kurulumu için iki yöntem bulunmaktadır:

## Eklenti Pazarından Kurulum
[Resmi yönetim paneli webman-admin](https://www.workerman.net/plugin/82)'e gidin ve uygulama eklentileri sayfasına tıklayarak ilgili uygulama eklentisini kurmak için kurulum düğmesini tıklayın.

## Kaynak Kodu Kurulumu
Uygulama pazarından uygulama eklentisi sıkıştırılmış dosyasını indirin, açın ve açılan dizini `{ana proje}/plugin/` dizini içine yükleyin (plugin dizini yoksa elle oluşturmanız gerekebilir). Daha sonra `php webman app-plugin:install eklentiadı` komutunu çalıştırarak kurulumu tamamlayın.

Örneğin, indirilen sıkıştırılmış dosyanın adı `ai.zip` ise, bu dosyayı `{ana proje}/plugin/ai` klasörüne açın ve `php webman app-plugin:install ai` komutunu kullanarak kurulumu tamamlayın.

# Kaldırma

Aynı şekilde, uygulama eklentisi kaldırma işlemi de iki şekilde gerçekleştirilebilir:

## Eklenti Pazarından Kaldırma
[Resmi yönetim paneli webman-admin](https://www.workerman.net/plugin/82)'e gidin ve uygulama eklentileri sayfasına tıklayarak ilgili uygulama eklentisini kaldırmak için kaldırma düğmesini tıklayın.

## Kaynak Kodu Kaldırma
Kaldırma işlemi için `php webman app-plugin:uninstall eklentiadı` komutunu çalıştırarak kaldırma işlemini tamamlayın; ardından el ile `{ana proje}/plugin/` dizini altındaki ilgili eklenti dizinini silin.
