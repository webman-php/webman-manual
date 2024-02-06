# Kurulum

Uygulama eklentisi kurulumu için iki seçenek bulunmaktadır:

## Eklenti Pazarından Kurulum
[Resmi web yönetim paneli webman-admin](https://www.workerman.net/plugin/82)'e gidin ve uygulama eklentisi sayfasına giderek uygun uygulama eklentisini kurmak için kurulum düğmesine tıklayın.

## Kaynak kod kurulumu
Uygulama pazarından uygulama eklentisi sıkıştırılmış paketini indirin, açın ve açılan klasörü `{ana proje}/plugin/` dizinine yükleyin (eğer plugin dizini yoksa manuel olarak oluşturmanız gerekebilir). Kurulumu tamamlamak için `php webman app-plugin:install eklentiadı` komutunu çalıştırın.

Örneğin, indirilen sıkıştırılmış paketin adı `ai.zip` ise, bu paketi `{ana proje}/plugin/ai` dizinine açın ve kurulumu tamamlamak için `php webman app-plugin:install ai` komutunu çalıştırın.

# Kaldırma

Aynı şekilde, uygulama eklentisi kaldırma işlemi için de iki seçenek bulunmaktadır:

## Eklenti Pazarından Kaldırma
[Resmi web yönetim paneli webman-admin](https://www.workerman.net/plugin/82)'e gidin ve uygulama eklentisi sayfasına giderek uygun uygulama eklentisini kaldırmak için kaldırma düğmesine tıklayın.

## Kaynak kod kaldırma
Kaldırmak için `php webman app-plugin:uninstall eklentiadı` komutunu çalıştırın, işlem tamamlandıktan sonra manuel olarak `{ana proje}/plugin/` dizini altındaki ilgili eklenti klasörünü silin.
