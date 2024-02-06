# Prueba de estrés

### ¿Qué factores afectan los resultados de la prueba de estrés?
* La latencia de red desde la máquina de pruebas hasta el servidor (se recomienda realizar la prueba en la red interna o en la misma máquina)
* El ancho de banda desde la máquina de pruebas hasta el servidor (se recomienda realizar la prueba en la red interna o en la misma máquina)
* Si se ha habilitado el HTTP keep-alive (se recomienda habilitarlo)
* Si el número de solicitudes concurrentes es suficiente (para pruebas en la red externa, se recomienda aumentar al máximo las solicitudes concurrentes)
* Si el número de procesos del servidor es adecuado (se sugiere que el número de procesos para el servicio "helloworld" sea igual al número de CPUs, y para el servicio de base de datos sea cuatro veces o más el número de CPUs)
* El rendimiento del servicio en sí mismo (por ejemplo, si se está utilizando una base de datos en la red externa)

### ¿Qué es el HTTP keep-alive?
El mecanismo de Keep-Alive HTTP es una técnica que permite enviar múltiples solicitudes y respuestas HTTP a través de una única conexión TCP, y tiene un gran impacto en los resultados de la prueba de rendimiento. Después de desactivar el keep-alive, el número de solicitudes por segundo (QPS) puede disminuir significativamente. Los navegadores actualmente habilitan keep-alive de forma predeterminada, lo que significa que después de que un navegador accede a una dirección HTTP, mantiene la conexión abierta temporalmente y la reutiliza para la siguiente solicitud, aumentando así el rendimiento. Se recomienda activar el keep-alive durante la prueba de estrés.

### ¿Cómo se activa el HTTP keep-alive durante la prueba de estrés?
Si se utiliza el programa "ab" para realizar la prueba de estrés, se debe agregar el parámetro -k, por ejemplo: `ab -n100000 -c200 -k http://127.0.0.1:8787/`. Para apipost, es necesario devolver la cabecera "gzip" en la respuesta para activar el keep-alive (esto es un error de apipost, consulte a continuación). La mayoría de los otros programas de pruebas de estrés tienen el keep-alive activado de forma predeterminada.

### ¿Por qué el QPS es tan bajo al realizar la prueba de estrés desde la red externa?
La alta latencia de la red externa resulta en un QPS bajo, lo cual es un fenómeno normal. Por ejemplo, al realizar una prueba de estrés en la página de Baidu, es posible que solo se obtengan unos pocos QPS. Se recomienda realizar la prueba en la red interna o en la misma máquina para excluir el impacto de la latencia de red. Si es necesario realizar la prueba desde la red externa, se puede aumentar el número de solicitudes concurrentes para aumentar el rendimiento (siempre y cuando se garantice un ancho de banda adecuado).

### ¿Por qué el rendimiento disminuye después de pasar por un proxy de nginx?
La operación de nginx consume recursos del sistema. Además, la comunicación entre nginx y webman también requiere recursos. Sin embargo, los recursos del sistema son limitados, y webman no puede acceder a todos los recursos del sistema, por lo que es normal que el rendimiento del sistema en su conjunto pueda disminuir. Para minimizar el impacto del rendimiento debido al proxy de nginx, se puede considerar deshabilitar el registro de nginx (`access_log off;`), y activar el keep-alive entre nginx y webman, consulte [proxy de nginx](nginx-proxy.md). Además, HTTPS consume más recursos en comparación con HTTP, ya que requiere un proceso de handshaking SSL/TLS, encriptación y desencriptación de datos, y aumenta el tamaño de los paquetes ocupando más ancho de banda, lo que puede afectar el rendimiento. Si se realizan pruebas de estrés con conexiones de corta duración (sin mantener el HTTP keep-alive), cada solicitud adicional requerirá un handshaking SSL/TLS adicional, lo que disminuirá significativamente el rendimiento. Se recomienda habilitar el HTTP keep-alive al realizar pruebas de estrés con HTTPS.

