# Plugins
Los plugins se dividen en **plugins base** y **plugins de aplicación**.

#### Plugins base
Los plugins base pueden entenderse como componentes básicos de webman. Pueden ser una biblioteca común (por ejemplo, webman/think-orm), un middleware común (por ejemplo, webman/cors), un conjunto de configuraciones de enrutamiento (como webman/auto-route), o un proceso personalizado (por ejemplo, webman/redis-queue), entre otros.

Para más información, consultar [Plugins base](plugin/base.md).

> **Nota**
> Los plugins base requieren webman>=1.2.0

#### Plugins de aplicación
Los plugins de aplicación son una aplicación completa, como un sistema de preguntas y respuestas, un sistema de gestión de contenidos (CMS), un sistema de comercio electrónico, etc.
Para más información, consultar [Plugins de aplicación](app/app.md).

> **Plugins de aplicación**
> Los plugins de aplicación requieren webman>=1.4.0
