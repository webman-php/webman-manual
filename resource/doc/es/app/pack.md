# Empaquetar

Por ejemplo, para empaquetar el complemento de la aplicación "foo":

- Establece el número de versión en `plugin/foo/config/app.php` (**importante**).
- Elimina los archivos innecesarios en `plugin/foo`, especialmente los archivos temporales de prueba en `plugin/foo/public`.
- Elimina la configuración de la base de datos y Redis. Si tu proyecto tiene configuraciones independientes de base de datos y Redis, estas configuraciones deberían activarse durante la instalación inicial del complemento (debes implementar esto tú mismo), permitiendo al administrador completar manualmente la información y generarla.
- Restaura otros archivos que necesiten ser restaurados a su estado original.
- Después de completar las operaciones anteriores, entra al directorio `{proyecto principal}/plugin/` y utiliza el comando `zip -r foo.zip foo` para generar foo.zip.
