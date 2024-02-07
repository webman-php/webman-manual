# காஸ்பின்

## விளக்கம்

காஸ்பின் ஒரு மிகப் பயனுள்ள, மிகுந்ததானத்தில் பொது அணுகுமுறை முகாமையை ஆதரிக்கும் விடுப்பு கட்டமைத்த முயற்சியாகக் கூடிய பூரணப்பாட்டைக் கொண்ட மூல உருவாக்கி வரும் சின்னத் திட்டமாகும்.
  
## திட்டத்தின் முகவரி

https://github.com/teamones-open/casbin

## நிறுவு

  ```php
  composer require teamones/casbin
  ```

## காஸ்பின் அலுவலகம்

விவரியாக பயன்படுத்தும்போது அலுவலகப் பதிவை பார்க்கலாம், இங்கு உள்ளது எப்படி வகைப்படுத்தி வரவேற்பு செய்ய வேண்டும் என்பதை மடிப்பது

https://casbin.org/docs/zh-CN/overview

## கோப்பு அமைப்பு

``` 
. 
├── config கட்டமைப்பு அடைவு
│   ├── casbin-restful-model.conf பயன்படுத்தும் அணுகுமுறை மாதிரி அமைப்பு கோப்பு
│   ├── casbin.php காஸ்பின் அமைப்பு
...... 
├── database தரவுத்தள கோப்பு
│   ├── migrations நகர்த்தல் கோப்புகள்
│   │   └── 20210218074218_create_rule_table.php
...... 
```
## தரவுத்தள மாறுபாடு கோப்புகள்

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * மாற்று முறை.
     *
     * இந்த முறையை பயின்று மாறுக்கக்கப்படும் மறுமுகப் பயிற்சிகளை உருவாக்கலாம்.
     *
     * மேலும் மாறுக்கப்படும் விவரங்களுக்கு இங்கே செல்லவும்:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * பின்வரும் கட்டங்களை இந்த முறையினிடம் பயன்படுத்தலாம், மறுமுகத்தில் சுழற்சி செய்யும்பொருட்டு:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * மற்ற புழுதிவுகள்வேறு தீங்குள்ள மாறுகள் மாறுக்கவில்லை என்பதன் விளக்கம் மிகுந்தமாகும் போது பிழையாக செல்வது.
     *
     * அடிப்படையாக "create()" அல்லது "update()" ஐ அழுத்தவும் "சேமி()" ஐ அழுத்தவாம்
     * கட்டமைப்பு வகையுடன் வேலை செய்யும் போது அடிப்படையாக அழுத்தப்படும்.
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'விதி அட்டவணை']);

        //தரவு புலங்களைச் சேர்க்கவும்
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'முதன்மை பொருள் ஐடி'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'விதி வகை'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //உருவாக்கம் செய்யும்
        $table->create();
    }
}

```
casbin உள்ளிட்ட கட்டுப்பாடு மாதிரி கட்டமைப்பு மொழி குறித்த விவரங்களைப் பார்க்கவும்: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // அனுமதி விதிகள் மாதிரி கட்டமைப்பு கோப்பு
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model அல்லது adapter
            'class' => \app\model\Rule::class,
        ],
    ],
    // பல உரிமங்கள் மாதிரிக்கு கட்டமைக்க முடியும்
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // அனுமதி விதிகள் மாதிரி கட்டமைப்பு கோப்பு
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // model அல்லது adapter
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### ஒருங்கிணைந்தன

தற்போதைய தொலைப்புcomposer பொறுப்பில் தௌக்கு-orm ஆனது model முறையை உள்ளடக்கியிருக்கும், பின்னர் டேப்புபகிர்ந்து ஒரு மாற்றம் செய்யலாம் vendor/teamones/src/adapters/DatabaseAdapter.php

அப்படியும் உருமங்களை மாற்ற உள்ளடக்கியது

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // அனுமதி விதிகள் மாதிரி கட்டமைப்பு கோப்பு
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // இங்கே வகை கட்டமைக்கவும் என்பது
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```
## பயன்பாட்டைப் பற்றிய விளக்கம்

### உள்ளீடு

```php
# உள்ளீடு
use teamones\casbin\Enforcer;
```

### இரண்டு பயன்பாடுகள்

```php
# 1. இயல்புநிலை உபயோகத்திற்கு இயல்புநிலையாக பயன்படுத்துக
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. தனிப்பட்ட rbac அமைப்பைப் பயன்படுத்துக
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### பொதுவான பயன்பாட்டின் அறிமுகம்

மேலும் அதிக API பயன்பாட்டுகளைப் பார்வையிட அனைவரும் தணிக்கையால் சென்றுக்கொள்ளவும்.

- மேல்வழிமுறைப் பயன்பாடுகள்: https://casbin.org/docs/zh-CN/management-api
- RBAC API: https://casbin.org/docs/zh-CN/rbac-api

```php
# பயனாளருக்கு அனுமதியைச் சேர்க்கவும்

Enforcer::addPermissionForUser('user1', '/user', 'read');

# ஒரு பயனாளரின் அனுமதியை நீக்குக

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# பயனாளருக்கு அனைத்து அனுமதிகளையும் பெறுதல்

Enforcer::getPermissionsForUser('user1'); 

# பயனாளருக்கு பங்குகொடுப்பைச் சேர்க்கவும்

Enforcer::addRoleForUser('user1', 'role1');

# பங்குகொடுப்புக்கு அனுமதியைச் சேர்க்கவும்

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# அனைத்து பங்குகளையும் பெறுக

Enforcer::getAllRoles();

# பயனாளரின் அனைத்து பங்குகளையும் பெறுக

Enforcer::getRolesForUser('user1');

# பங்குகளுக்கு அட்டவணை போல பயனானவர்களைப் பெறுக

Enforcer::getUsersForRole('role1');

# பயனாளர் ஒரு பங்குக்கு உள்ளதாக உள்ளாரா என்பதைச் சொல்லுக

Enforcer::hasRoleForUser('use1', 'role1');

# பயனாளரின் பங்கை நீக்குக

Enforcer::deleteRoleForUser('use1', 'role1');

# பயனாளரின் அனைத்து பங்குகளையும் நீக்குக

Enforcer::deleteRolesForUser('use1');

# பங்கை நீக்குக

Enforcer::deleteRole('role1');

# அனுமதியை நீக்குக

Enforcer::deletePermission('/user', 'read');

# பயனாய்வாளர் அல்லது பங்குக்கு உள்ளீட்டக்கான அனைத்து அனுமதிகளையும் நீக்குக

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# அனுமதி சரிபார்க்கவும், true அல்லது false பின்பற்றுக

Enforcer::enforce("user1", "/user", "edit");
```
