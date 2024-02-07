# वेबमन

## विवरण

वेबमैन एक शक्तिशाली और अच्छी तरह से व्यायामी तक पहुंचने का नियंत्रण फ्रेमवर्क है, जिसकी अनुमति प्रबंधन यांत्रिकी विभिन्न एक्सेस कंट्रोल मॉडल का समर्थन करती है।
  
## परियोजना कोड

https://github.com/teamones-open/casbin

## स्थापना
 
  ```php
  composer require teamones/casbin
  ```

## Casbin आधिकारिक वेबसाइट

विस्तृत उपयोग के लिए वेबसाइट पर जाकर आप आधिकारिक चीनी दस्तावेज़ देख सकते हैं, यहां सिर्फ वेबमैन में कैस्बिन का उपयोग कैसे कॉन्फ़िगर करना है उसकी चर्चा की गई है

https://casbin.org/docs/zh-CN/overview

## निर्देशिका संरचना

```  
.
├── config                        कॉन्फ़िगरेशन निर्देशिका
│   ├── casbin-restful-model.conf प्रयोग करने वाले आधिकारिक मॉडल कॉन्फ़िगरेशन फ़ाइल
│   ├── casbin.php                कैस्बिन कॉन्फ़िगरेशन
......
├── database                      डेटाबेस फ़ाइल
│   ├── migrations                वंशानुक्रमणी फ़ाइलें
│   │   └── 20210218074218_create_rule_table.php
......
```

## डेटाबेस माइग्रेशन फ़ाइल

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * परिवर्तन विधि।
     *
     * इस विधि का प्रयोग करके अपनी पुनराप्ती माइग्रेशन लिखें।
     *
     * माइग्रेशन लेखन पर अधिक जानकारी यहां उपलब्ध है:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
............
```

## कैस्बिन कॉन्फ़िगरेशन

आधिकार सूत्र मॉडल कॉन्फ़िगरेशन विधि के लिए देखें: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // आधिकार सूत्र मॉडल कॉन्फ़िगरेशन फ़ाइल
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // मॉडल या एडाप्टर
            'class' => \app\model\Rule::class,
        ],
    ],
    // कई आजमा आधिकार मॉडल कॉन्फ़िगर किया जा सकता है
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // आधिकार सूत्र मॉडल कॉन्फ़िगरेशन फ़ाइल
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // मॉडल या एडाप्टर
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### एडाप्टर

वर्तमान कम्पोसर एडाप्टर में एक सोच-orm का मॉडल का विधि किया गया है, अन्य orm के लिए कृपया vendor/teamones/src/adapters/DatabaseAdapter.php को देखें

फिर कॉन्फ़िगरेशन में परिवर्तन करें

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // आधिकार सूत्र मॉडल कॉन्फ़िगरेशन फ़ाइल
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // यहां प्रकार को एडाप्टर मोड में कॉन्फ़िगर किया गया है
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
```

## उपयोग की निर्देशिका

### आयात

```php
# इम्पोर्ट करें
use teamones\casbin\Enforcer;
```

### दो तरह का उपयोग

```php
# 1. डिफ़ॉल्ट कॉन्फ़िगरेशन का उपयोग करें
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. अपनी विशेष rbac कॉन्फ़िगरेशन का उपयोग करें
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### उपयोगी API संक्षेप

अधिक API उपयोग के लिए कृपया आधिकारिक वेबसाइट देखें

- प्रबंधन API: https://casbin.org/docs/zh-CN/management-api
- RBAC API: https://casbin.org/docs/zh-CN/rbac-api

```php
# उपयोगकर्ता के लिए अनुमति जोड़ें

Enforcer::addPermissionForUser('user1', '/user', 'read');

# एक उपयोगकर्ता की अनुमति हटाएं

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# उपयोगकर्ता की सभी अनुमतियां प्राप्त करें

Enforcer::getPermissionsForUser('user1'); 

# उपयोगकर्ता के लिए भूमिका जोड़ें

Enforcer::addRoleForUser('user1', 'role1');

# भूमिका के लिए अनुमति जोड़ें

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# सभी भूमिकाएँ प्राप्त करें

Enforcer::getAllRoles();

# उपयोगकर्ता की सभी भूमिकाएँ प्राप्त करें

Enforcer::getRolesForUser('user1');

# भूमिका के आधार पर उपयोगकर्ता प्राप्त करें

Enforcer::getUsersForRole('role1');

# उपयोगकर्ता के लिए एक भूमिका का पता लगाएं

Enforcer::hasRoleForUser('use1', 'role1');

# उपयोगकर्ता भूमिका हटाएं

Enforcer::deleteRoleForUser('use1', 'role1');

# उपयोगकर्ता की सभी भूमिकाएँ हटाएं

Enforcer::deleteRolesForUser('use1');

# भूमिका हटाएं

Enforcer::deleteRole('role1');

# अनुमति हटाएं

Enforcer::deletePermission('/user', 'read');

# उपयोगकर्ता या भूमिका की सभी अनुमतियां हटाएं

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# अनुमति की जांच करें, true या false लौटाएं

Enforcer::enforce("user1", "/user", "edit");
```
