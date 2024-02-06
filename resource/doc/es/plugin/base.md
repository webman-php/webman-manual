# Plugins básicos

Los plugins básicos suelen ser componentes genéricos que generalmente se instalan usando Composer y se colocan en la carpeta `vendor`. Durante la instalación, es posible copiar automáticamente algunas configuraciones personalizadas (como middleware, procesos, rutas, etc.) en el directorio `{proyecto principal}/config/plugin`. Webman reconocerá automáticamente estas configuraciones en el directorio y las fusionará con la configuración principal, lo que permitirá que los plugins intervengan en cualquier etapa del ciclo de vida de webman.

Para obtener más información, consulta [Creación de plugins básicos](create.md)
