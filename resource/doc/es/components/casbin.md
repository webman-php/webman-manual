# Casbin

## Descripción

Casbin es un marco de control de acceso de código abierto, potente y eficiente, cuyo mecanismo de gestión de permisos admite varios modelos de control de acceso.

## Dirección del proyecto

https://github.com/teamones-open/casbin

## Instalación

```php
composer require teamones/casbin
```

## Sitio web de Casbin

Para obtener instrucciones detalladas, consulte la documentación oficial en chino. Aquí solo se explica cómo configurar y usar en webman.

https://casbin.org/docs/zh-CN/overview

## Estructura de directorios

```
.
├── config                        Directorio de configuración
│   ├── casbin-restful-model.conf Archivo de configuración de modelo de permisos utilizado
│   ├── casbin.php                Configuración de casbin
......
├── database                      Archivos de base de datos
│   ├── migraciones               Archivos de migración
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
     * Método Change.
     *
     * Escriba sus migraciones reversibles utilizando este método.
     *
     * Más información sobre cómo escribir migraciones está disponible aquí:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * Los siguientes comandos se pueden usar en este método y Phinx
     * los revertirá automáticamente al deshacerlos:
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

        // Añadir campos de datos
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

Para la sintaxis de configuración del modelo de reglas de permisos, consulte: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Archivo de configuración de modelo de reglas de permisos
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // modelo o adaptador
            'class' => \app\model\Rule::class,
        ],
    ],
    // Se pueden configurar varios modelos de permisos
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Archivo de configuración de modelo de reglas de permisos
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

La encapsulación actual de composer utiliza el método de modelo de think-orm, para otros ORM consulte vendor/teamones/src/adapters/DatabaseAdapter.php

Luego, modifique la configuración

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Archivo de configuración de modelo de reglas de permisos
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Aquí se configura el tipo como adaptador
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## Instrucciones de uso

### Incluir

```php
# Incluir
use teamones\casbin\Enforcer;
```

### Dos formas de uso

```php
# 1. Usar la configuración predeterminada
Enforcer::addPermissionForUser('usuario1', '/usuario', 'lectura');

# 1. Usar la configuración personalizada rbac
Enforcer::instance('rbac')->addPermissionForUser('usuario1', '/usuario', 'lectura');
```

### Introducción a API común

Para obtener más formas de uso de la API, consulte la documentación oficial

- API de gestión: https://casbin.org/docs/zh-CN/management-api
- API de RBAC: https://casbin.org/docs/zh-CN/rbac-api

```php
# Agregar permiso para usuario

Enforcer::addPermissionForUser('usuario1', '/usuario', 'lectura');

# Eliminar permiso de usuario

Enforcer::deletePermissionForUser('usuario1', '/usuario', 'lectura');

# Obtener todos los permisos del usuario

Enforcer::getPermissionsForUser('usuario1');

# Agregar rol para usuario

Enforcer::addRoleForUser('usuario1', 'rol1');

# Agregar permiso para rol

Enforcer::addPermissionForUser('rol1', '/usuario', 'editar');

# Obtener todos los roles

Enforcer::getAllRoles();

# Obtener todos los roles del usuario

Enforcer::getRolesForUser('usuario1');

# Obtener usuarios según el rol

Enforcer::getUsersForRole('rol1');

# Verificar si el usuario pertenece a un rol

Enforcer::hasRoleForUser('usuario1', 'rol1');

# Eliminar rol de usuario

Enforcer::deleteRoleForUser('usuario1', 'rol1');

# Eliminar todos los roles de usuario

Enforcer::deleteRolesForUser('usuario1');

# Eliminar rol

Enforcer::deleteRole('rol1');

# Eliminar permiso

Enforcer::deletePermission('/usuario', 'lectura');

# Eliminar todos los permisos de usuario o rol

Enforcer::deletePermissionsForUser('usuario1');
Enforcer::deletePermissionsForUser('rol1');

# Verificar permiso, devolver true o false

Enforcer::enforce("usuario1", "/usuario", "editar");
```
