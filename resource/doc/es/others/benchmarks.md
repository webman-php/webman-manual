# Prueba de rendimiento

### ¿Qué factores afectan los resultados de la prueba de rendimiento?
* La latencia de red desde la máquina de prueba al servidor (se recomienda realizar pruebas en la red interna o en la misma máquina)
* Ancho de banda desde la máquina de prueba al servidor (se recomienda realizar pruebas en la red interna o en la misma máquina)
* ¿Está activado el HTTP keep-alive? (se recomienda activarlo)
* ¿Es suficiente la cantidad de conexiones simultáneas? (para pruebas externas, se sugiere aumentar la cantidad de conexiones simultáneas)
* ¿El número de procesos del servidor es adecuado? (se sugiere que el número de procesos para un servicio "helloworld" sea igual al número de CPUs, y para servicios de base de datos sea cuatro veces o más el número de CPUs)
* Rendimiento del servicio en sí (por ejemplo, si se está utilizando una base de datos externa)

### ¿Qué es HTTP keep-alive?
El mecanismo de HTTP Keep-Alive es una técnica para enviar múltiples solicitudes y respuestas HTTP a través de una única conexión TCP, y tiene un gran impacto en los resultados de la prueba de rendimiento. Deshabilitar el keep-alive puede disminuir el rendimiento, ya que necesario realizar múltiples conexiones. La mayoría de los navegadores tienen activado por defecto el keep-alive para mejorar el rendimiento. Se recomienda activar el keep-alive durante las pruebas de rendimiento.

