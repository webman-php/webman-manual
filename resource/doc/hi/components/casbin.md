# वेबमैन

## विवरण

वेबमैन एक शक्तिशाली, दक्षिणपंथी खुला पहुंच नियंत्रण ढांचा है, जिसका अनुमति प्रबंधन तंत्र समर्थन विभिन्न पहुंच नियंत्रण मॉडल को।
  
## प्रोजेक्ट लिंक

https://github.com/teamones-open/casbin

## स्थापना
 
  ```php
  composer require teamones/casbin
  ```

## Casbin आधिकारिक वेबसाइट

विस्तृत उपयोग के लिए आप आधिकारिक चीनी दस्तावेज़ देख सकते हैं, यहां केवल वेबमैन में कैसबिन को कैसे समाकृत करने के बारे में बताया जा रहा है

https://casbin.org/docs/zh-CN/overview

## निर्देशिका संरचना

```
.
├── कॉन्फ़िग                        कॉन्फ़िगरेशन निर्देशिका
│   ├── casbin-restful-model.conf    इस्तेमाल होने वाली अनुमति मॉडल कॉन्फ़िगरेशन फ़ाइल
│   ├── casbin.php                  casbin कॉन्फ़िगरेशन
......
├── डेटाबेस                        डेटाबेस फ़ाइल
│   ├── migrations                 स्थानांतरण फ़ाइल
│   │   └── 20210218074218_create_rule_table.php
......
```

## डेटाबेस स्थानांतरण फ़ाइल

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * बदलाव पद्धति
     *
     * इस पद्धति का उपयोग करके अपनी पलटावनीय माइग्रेशन लिखें।
     *
     * और जानकारी लिखने के लिए यहां क्लिक करें:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * इस पद्धति में निम्नलिखित कमांड का उपयोग किया जा सकता है और फिंक्स आपको स्वचालित रूप से उन्हें उल्टाने के प्रयास करते हैं जब आप उन्हें वापस कर रहे होते हैं:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * किसी भी अन्य विनाशकारी परिवर्तन से परेशानी होगी जब कोशिश करेंगे करीगर रोलबैक करने के लिए।
     *
     * ध्यान दें कि "create()" या "update()" को कॉल करें न कि "save()" जब आप टेबल कक्षा के साथ काम कर रहे होते हैं।
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'नियम तालिका']);

        //डेटा फ़ील्ड जोड़ें
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'प्राइमरी की ID'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'नियम प्रकार'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //निर्देशित क्रिएट करें
        $table->create();
    }
}

```

## कैसबिन कॉन्फ़िगरेशन

अनुमति नियम मॉडल कॉन्फ़ीगरेशन वाक्य संख्या के लिए देखें: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // अनुमति नियम मॉडल कॉन्फ़ीगरेशन फ़ाइल
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // मॉडल या एडाप्टर
            'class' => \app\model\Rule::class,
        ],
    ],
    // गैर डिफ़ॉल्ट अनुमति मॉडल कॉन्फ़िगर किया जा सकता है
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // अनुमति नियम मॉडल कॉन्फ़ीगरेशन फ़ाइल
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // मॉडल या एडाप्टर
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### अनुकूलक

वर्तमान कम्पोजर एडाप्टर रूपांतरित करने के लिए think-orm के मॉडल विधि का उपयोग करता है, अन्य orm के लिए कृपया देखें vendor/teamones/src/adapters/DatabaseAdapter.php

फिर कॉन्फ़िगरेशन को बदलें

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // अनुमति नियम मॉडल कॉन्फ़ीगरेशन फ़ाइल
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // यहां प्रकार अनुकूलक मोड में कॉन्फ़िगर
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## उपयोग निर्देश

### आयात

```php
# आयात
use teamones\casbin\Enforcer;
```

### दो प्रकार का उपयोग

```php
# 1. डिफ़ॉल्ट सेटिंग का उपयोग करें
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. अपनी अनुकूल rbac सेटिंग का उपयोग करें
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### लोकप्रिय एपीआई परिचय

अधिक एपीआई उपयोग के लिए कृपया आधिकारिक साइट पर जाएं

- प्रबंधन एपीआई: https://casbin.org/docs/zh-CN/management-api
- RBAC एपीआई: https://casbin.org/docs/zh-CN/rbac-api

```php
# उपयोगकर्ता के लिए अनुमतियां जोड़ें

Enforcer::addPermissionForUser('user1', '/user', 'read');

# एक उपयोगकर्ता की अनुमतियां हटाएं

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# उपयोगकर्ता की सभी अनुमतियां प्राप्त करें

Enforcer::getPermissionsForUser('user1'); 

# उपयोगकर्ता के लिए भूमिका जोड़ें

Enforcer::addRoleForUser('user1', 'role1');

# भूमिका के लिए अनुमतियां जोड़ें

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# सभी भूमिकाएं प्राप्त करें

Enforcer::getAllRoles();

# उपयोगकर्ता के लिए सभी भूमिकाएं प्राप्त करें

Enforcer::getRolesForUser('user1');

# भूमिका के आधार पर उपयोगकर्ताओं को प्राप्त करें

Enforcer::getUsersForRole('role1');

# जांचें कि उपयोगकर्ता किसी भूमिका का हिस्सा है या नहीं

Enforcer::hasRoleForUser('use1', 'role1');

# उपयोगकर्ता भूमिका हटाएं

Enforcer::deleteRoleForUser('use1', 'role1');

# उपयोगकर्ता की सभी भूमिकाएं हटाएं

Enforcer::deleteRolesForUser('use1');

# भूमिका हटाएं

Enforcer::deleteRole('role1');

# अनुमति हटाएं

Enforcer::deletePermission('/user', 'read');

# उपयोगकर्ता या भूमिका की सभी अनुमतियां हटाएं

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# अनुमति की जांच, true या false लौटाएं

Enforcer::enforce("user1", "/user", "edit");
```