### ¿Cómo saber si el sistema ha alcanzado su límite de rendimiento?
Normalmente, cuando la CPU alcanza el 100%, significa que el rendimiento del sistema ha alcanzado su límite. Si la CPU todavía tiene recursos disponibles, significa que no se ha alcanzado el límite, por lo tanto, en este caso se puede aumentar el número de solicitudes concurrentes para mejorar el QPS. Si aumentar las solicitudes concurrentes no mejora el QPS, es posible que el número de procesos de webman sea insuficiente, en cuyo caso se debe aumentar adecuadamente el número de procesos de webman. Si aún así no se puede mejorar el rendimiento, se debe considerar si el ancho de banda es suficiente.

### ¿Por qué los resultados de la prueba de rendimiento de webman son inferiores a los de la estructura de gin en Go?
Las pruebas [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) muestran que webman supera a gin aproximadamente en un 100% en todos los indicadores como consultas de texto puro, consultas de base de datos, actualizaciones de base de datos, etc. Si los resultados varían, puede ser debido al uso de ORM en webman, lo que provoca una pérdida significativa de rendimiento. Se recomienda intentar comparar webman con el uso de PDO nativo y gin con SQL nativo.

### ¿Cuánto afecta el rendimiento el uso de ORM en webman?
Aquí hay un conjunto de datos de prueba:

**Entorno**
Servidor en la nube: 4 núcleos, 4 GB de RAM; se realiza una consulta aleatoria de un registro entre 100,000 y se devuelve en formato JSON.

**Si se usa PDO nativo**
El QPS de webman es de 17,800.

**Si se utiliza 'Db::table()' de Laravel**
El QPS de webman disminuye a 9,400.

**Si se utiliza un modelo de Laravel**
El QPS de webman disminuye a 7,200.

ThinkORM tiene resultados similares con poco cambio.

> **Nota**
> Aunque el uso de ORM puede disminuir el rendimiento, para la mayoría de las aplicaciones es suficiente. Deberíamos encontrar un equilibrio entre la eficiencia de desarrollo, mantenibilidad y rendimiento en lugar de buscar solo el rendimiento.

### ¿Por qué el QPS es tan bajo al realizar pruebas de estrés con apipost?
El módulo de prueba de estrés de apipost tiene un error. Si el servidor no devuelve la cabecera 'gzip', no podrá mantener el keep-alive, lo que resultará en una disminución significativa del rendimiento. La solución es comprimir los datos al devolverlos y agregar la cabecera 'gzip'. Por ejemplo:

```php
<?php
namespace app\controller;
class IndexController
{
    public function index()
    {
        return response(gzencode('hello webman'))->withHeader('Content-Encoding', 'gzip');
    }
}
```
Además, en algunas situaciones, apipost no puede generar una presión satisfactoria, lo que se traduce en un QPS aproximadamente un 50% menor que el logrado con ab. Se recomienda realizar pruebas de estrés con ab, wrk u otro software de prueba de estrés profesional en lugar de apipost.

### Establecer un número adecuado de procesos
Por defecto, webman abre 4 veces el número de procesos de la CPU. Para pruebas de estrés en operaciones de red sin I/O, se obtiene el mejor rendimiento cuando el número de procesos es igual al número de núcleos de la CPU, ya que esto reduce el costo del cambio de procesos. Sin embargo, en operaciones con I/O bloqueante como bases de datos y Redis, el número de procesos puede ser de 3 a 8 veces el número de núcleos de la CPU, ya que se requieren más procesos para aumentar la concurrencia, y el costo del cambio de procesos es relativamente insignificante en comparación con el I/O bloqueante.

### Algunos rangos de referencia para pruebas de estrés

**Servidor en la nube: 4 núcleos, 4 GB de RAM, 16 procesos, pruebas en la misma máquina/en la red interna**

| - | Keep-Alive habilitado | Keep-Alive deshabilitado |
|--|-----|-----|
| hello world | 80,000-160,000 QPS | 10,000-30,000 QPS |
| Búsqueda única en base de datos | 10,000-20,000 QPS | 10,000 QPS |

[**Datos de pruebas de techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)


### Ejemplos de comandos de prueba de estrés

**ab**
```
# 100,000 solicitudes, 200 solicitudes concurrentes, keep-alive habilitado
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100,000 solicitudes, 200 solicitudes concurrentes, keep-alive deshabilitado
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# 200 solicitudes concurrentes durante 10 segundos, keep-alive habilitado (por defecto)
wrk -c 200 -d 10s http://example.com
```
