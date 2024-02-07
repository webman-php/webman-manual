# Plugins básicos

Los plugins básicos suelen ser componentes comunes que generalmente se instalan mediante Composer y se ubican en la carpeta `vendor`. Durante la instalación, es posible copiar automáticamente algunas configuraciones personalizadas (como middlewares, procesos, rutas, etc.) al directorio `{proyecto principal}/config/plugin`. Posteriormente, webman reconocerá automáticamente estas configuraciones del directorio y las fusionará con la configuración principal, permitiendo que los plugins intervengan en cualquier etapa del ciclo de vida de webman.

Para obtener más información, consulta [Creación de plugins básicos](create.md).
