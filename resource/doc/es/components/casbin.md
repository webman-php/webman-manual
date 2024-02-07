# Casbin

## Descripción

Casbin es un potente y eficiente marco de control de acceso de código abierto cuyo mecanismo de gestión de permisos admite varios modelos de control de acceso.

## Dirección del proyecto

https://github.com/teamones-open/casbin

## Instalación

```php
composer require teamones/casbin
```

## Sitio web de Casbin

Para obtener información detallada sobre el uso, consulte la documentación oficial en chino. Aquí solo se explica cómo configurar y usar en webman.

https://casbin.org/docs/zh-CN/overview

## Estructura de directorios

``` 
.
├── config                        Directorio de configuración
│   ├── casbin-restful-model.conf Archivo de configuración del modelo de permisos utilizado
│   ├── casbin.php                Configuración de casbin
......
├── database                      Archivos de base de datos
│   ├── migrations                Archivos de migración
│   │   └── 20210218074218_create_rule_table.php
......
```

## Archivos de migración de base de datos

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * Método de cambio.
     *
     * Escriba sus migraciones reversibles utilizando este método.
     *
     * Más información sobre cómo escribir migraciones está disponible aquí:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * Los siguientes comandos se pueden utilizar en este método y Phinx
     * los revertirá automáticamente al realizar un retroceso:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Cualquier otro cambio destructivo resultará en un error al intentar
     * revertir la migración.
     *
     * Recuerde llamar a "create()" o "update()" y NO a "save()" al trabajar
     * con la clase Table.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Tabla de reglas']);

        // Agregar campos de datos
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'ID principal'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Tipo de regla'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        // Ejecutar la creación
        $table->create();
    }
}
```

## Configuración de casbin

Consulte la sintaxis de configuración del modelo de reglas de permisos en: https://casbin.org/docs/zh-CN/syntax-for-models

```php
<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Archivo de configuración del modelo de reglas de permisos
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modelo o adaptador
            'class' => \app\model\Rule::class,
        ],
    ],
    // Se pueden configurar múltiples modelos de permisos
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Archivo de configuración del modelo de reglas de permisos
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modelo o adaptador
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Adaptador

El adaptador envuelto actualmente por composer es el método de modelo think-orm. Para otros ORM, consulte vendor/teamones/src/adapters/DatabaseAdapter.php

Luego modifique la configuración

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Archivo de configuración del modelo de reglas de permisos
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // aquí tipo configurado como modo adaptador
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Instrucciones de uso

### Importar

```php
# Importar
use teamones\casbin\Enforcer;
```

### Dos formas de uso

```php
# 1. Usar la configuración predeterminada
Enforcer::addPermissionForUser('usuario1', '/usuario', 'leer');

# 1. Utilizar la configuración de rbac personalizada
Enforcer::instance('rbac')->addPermissionForUser('usuario1', '/usuario', 'leer');
```

### API comunes

Para obtener más información sobre el uso de la API, consulte la documentación oficial:

- API de gestión: https://casbin.org/docs/zh-CN/management-api
- API de RBAC: https://casbin.org/docs/zh-CN/rbac-api

```php
# Agregar permiso para un usuario

Enforcer::addPermissionForUser('usuario1', '/usuario', 'leer');

# Eliminar un permiso de un usuario

Enforcer::deletePermissionForUser('usuario1', '/usuario', 'leer');

# Obtener todos los permisos de un usuario

Enforcer::getPermissionsForUser('usuario1');

# Agregar un rol para un usuario

Enforcer::addRoleForUser('usuario1', 'rol1');

# Agregar permiso para un rol

Enforcer::addPermissionForUser('rol1', '/usuario', 'editar');

# Obtener todos los roles

Enforcer::getAllRoles();

# Obtener todos los roles de un usuario

Enforcer::getRolesForUser('usuario1');

# Obtener usuarios según el rol

Enforcer::getUsersForRole('rol1');

# Comprobar si un usuario pertenece a un rol

Enforcer::hasRoleForUser('usuario1', 'rol1');

# Eliminar rol de un usuario

Enforcer::deleteRoleForUser('usuario1', 'rol1');

# Eliminar todos los roles de un usuario

Enforcer::deleteRolesForUser('usuario1');

# Eliminar rol

Enforcer::deleteRole('rol1');

# Eliminar permiso

Enforcer::deletePermission('/usuario', 'leer');

# Eliminar todos los permisos de un usuario o rol

Enforcer::deletePermissionsForUser('usuario1');
Enforcer::deletePermissionsForUser('rol1');

# Comprobar permisos, devolver true o false

Enforcer::enforce("usuario1", "/usuario", "editar");
```
