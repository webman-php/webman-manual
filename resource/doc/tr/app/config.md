# Yapılandırma Dosyası

Eklentinin yapılandırması normal webman projesiyle aynıdır, ancak genellikle eklentinin yapılandırması yalnızca mevcut eklenti için geçerlidir ve ana projeyi genellikle etkilemez.
Örneğin, `plugin.foo.app.controller_suffix` değeri yalnızca eklentinin denetleyici son ekini etkiler, ana projeyi etkilemez.
Örneğin, `plugin.foo.app.controller_reuse` değeri yalnızca eklentinin denetleyiciyi yeniden kullanıp kullanmayacağını etkiler, ana projeyi etkilemez.
Örneğin, `plugin.foo.middleware` değeri yalnızca eklentinin ara yazılımını etkiler, ana projeyi etkilemez.
Örneğin, `plugin.foo.view` değeri yalnızca eklentinin kullandığı görünümü etkiler, ana projeyi etkilemez.
Örneğin, `plugin.foo.container` değeri yalnızca eklentinin kullandığı konteynırı etkiler, ana projeyi etkilemez.
Örneğin, `plugin.foo.exception` değeri yalnızca eklentinin istisna işleme sınıfını etkiler, ana projeyi etkilemez.

Ancak, çünkü yönlendirme geneldir, bu nedenle eklentinin yapılandırması da genel olarak etkilidir.

## Yapılandırmayı Alma
Bir eklentinin yapılandırmasını almak için `config('plugin.{eklenti}.{belirli yapılandırma}');` yöntemini kullanın, örneğin `plugin/foo/config/app.php` dosyasının tüm yapılandırmasını almak için `config('plugin.foo.app')` yöntemini kullanın.
Aynı şekilde ana proje veya diğer eklentiler de `config('plugin.foo.xxx')` kullanarak foo eklentisinin yapılandırmasını alabilir.

## Desteklenmeyen Yapılandırmalar
Uygulama eklentileri server.php, session.php yapılandırmalarını desteklemez, `app.request_class`, `app.public_path`, `app.runtime_path` yapılandırmalarını desteklemez.