### ¿Cómo activar el HTTP keep-alive durante las pruebas de rendimiento?
Si se utiliza el programa ab para las pruebas, es necesario agregar el parámetro -k, por ejemplo: `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
Para apipost, es necesario devolver el encabezado gzip en la respuesta para activar el keep-alive (esto es un problema conocido de apipost).
La mayoría de los otros programas de pruebas de rendimiento tendrán el keep-alive activado por defecto.

### ¿Por qué el rendimiento de las pruebas de presión a través de la red externa es bajo?
La alta latencia de la red externa puede causar una disminución en el rendimiento, lo cual es un fenómeno normal. Por ejemplo, las pruebas de presión en una página como baidu pueden tener una QPS de solo unas pocas docenas.
Se recomienda realizar pruebas en la red interna o en la misma máquina para excluir el efecto de la latencia de la red. Si es necesario realizar pruebas en la red externa, se puede aumentar la cantidad de conexiones simultáneas para aumentar el rendimiento (asegurándose de que haya suficiente ancho de banda disponible).

### ¿Por qué el rendimiento disminuye después de pasar por un proxy nginx?
El funcionamiento de nginx requiere recursos del sistema. Además, la comunicación entre nginx y webman también consume recursos. Dado que los recursos del sistema son limitados y webman no puede acceder a todos ellos, es normal que el rendimiento del sistema en general disminuya.
Para reducir al mínimo el impacto del proxy nginx en el rendimiento, se puede considerar desactivar el registro de nginx (`access_log off;`), y activar el keep-alive entre nginx y webman, consulte [proxy nginx](nginx-proxy.md). 
Además, HTTPS consume más recursos que HTTP, ya que requiere un handshake SSL/TLS, cifrado/descifrado de datos, y aumenta el tamaño de los paquetes, ocupando más ancho de banda, lo que puede disminuir el rendimiento. Si la prueba se realiza con conexiones de corta duración (sin habilitar el HTTP keep-alive), cada solicitud requerirá un handshake adicional SSL/TLS, lo que disminuirá significativamente el rendimiento. Se recomienda habilitar el HTTP keep-alive durante las pruebas de rendimiento con HTTPS.

### ¿Cómo saber si el sistema ha alcanzado su límite de rendimiento?
Por lo general, cuando la CPU alcanza el 100%, significa que el rendimiento del sistema ha alcanzado su límite. Si la CPU aún tiene recursos disponibles, eso significa que no se ha alcanzado el límite y se puede aumentar la cantidad de conexiones simultáneas para aumentar el QPS. Si aumentar la cantidad de conexiones no aumenta el QPS, podría ser que la cantidad de procesos webman no es suficiente, en ese caso, es posible que se deba aumentar la cantidad de procesos webman. Si aún así no se observa un aumento en el rendimiento, es necesario considerar si el ancho de banda es suficiente.

### ¿Por qué los resultados de las pruebas de presión muestran que el rendimiento de webman es inferior al del framework go gin?
[techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) muestra que webman supera a gin en alrededor del doble en todas las métricas, desde texto plano hasta consultas y actualizaciones de bases de datos.
Si los resultados no son similares, podría ser porque se usa un ORM en webman que causa una pérdida significativa de rendimiento. Se puede intentar comparar webman con PDO nativo y gin con SQL nativo.

### ¿Cuánto se pierde en rendimiento al usar ORM en webman?
Aquí hay un conjunto de datos de pruebas

**Entorno**
Servidor de nube de Alibaba con 4 núcleos y 4 GB de RAM, consulta aleatoria de un registro de 100,000 para devolver un dato JSON.

**Si se usa PDO nativo**
El QPS de webman es de 17,800.

**Si se usa Db::table() de Laravel**
El QPS de webman disminuye a 9,400.

**Si se usa el modelo de Laravel**
El QPS de webman disminuye a 7,200.

Los resultados son similares con thinkORM.

> **Nota**
> Aunque el uso de un ORM puede disminuir el rendimiento, para la mayoría de los negocios es suficiente. Deberíamos encontrar un equilibrio entre la eficiencia de desarrollo, la mantenibilidad, el rendimiento y otros criterios, en lugar de buscar solo el rendimiento.

### ¿Por qué la presión de apipost muestra un QPS tan bajo?
El módulo de pruebas de presión de apipost tiene un error, si el servidor no devuelve el encabezado gzip, no se puede mantener el keep-alive, lo que resulta en una disminución significativa del rendimiento.
La solución es comprimir los datos al devolverlos y agregar el encabezado gzip, por ejemplo:
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
Además, en algunos casos, apipost no puede generar una presión satisfactoria, lo que resulta en un QPS alrededor de un 50% menor que el de ab con la misma cantidad de conexiones simultáneas.
Se recomienda realizar pruebas de presión con ab, wrk u otro software especializado en pruebas de rendimiento en lugar de apipost.

### Configurar la cantidad adecuada de procesos
webman abre por defecto 4 veces la cantidad de procesos equivalentes a la cantidad de CPUs. Para un servicio "helloworld" sin E/S de red, es óptimo abrir la misma cantidad de procesos que la cantidad de núcleos de CPU para reducir el tiempo de cambio de procesos.
Si la aplicación involucra bloqueos de I/O como bases de datos o redis, se puede configurar la cantidad de procesos de 3 a 8 veces la cantidad de CPUs, ya que se necesita más procesos para manejar la concurrencia, y la sobrecarga de cambio de procesos es relativamente insignificante en comparación con las E/S de bloqueo.

### Rangos de referencia para pruebas de presión

**Servidor en la nube: 4 núcleos, 4 GB de RAM, 16 procesos, pruebas internas/en red interna**

| - | Con keep-alive | Sin keep-alive |
|--|-----|-----|
| hello world | 80,000-160,000 QPS | 10,000-30,000 QPS |
| Consulta única a base de datos | 10,000-20,000 QPS | 10,000 QPS |

[**Datos de pruebas de tercero techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)


### Ejemplos de comandos de pruebas de presión

**ab**
```
# 100,000 solicitudes, 200 conexiones simultáneas, con keep-alive
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100,000 solicitudes, 200 conexiones simultáneas, sin keep-alive
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# Presión de 200 conexiones, durante 10 segundos, con keep-alive (predeterminado)
wrk -c 200 -d 10s http://example.com
```
