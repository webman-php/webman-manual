# Empaquetar

Por ejemplo, empaquetar el plugin de la aplicación "foo"

- Establecer el número de versión en `plugin/foo/config/app.php` (**Importante**)
- Eliminar los archivos no necesarios en `plugin/foo`, especialmente los archivos temporales de prueba de carga en `plugin/foo/public`
- Eliminar la configuración de la base de datos y Redis. Si su proyecto tiene su propia configuración de base de datos y Redis, estas configuraciones deben ser activadas por un programa de instalación la primera vez que se accede a la aplicación (debe implementarse manualmente), para que el administrador las complete manualmente y las genere.
- Restaurar otros archivos que deben ser restaurados a su estado original
- Después de completar las operaciones anteriores, ingresar al directorio `{proyecto_principal}/plugin/` y utilizar el comando `zip -r foo.zip foo` para generar foo.zip
