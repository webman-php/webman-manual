# Plugin
I plugin sono divisi in **plugin base** e **plugin applicativi**.

#### Plugin base
I plugin base possono essere considerati come alcuni componenti di base di webman, potrebbero essere una libreria generica (ad esempio webman/think-orm), potrebbero essere un middleware generico (come webman/cors), oppure un insieme di configurazioni del percorso (come webman/auto-route), o ancora un processo personalizzato (ad esempio webman/redis-queue) e così via.

Per ulteriori informazioni, consulta [plugin base](plugin/base.md).

> **Nota**
> I plugin base richiedono webman>=1.2.0

#### Plugin applicativi
I plugin applicativi sono un'applicazione completa, come ad esempio un sistema di domande e risposte, un sistema CMS, un sistema di negozi, ecc.
Per ulteriori informazioni, consulta [plugin applicativi](app/app.md).

> **Plugin applicativi**
> I plugin applicativi richiedono webman>=1.4.0
