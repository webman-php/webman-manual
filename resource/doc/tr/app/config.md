# Yapılandırma Dosyası

Eklentinin yapılandırması normal bir webman projesiyle aynıdır, ancak genellikle eklentinin yapılandırması yalnızca geçerli eklenti için etkilidir ve genellikle ana projeye bir etkisi olmaz.
Örneğin, `plugin.foo.app.controller_suffix` değeri yalnızca eklentinin denetleyici sonekini etkiler, ana projeye etkisi yoktur.
Örneğin, `plugin.foo.app.controller_reuse` değeri yalnızca eklentinin denetleyiciyi yeniden kullanıp kullanmayacağını etkiler, ana projeye etkisi yoktur.
Örneğin, `plugin.foo.middleware` değeri yalnızca eklentinin orta uçlarını etkiler, ana projeye etkisi yoktur.
Örneğin, `plugin.foo.view` değeri yalnızca eklentinin kullandığı görünümü etkiler, ana projeye etkisi yoktur.
Örneğin, `plugin.foo.container` değeri yalnızca eklentinin kullandığı konteyneri etkiler, ana projeye etkisi yoktur.
Örneğin, `plugin.foo.exception` değeri yalnızca eklentinin istisna işleme sınıfını etkiler, ana projeye etkisi yoktur.

Ancak, çünkü yönlendirme globaldir, bu nedenle eklenti yapılandırmasının yönlendirmesi de genel bir etkiye sahiptir.

## Yapılandırmayı Almak
Bir eklentinin yapılandırmasını almanın yöntemi `config('plugin.{eklenti}.{belirli yapılandırma}');` şeklindedir, örneğin `plugin/foo/config/app.php` dosyasının tüm yapılandırmasını almanın yöntemi `config('plugin.foo.app')` şeklindedir.
Benzer şekilde, ana proje veya diğer eklentiler de `config('plugin.foo.xxx')` kullanarak foo eklentisinin yapılandırmasını alabilir.

## Desteklenmeyen Yapılandırmalar
Uygulama eklentileri `server.php`, `session.php` yapılandırmalarını desteklemez, `app.request_class`, `app.public_path`, `app.runtime_path` yapılandırmalarını desteklemez.
