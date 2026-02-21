# Autres validateurs

Il existe de nombreux validateurs disponibles dans Composer qui peuvent être utilisés directement, tels que :

#### <a href="#webman-validation"> webman/validation (Recommandé)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# Validateur webman/validation

Basé sur `illuminate/validation`, il fournit la validation manuelle, la validation par annotation, la validation au niveau des paramètres et des ensembles de règles réutilisables.

## Installation

```bash
composer require webman/validation
```

## Concepts de base

- **Réutilisation des ensembles de règles** : Définir des `rules`, `messages`, `attributes` et `scenes` réutilisables en étendant `support\validation\Validator`, qui peuvent être réutilisés dans la validation manuelle et par annotation.
- **Validation par annotation au niveau de la méthode (Attribute)** : Utiliser l'attribut PHP 8 `#[Validate]` pour lier la validation aux méthodes du contrôleur.
- **Validation par annotation au niveau des paramètres (Attribute)** : Utiliser l'attribut PHP 8 `#[Param]` pour lier la validation aux paramètres des méthodes du contrôleur.
- **Gestion des exceptions** : Lance `support\validation\ValidationException` en cas d'échec de validation ; la classe d'exception est configurable.
- **Validation en base de données** : Si la validation en base de données est impliquée, vous devez installer `composer require webman/database`.

## Validation manuelle

### Utilisation de base

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **Note**
> `validate()` lance `support\validation\ValidationException` lorsque la validation échoue. Si vous préférez ne pas lancer d'exceptions, utilisez l'approche `fails()` ci-dessous pour obtenir les messages d'erreur.

### Messages et attributs personnalisés

```php
use support\validation\Validator;

$data = ['contact' => 'user@example.com'];

Validator::make(
    $data,
    ['contact' => 'required|email'],
    ['contact.email' => 'Format d\'email invalide'],
    ['contact' => 'Email']
)->validate();
```

### Valider sans exception (obtenir les messages d'erreur)

Si vous préférez ne pas lancer d'exceptions, utilisez `fails()` pour vérifier et obtenir les messages d'erreur via `errors()` (retourne `MessageBag`) :

```php
use support\validation\Validator;

$data = ['email' => 'bad-email'];

$validator = Validator::make($data, [
    'email' => 'required|email',
]);

if ($validator->fails()) {
    $firstError = $validator->errors()->first();      // string
    $allErrors = $validator->errors()->all();         // array
    $errorsByField = $validator->errors()->toArray(); // array
    // gérer les erreurs...
}
```

## Réutilisation des ensembles de règles (Validateur personnalisé)

```php
namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:1',
        'name' => 'required|string|min:2|max:20',
        'email' => 'required|email',
    ];

    protected array $messages = [
        'name.required' => 'Le nom est obligatoire',
        'email.required' => 'L\'email est obligatoire',
        'email.email' => 'Format d\'email invalide',
    ];

    protected array $attributes = [
        'name' => 'Nom',
        'email' => 'Email',
    ];
}
```

### Réutilisation de la validation manuelle

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### Utiliser les scènes (Optionnel)

`scenes` est une fonctionnalité optionnelle ; elle ne valide qu'un sous-ensemble de champs lorsque vous appelez `withScene(...)`.

```php
namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:1',
        'name' => 'required|string|min:2|max:20',
        'email' => 'required|email',
    ];

    protected array $scenes = [
        'create' => ['name', 'email'],
        'update' => ['id', 'name', 'email'],
    ];
}
```

```php
use app\validation\UserValidator;

// Aucune scène spécifiée -> valider toutes les règles
UserValidator::make($data)->validate();

// Scène spécifiée -> valider uniquement les champs de cette scène
UserValidator::make($data)->withScene('create')->validate();
```

## Validation par annotation (niveau méthode)

### Règles directes

```php
use support\Request;
use support\validation\annotation\Validate;

class AuthController
{
    #[Validate(
        rules: [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ],
        messages: [
            'email.required' => 'L\'email est obligatoire',
            'password.required' => 'Le mot de passe est obligatoire',
        ],
        attributes: [
            'email' => 'Email',
            'password' => 'Mot de passe',
        ]
    )]
    public function login(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Réutilisation des ensembles de règles

```php
use app\validation\UserValidator;
use support\Request;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create')]
    public function create(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Superpositions multiples de validation

```php
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(rules: ['email' => 'required|email'])]
    #[Validate(rules: ['token' => 'required|string'])]
    public function send()
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Source des données de validation

```php
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(
        rules: ['email' => 'required|email'],
        in: ['query', 'body', 'path']
    )]
    public function send()
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

Utilisez le paramètre `in` pour spécifier la source des données :

* **query** Paramètres de requête HTTP, depuis `$request->get()`
* **body** Corps de la requête HTTP, depuis `$request->post()`
* **path** Paramètres de chemin de la requête HTTP, depuis `$request->route->param()`

`in` peut être une chaîne ou un tableau ; lorsqu'il s'agit d'un tableau, les valeurs sont fusionnées dans l'ordre, les valeurs ultérieures écrasant les précédentes. Lorsque `in` n'est pas passé, la valeur par défaut est `['query', 'body', 'path']`.


## Validation au niveau des paramètres (Param)

### Utilisation de base

```php
use support\validation\annotation\Param;

class MailController
{
    public function send(
        #[Param(rules: 'required|email')] string $from,
        #[Param(rules: 'required|email')] string $to,
        #[Param(rules: 'required|string|min:1|max:500')] string $content
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Source des données de validation

De même, la validation au niveau des paramètres prend en charge le paramètre `in` pour spécifier la source :

```php
use support\validation\annotation\Param;

class MailController
{
    public function send(
        #[Param(rules: 'required|email', in: ['body'])] string $from
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```


### rules accepte une chaîne ou un tableau

```php
use support\validation\annotation\Param;

class MailController
{
    public function send(
        #[Param(rules: ['required', 'email'])] string $from
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Messages / attribut personnalisés

```php
use support\validation\annotation\Param;

class UserController
{
    public function updateEmail(
        #[Param(
            rules: 'required|email',
            messages: ['email.email' => 'Format d\'email invalide'],
            attribute: 'Email'
        )]
        string $email
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Réutilisation des constantes de règles

```php
final class ParamRules
{
    public const EMAIL = ['required', 'email'];
}

class UserController
{
    public function send(
        #[Param(rules: ParamRules::EMAIL)] string $email
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

## Combinaison niveau méthode + niveau paramètre

```php
use support\Request;
use support\validation\annotation\Param;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(rules: ['token' => 'required|string'])]
    public function send(
        Request $request,
        #[Param(rules: 'required|email')] string $from,
        #[Param(rules: 'required|integer')] int $id
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

## Inférence automatique des règles (basée sur la signature des paramètres)

Lorsque `#[Validate]` est utilisé sur une méthode, ou lorsqu'un paramètre de cette méthode utilise `#[Param]`, ce composant **infère et complète automatiquement les règles de validation de base à partir de la signature des paramètres de la méthode**, puis les fusionne avec les règles existantes avant la validation.

### Exemple : expansion équivalente de `#[Validate]`

1) Activer uniquement `#[Validate]` sans écrire les règles manuellement :

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate]
    public function create(string $content, int $uid)
    {
    }
}
```

Équivalent à :

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'required|string',
        'uid' => 'required|integer',
    ])]
    public function create(string $content, int $uid)
    {
    }
}
```

2) Règles partielles écrites, le reste complété par la signature des paramètres :

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'min:2',
    ])]
    public function create(string $content, int $uid)
    {
    }
}
```

Équivalent à :

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'required|string|min:2',
        'uid' => 'required|integer',
    ])]
    public function create(string $content, int $uid)
    {
    }
}
```

3) Valeur par défaut / type nullable :

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate]
    public function create(string $content = 'default', ?int $uid = null)
    {
    }
}
```

Équivalent à :

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'string',
        'uid' => 'integer|nullable',
    ])]
    public function create(string $content = 'default', ?int $uid = null)
    {
    }
}
```

## Gestion des exceptions

### Exception par défaut

L'échec de validation lance `support\validation\ValidationException` par défaut, qui étend `Webman\Exception\BusinessException` et ne journalise pas les erreurs.

Le comportement de réponse par défaut est géré par `BusinessException::render()` :

- Requêtes normales : retourne un message sous forme de chaîne, ex. `token is required.`
- Requêtes JSON : retourne une réponse JSON, ex. `{"code": 422, "msg": "token is required.", "data":....}`

### Personnaliser la gestion via une exception personnalisée

- Configuration globale : `exception` dans `config/plugin/webman/validation/app.php`

## Support multilingue

Le composant inclut des packs de langue chinois et anglais intégrés et prend en charge les remplacements au niveau du projet. Ordre de chargement :

1. Pack de langue du projet `resource/translations/{locale}/validation.php`
2. Pack intégré du composant `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Anglais intégré d'Illuminate (repli)

> **Note**
> La langue par défaut de Webman est configurée dans `config/translation.php`, ou peut être modifiée via `locale('en');`.

### Exemple de remplacement local

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## Chargement automatique du middleware

Après l'installation, le composant charge automatiquement le middleware de validation via `config/plugin/webman/validation/middleware.php` ; aucune inscription manuelle n'est nécessaire.

## Génération en ligne de commande

Utilisez la commande `make:validator` pour générer des classes de validateur (sortie par défaut dans le répertoire `app/validation`).

> **Note**
> Nécessite `composer require webman/console`

### Utilisation de base

- **Générer un modèle vide**

```bash
php webman make:validator UserValidator
```

- **Écraser un fichier existant**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### Générer les règles à partir de la structure de la table

- **Spécifier le nom de la table pour générer les règles de base** (infère `$rules` à partir du type de champ/nullable/longueur etc. ; exclut les champs liés à l'ORM par défaut : laravel utilise `created_at/updated_at/deleted_at`, thinkorm utilise `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **Spécifier la connexion à la base de données** (scénarios multi-connexions)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Scènes

- **Générer les scènes CRUD** : `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> La scène `update` inclut le champ clé primaire (pour localiser les enregistrements) plus les autres champs ; `delete/detail` incluent uniquement la clé primaire par défaut.

### Sélection ORM (laravel (illuminate/database) vs think-orm)

- **Sélection automatique (par défaut)** : Utilise celui qui est installé/configuré ; lorsque les deux existent, utilise illuminate par défaut
- **Forcer la spécification**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### Exemple complet

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## Tests unitaires

Depuis le répertoire racine de `webman/validation`, exécutez :

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## Référence des règles de validation

<a name="available-validation-rules"></a>
## Règles de validation disponibles

> [!IMPORTANT]
> - Webman Validation est basé sur `illuminate/validation` ; les noms des règles correspondent à Laravel et il n'y a pas de règles spécifiques à Webman.
> - Le middleware valide les données de `$request->all()` (GET+POST) fusionnées avec les paramètres de route par défaut, à l'exclusion des fichiers uploadés ; pour les règles de fichiers, fusionnez `$request->file()` dans les données vous-même, ou appelez `Validator::make` manuellement.
> - `current_password` dépend du garde d'authentification ; `exists`/`unique` dépendent de la connexion à la base de données et du query builder ; ces règles sont indisponibles lorsque les composants correspondants ne sont pas intégrés.

La liste suivante présente toutes les règles de validation disponibles et leurs objectifs :

<style>
    .collection-method-list > p {
        columns: 10.8em 3; -moz-columns: 10.8em 3; -webkit-columns: 10.8em 3;
    }

    .collection-method-list a {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

#### Booléen

<div class="collection-method-list" markdown="1">

[Accepted](#rule-accepted)
[Accepted If](#rule-accepted-if)
[Boolean](#rule-boolean)
[Declined](#rule-declined)
[Declined If](#rule-declined-if)

</div>

#### Chaîne

<div class="collection-method-list" markdown="1">

[Active URL](#rule-active-url)
[Alpha](#rule-alpha)
[Alpha Dash](#rule-alpha-dash)
[Alpha Numeric](#rule-alpha-num)
[Ascii](#rule-ascii)
[Confirmed](#rule-confirmed)
[Current Password](#rule-current-password)
[Different](#rule-different)
[Doesnt Start With](#rule-doesnt-start-with)
[Doesnt End With](#rule-doesnt-end-with)
[Email](#rule-email)
[Ends With](#rule-ends-with)
[Enum](#rule-enum)
[Hex Color](#rule-hex-color)
[In](#rule-in)
[IP Address](#rule-ip)
[IPv4](#rule-ipv4)
[IPv6](#rule-ipv6)
[JSON](#rule-json)
[Lowercase](#rule-lowercase)
[MAC Address](#rule-mac)
[Max](#rule-max)
[Min](#rule-min)
[Not In](#rule-not-in)
[Regular Expression](#rule-regex)
[Not Regular Expression](#rule-not-regex)
[Same](#rule-same)
[Size](#rule-size)
[Starts With](#rule-starts-with)
[String](#rule-string)
[Uppercase](#rule-uppercase)
[URL](#rule-url)
[ULID](#rule-ulid)
[UUID](#rule-uuid)

</div>

#### Numérique

<div class="collection-method-list" markdown="1">

[Between](#rule-between)
[Decimal](#rule-decimal)
[Different](#rule-different)
[Digits](#rule-digits)
[Digits Between](#rule-digits-between)
[Greater Than](#rule-gt)
[Greater Than Or Equal](#rule-gte)
[Integer](#rule-integer)
[Less Than](#rule-lt)
[Less Than Or Equal](#rule-lte)
[Max](#rule-max)
[Max Digits](#rule-max-digits)
[Min](#rule-min)
[Min Digits](#rule-min-digits)
[Multiple Of](#rule-multiple-of)
[Numeric](#rule-numeric)
[Same](#rule-same)
[Size](#rule-size)

</div>

#### Tableau

<div class="collection-method-list" markdown="1">

[Array](#rule-array)
[Between](#rule-between)
[Contains](#rule-contains)
[Doesnt Contain](#rule-doesnt-contain)
[Distinct](#rule-distinct)
[In Array](#rule-in-array)
[In Array Keys](#rule-in-array-keys)
[List](#rule-list)
[Max](#rule-max)
[Min](#rule-min)
[Size](#rule-size)

</div>

#### Date

<div class="collection-method-list" markdown="1">

[After](#rule-after)
[After Or Equal](#rule-after-or-equal)
[Before](#rule-before)
[Before Or Equal](#rule-before-or-equal)
[Date](#rule-date)
[Date Equals](#rule-date-equals)
[Date Format](#rule-date-format)
[Different](#rule-different)
[Timezone](#rule-timezone)

</div>

#### Fichier

<div class="collection-method-list" markdown="1">

[Between](#rule-between)
[Dimensions](#rule-dimensions)
[Encoding](#rule-encoding)
[Extensions](#rule-extensions)
[File](#rule-file)
[Image](#rule-image)
[Max](#rule-max)
[MIME Types](#rule-mimetypes)
[MIME Type By File Extension](#rule-mimes)
[Size](#rule-size)

</div>

#### Base de données

<div class="collection-method-list" markdown="1">

[Exists](#rule-exists)
[Unique](#rule-unique)

</div>

#### Utilitaires

<div class="collection-method-list" markdown="1">

[Any Of](#rule-anyof)
[Bail](#rule-bail)
[Exclude](#rule-exclude)
[Exclude If](#rule-exclude-if)
[Exclude Unless](#rule-exclude-unless)
[Exclude With](#rule-exclude-with)
[Exclude Without](#rule-exclude-without)
[Filled](#rule-filled)
[Missing](#rule-missing)
[Missing If](#rule-missing-if)
[Missing Unless](#rule-missing-unless)
[Missing With](#rule-missing-with)
[Missing With All](#rule-missing-with-all)
[Nullable](#rule-nullable)
[Present](#rule-present)
[Present If](#rule-present-if)
[Present Unless](#rule-present-unless)
[Present With](#rule-present-with)
[Present With All](#rule-present-with-all)
[Prohibited](#rule-prohibited)
[Prohibited If](#rule-prohibited-if)
[Prohibited If Accepted](#rule-prohibited-if-accepted)
[Prohibited If Declined](#rule-prohibited-if-declined)
[Prohibited Unless](#rule-prohibited-unless)
[Prohibits](#rule-prohibits)
[Required](#rule-required)
[Required If](#rule-required-if)
[Required If Accepted](#rule-required-if-accepted)
[Required If Declined](#rule-required-if-declined)
[Required Unless](#rule-required-unless)
[Required With](#rule-required-with)
[Required With All](#rule-required-with-all)
[Required Without](#rule-required-without)
[Required Without All](#rule-required-without-all)
[Required Array Keys](#rule-required-array-keys)
[Sometimes](#validating-when-present)

</div>

<a name="rule-accepted"></a>
#### accepted

Le champ doit être `"yes"`, `"on"`, `1`, `"1"`, `true`, ou `"true"`. Couramment utilisé pour des scénarios comme la vérification de l'accord de l'utilisateur aux conditions d'utilisation.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

Lorsqu'un autre champ est égal à la valeur spécifiée, le champ doit être `"yes"`, `"on"`, `1`, `"1"`, `true`, ou `"true"`. Couramment utilisé pour les scénarios d'accord conditionnel.

<a name="rule-active-url"></a>
#### active_url

Le champ doit avoir un enregistrement A ou AAAA valide. Cette règle utilise d'abord `parse_url` pour extraire l'hôte de l'URL, puis valide avec `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

Le champ doit être une valeur postérieure à la date donnée. La date est passée à `strtotime` pour être convertie en `DateTime` valide :

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

Vous pouvez également passer un autre nom de champ pour la comparaison :

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

Vous pouvez utiliser le constructeur fluent de la règle `date` :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->after(\Carbon\Carbon::today()->addDays(7)),
    ],
])->validate();
```

`afterToday` et `todayOrAfter` expriment commodément « doit être après aujourd'hui » ou « doit être aujourd'hui ou plus tard » :

```php
Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->afterToday(),
    ],
])->validate();
```

<a name="rule-after-or-equal"></a>
#### after_or_equal:_date_

Le champ doit être à la date donnée ou après. Voir [after](#rule-after) pour plus de détails.

Vous pouvez utiliser le constructeur fluent de la règle `date` :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->afterOrEqual(\Carbon\Carbon::today()->addDays(7)),
    ],
])->validate();
```

<a name="rule-anyof"></a>
#### anyOf

`Rule::anyOf` permet de spécifier « satisfaire l'un des ensembles de règles ». Par exemple, la règle suivante signifie que `username` doit être soit une adresse email, soit une chaîne alphanumérique/tiret souligné/tiret d'au moins 6 caractères :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'username' => [
        'required',
        Rule::anyOf([
            ['string', 'email'],
            ['string', 'alpha_dash', 'min:6'],
        ]),
    ],
])->validate();
```

<a name="rule-alpha"></a>
#### alpha

Le champ doit être des lettres Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) et [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

Pour autoriser uniquement ASCII (`a-z`, `A-Z`), ajoutez l'option `ascii` :

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

Le champ ne peut contenir que des lettres et chiffres Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), plus le tiret ASCII (`-`) et le tiret souligné (`_`).

Pour autoriser uniquement ASCII (`a-z`, `A-Z`, `0-9`), ajoutez l'option `ascii` :

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

Le champ ne peut contenir que des lettres et chiffres Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)).

Pour autoriser uniquement ASCII (`a-z`, `A-Z`, `0-9`), ajoutez l'option `ascii` :

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

Le champ doit être un tableau PHP `array`.

Lorsque la règle `array` a des paramètres supplémentaires, les clés du tableau d'entrée doivent être dans la liste des paramètres. Dans l'exemple, la clé `admin` n'est pas dans la liste autorisée, donc elle est invalide :

```php
use support\validation\Validator;

$input = [
    'user' => [
        'name' => 'Taylor Otwell',
        'username' => 'taylorotwell',
        'admin' => true,
    ],
];

Validator::make($input, [
    'user' => 'array:name,username',
])->validate();
```

Il est recommandé de définir explicitement les clés de tableau autorisées dans les projets réels.

<a name="rule-ascii"></a>
#### ascii

Le champ ne peut contenir que des caractères ASCII 7 bits.

<a name="rule-bail"></a>
#### bail

Arrêter la validation des règles suivantes pour le champ lorsque la première règle échoue.

Cette règle n'affecte que le champ actuel. Pour « arrêter au premier échec globalement », utilisez directement le validateur Illuminate et appelez `stopOnFirstFailure()`.

<a name="rule-before"></a>
#### before:_date_

Le champ doit être avant la date donnée. La date est passée à `strtotime` pour être convertie en `DateTime` valide. Comme [after](#rule-after), vous pouvez passer un autre nom de champ pour la comparaison.

Vous pouvez utiliser le constructeur fluent de la règle `date` :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->before(\Carbon\Carbon::today()->subDays(7)),
    ],
])->validate();
```

`beforeToday` et `todayOrBefore` expriment commodément « doit être avant aujourd'hui » ou « doit être aujourd'hui ou plus tôt » :

```php
Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->beforeToday(),
    ],
])->validate();
```

<a name="rule-before-or-equal"></a>
#### before_or_equal:_date_

Le champ doit être à la date donnée ou avant. La date est passée à `strtotime` pour être convertie en `DateTime` valide. Comme [after](#rule-after), vous pouvez passer un autre nom de champ pour la comparaison.

Vous pouvez utiliser le constructeur fluent de la règle `date` :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->beforeOrEqual(\Carbon\Carbon::today()->subDays(7)),
    ],
])->validate();
```

<a name="rule-between"></a>
#### between:_min_,_max_

La taille du champ doit être entre _min_ et _max_ (inclus). L'évaluation pour les chaînes, nombres, tableaux et fichiers est la même que [size](#rule-size).

<a name="rule-boolean"></a>
#### boolean

Le champ doit être convertible en booléen. Les entrées acceptables incluent `true`, `false`, `1`, `0`, `"1"`, `"0"`.

Utilisez le paramètre `strict` pour autoriser uniquement `true` ou `false` :

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

Le champ doit avoir un champ correspondant `{field}_confirmation`. Par exemple, lorsque le champ est `password`, `password_confirmation` est requis.

Vous pouvez également spécifier un nom de champ de confirmation personnalisé, ex. `confirmed:repeat_username` exige que `repeat_username` corresponde au champ actuel.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

Le champ doit être un tableau et doit contenir toutes les valeurs de paramètres données. Cette règle est couramment utilisée pour la validation de tableaux ; vous pouvez utiliser `Rule::contains` pour la construire :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'roles' => [
        'required',
        'array',
        Rule::contains(['admin', 'editor']),
    ],
])->validate();
```

<a name="rule-doesnt-contain"></a>
#### doesnt_contain:_foo_,_bar_,...

Le champ doit être un tableau et ne doit pas contenir l'une des valeurs de paramètres données. Vous pouvez utiliser `Rule::doesntContain` pour la construire :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'roles' => [
        'required',
        'array',
        Rule::doesntContain(['admin', 'editor']),
    ],
])->validate();
```

<a name="rule-current-password"></a>
#### current_password

Le champ doit correspondre au mot de passe de l'utilisateur authentifié actuel. Vous pouvez spécifier le garde d'authentification comme premier paramètre :

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> Cette règle dépend du composant d'authentification et de la configuration du garde ; ne pas utiliser lorsque l'authentification n'est pas intégrée.

<a name="rule-date"></a>
#### date

Le champ doit être une date valide (non relative) reconnaissable par `strtotime`.

<a name="rule-date-equals"></a>
#### date_equals:_date_

Le champ doit être égal à la date donnée. La date est passée à `strtotime` pour être convertie en `DateTime` valide.

<a name="rule-date-format"></a>
#### date_format:_format_,...

Le champ doit correspondre à l'un des formats donnés. Utilisez soit `date` soit `date_format`. Cette règle prend en charge tous les formats PHP [DateTime](https://www.php.net/manual/en/class.datetime.php).

Vous pouvez utiliser le constructeur fluent de la règle `date` :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->format('Y-m-d'),
    ],
])->validate();
```

<a name="rule-decimal"></a>
#### decimal:_min_,_max_

Le champ doit être numérique avec le nombre de décimales requis :

```php
Validator::make($data, [
    'price' => 'decimal:2',
])->validate();

Validator::make($data, [
    'price' => 'decimal:2,4',
])->validate();
```

<a name="rule-declined"></a>
#### declined

Le champ doit être `"no"`, `"off"`, `0`, `"0"`, `false`, ou `"false"`.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

Lorsqu'un autre champ est égal à la valeur spécifiée, le champ doit être `"no"`, `"off"`, `0`, `"0"`, `false`, ou `"false"`.

<a name="rule-different"></a>
#### different:_field_

Le champ doit être différent de _field_.

<a name="rule-digits"></a>
#### digits:_value_

Le champ doit être un entier de longueur _value_.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

Le champ doit être un entier de longueur entre _min_ et _max_.

<a name="rule-dimensions"></a>
#### dimensions

Le champ doit être une image et satisfaire les contraintes de dimension :

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

Contraintes disponibles : _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

_ratio_ est le rapport d'aspect ; il peut être exprimé en fraction ou en décimal :

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

Cette règle a de nombreux paramètres ; il est recommandé d'utiliser `Rule::dimensions` pour la construire :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'avatar' => [
        'required',
        Rule::dimensions()
            ->maxWidth(1000)
            ->maxHeight(500)
            ->ratio(3 / 2),
    ],
])->validate();
```

<a name="rule-distinct"></a>
#### distinct

Lors de la validation des tableaux, les valeurs du champ ne doivent pas être dupliquées :

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

Utilise la comparaison souple par défaut. Ajoutez `strict` pour une comparaison stricte :

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

Ajoutez `ignore_case` pour ignorer les différences de casse :

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

Le champ ne doit pas commencer par l'une des valeurs spécifiées.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

Le champ ne doit pas se terminer par l'une des valeurs spécifiées.

<a name="rule-email"></a>
#### email

Le champ doit être une adresse email valide. Cette règle dépend de [egulias/email-validator](https://github.com/egulias/EmailValidator), utilise `RFCValidation` par défaut et peut utiliser d'autres méthodes de validation :

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

Méthodes de validation disponibles :

<div class="content-list" markdown="1">

- `rfc` : `RFCValidation` - Valider l'email selon la spécification RFC ([RFC pris en charge](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict` : `NoRFCWarningsValidation` - Échouer sur les avertissements RFC (ex. point final ou points consécutifs).
- `dns` : `DNSCheckValidation` - Vérifier si le domaine a des enregistrements MX valides.
- `spoof` : `SpoofCheckValidation` - Empêcher les caractères Unicode homographes ou d'usurpation.
- `filter` : `FilterEmailValidation` - Valider en utilisant PHP `filter_var`.
- `filter_unicode` : `FilterEmailValidation::unicode()` - Validation `filter_var` autorisant Unicode.

</div>

Vous pouvez utiliser le constructeur fluent de règles :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        'required',
        Rule::email()
            ->rfcCompliant(strict: false)
            ->validateMxRecord()
            ->preventSpoofing(),
    ],
])->validate();
```

> [!WARNING]
> `dns` et `spoof` nécessitent l'extension PHP `intl`.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

Le champ doit correspondre à l'encodage de caractères spécifié. Cette règle utilise `mb_check_encoding` pour détecter l'encodage du fichier ou de la chaîne. Peut être utilisée avec le constructeur de règles de fichier :

```php
use Illuminate\Validation\Rules\File;
use support\validation\Validator;

Validator::make($data, [
    'attachment' => [
        'required',
        File::types(['csv'])->encoding('utf-8'),
    ],
])->validate();
```

<a name="rule-ends-with"></a>
#### ends_with:_foo_,_bar_,...

Le champ doit se terminer par l'une des valeurs spécifiées.

<a name="rule-enum"></a>
#### enum

`Enum` est une règle basée sur une classe pour valider que la valeur du champ est une valeur enum valide. Passez le nom de la classe enum lors de la construction. Pour les valeurs primitives, utilisez Backed Enum :

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

Utilisez `only`/`except` pour restreindre les valeurs enum :

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [
        Rule::enum(ServerStatus::class)
            ->only([ServerStatus::Pending, ServerStatus::Active]),
    ],
])->validate();

Validator::make($data, [
    'status' => [
        Rule::enum(ServerStatus::class)
            ->except([ServerStatus::Pending, ServerStatus::Active]),
    ],
])->validate();
```

Utilisez `when` pour des restrictions conditionnelles :

```php
use app\Enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [
        Rule::enum(ServerStatus::class)->when(
            $isAdmin,
            fn ($rule) => $rule->only(ServerStatus::Active),
            fn ($rule) => $rule->only(ServerStatus::Pending),
        ),
    ],
])->validate();
```

<a name="rule-exclude"></a>
#### exclude

Le champ sera exclu des données retournées par `validate`/`validated`.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

Lorsque _anotherfield_ est égal à _value_, le champ sera exclu des données retournées par `validate`/`validated`.

Pour des conditions complexes, utilisez `Rule::excludeIf` :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'role_id' => Rule::excludeIf($isAdmin),
])->validate();

Validator::make($data, [
    'role_id' => Rule::excludeIf(fn () => $isAdmin),
])->validate();
```

<a name="rule-exclude-unless"></a>
#### exclude_unless:_anotherfield_,_value_

Sauf si _anotherfield_ est égal à _value_, le champ sera exclu des données retournées par `validate`/`validated`. Si _value_ est `null` (ex. `exclude_unless:name,null`), le champ n'est conservé que lorsque le champ de comparaison est `null` ou absent.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

Lorsque _anotherfield_ existe, le champ sera exclu des données retournées par `validate`/`validated`.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

Lorsque _anotherfield_ n'existe pas, le champ sera exclu des données retournées par `validate`/`validated`.

<a name="rule-exists"></a>
#### exists:_table_,_column_

Le champ doit exister dans la table de base de données spécifiée.

<a name="basic-usage-of-exists-rule"></a>
#### Utilisation de base de la règle Exists

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

Lorsque `column` n'est pas spécifié, le nom du champ est utilisé par défaut. Cet exemple valide donc si la colonne `state` existe dans la table `states`.

<a name="specifying-a-custom-column-name"></a>
#### Spécifier un nom de colonne personnalisé

Ajoutez le nom de la colonne après le nom de la table :

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

Pour spécifier une connexion à la base de données, préfixez le nom de la table avec le nom de la connexion :

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

Vous pouvez également passer un nom de classe de modèle ; le framework résoudra le nom de la table :

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

Pour des conditions de requête personnalisées, utilisez le constructeur `Rule` :

```php
use Illuminate\Database\Query\Builder;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        'required',
        Rule::exists('staff')->where(function (Builder $query) {
            $query->where('account_id', 1);
        }),
    ],
])->validate();
```

Vous pouvez également spécifier le nom de la colonne directement dans `Rule::exists` :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

Pour valider qu'un ensemble de valeurs existe, combinez avec la règle `array` :

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

Lorsque `array` et `exists` sont présents, une seule requête valide toutes les valeurs.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

Valide que l'extension du fichier uploadé est dans la liste autorisée :

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> Ne vous fiez pas uniquement à l'extension pour la validation du type de fichier ; utilisez avec [mimes](#rule-mimes) ou [mimetypes](#rule-mimetypes).

<a name="rule-file"></a>
#### file

Le champ doit être un fichier uploadé avec succès.

<a name="rule-filled"></a>
#### filled

Lorsque le champ existe, sa valeur ne doit pas être vide.

<a name="rule-gt"></a>
#### gt:_field_

Le champ doit être supérieur au _field_ ou _value_ donné. Les deux champs doivent avoir le même type. L'évaluation pour les chaînes, nombres, tableaux et fichiers est la même que [size](#rule-size).

<a name="rule-gte"></a>
#### gte:_field_

Le champ doit être supérieur ou égal au _field_ ou _value_ donné. Les deux champs doivent avoir le même type. L'évaluation pour les chaînes, nombres, tableaux et fichiers est la même que [size](#rule-size).

<a name="rule-hex-color"></a>
#### hex_color

Le champ doit être une [valeur de couleur hexadécimale](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) valide.

<a name="rule-image"></a>
#### image

Le champ doit être une image (jpg, jpeg, png, bmp, gif ou webp).

> [!WARNING]
> SVG n'est pas autorisé par défaut en raison du risque XSS. Pour l'autoriser, ajoutez `allow_svg` : `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

Le champ doit être dans la liste de valeurs donnée. Vous pouvez utiliser `Rule::in` pour construire :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'zones' => [
        'required',
        Rule::in(['first-zone', 'second-zone']),
    ],
])->validate();
```

Lorsqu'il est combiné avec la règle `array`, chaque valeur du tableau d'entrée doit être dans la liste `in` :

```php
use support\validation\Rule;
use support\validation\Validator;

$input = [
    'airports' => ['NYC', 'LAS'],
];

Validator::make($input, [
    'airports' => [
        'required',
        'array',
    ],
    'airports.*' => Rule::in(['NYC', 'LIT']),
])->validate();
```

<a name="rule-in-array"></a>
#### in_array:_anotherfield_.*

Le champ doit exister dans la liste de valeurs de _anotherfield_.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

Le champ doit être un tableau et doit contenir au moins l'une des valeurs données comme clé :

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

Le champ doit être un entier.

Utilisez le paramètre `strict` pour exiger que le type du champ soit entier ; les entiers sous forme de chaîne seront invalides :

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> Cette règle valide uniquement si elle passe le `FILTER_VALIDATE_INT` de PHP ; pour les types numériques stricts, utilisez avec [numeric](#rule-numeric).

<a name="rule-ip"></a>
#### ip

Le champ doit être une adresse IP valide.

<a name="rule-ipv4"></a>
#### ipv4

Le champ doit être une adresse IPv4 valide.

<a name="rule-ipv6"></a>
#### ipv6

Le champ doit être une adresse IPv6 valide.

<a name="rule-json"></a>
#### json

Le champ doit être une chaîne JSON valide.

<a name="rule-lt"></a>
#### lt:_field_

Le champ doit être inférieur au _field_ donné. Les deux champs doivent avoir le même type. L'évaluation pour les chaînes, nombres, tableaux et fichiers est la même que [size](#rule-size).

<a name="rule-lte"></a>
#### lte:_field_

Le champ doit être inférieur ou égal au _field_ donné. Les deux champs doivent avoir le même type. L'évaluation pour les chaînes, nombres, tableaux et fichiers est la même que [size](#rule-size).

<a name="rule-lowercase"></a>
#### lowercase

Le champ doit être en minuscules.

<a name="rule-list"></a>
#### list

Le champ doit être un tableau liste. Les clés du tableau liste doivent être des nombres consécutifs de 0 à `count($array) - 1`.

<a name="rule-mac"></a>
#### mac_address

Le champ doit être une adresse MAC valide.

<a name="rule-max"></a>
#### max:_value_

Le champ doit être inférieur ou égal à _value_. L'évaluation pour les chaînes, nombres, tableaux et fichiers est la même que [size](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

Le champ doit être un entier dont la longueur ne dépasse pas _value_.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

Valide que le type MIME du fichier est dans la liste :

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

Le type MIME est deviné en lisant le contenu du fichier et peut différer du MIME fourni par le client.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

Valide que le type MIME du fichier correspond à l'extension donnée :

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

Bien que les paramètres soient des extensions, cette règle lit le contenu du fichier pour déterminer le MIME. Correspondance extension-MIME :

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### Types MIME et extensions

Cette règle ne valide pas que « l'extension du fichier » corresponde au « MIME réel ». Par exemple, `mimes:png` considère `photo.txt` avec du contenu PNG comme valide. Pour valider l'extension, utilisez [extensions](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

Le champ doit être supérieur ou égal à _value_. L'évaluation pour les chaînes, nombres, tableaux et fichiers est la même que [size](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

Le champ doit être un entier dont la longueur n'est pas inférieure à _value_.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

Le champ doit être un multiple de _value_.

<a name="rule-missing"></a>
#### missing

Le champ ne doit pas exister dans les données d'entrée.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

Lorsque _anotherfield_ est égal à l'une des _value_, le champ ne doit pas exister.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

Sauf si _anotherfield_ est égal à l'une des _value_, le champ ne doit pas exister.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

Lorsque l'un des champs spécifiés existe, le champ ne doit pas exister.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

Lorsque tous les champs spécifiés existent, le champ ne doit pas exister.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

Le champ ne doit pas être dans la liste de valeurs donnée. Vous pouvez utiliser `Rule::notIn` pour construire :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'toppings' => [
        'required',
        Rule::notIn(['sprinkles', 'cherries']),
    ],
])->validate();
```

<a name="rule-not-regex"></a>
#### not_regex:_pattern_

Le champ ne doit pas correspondre à l'expression régulière donnée.

Cette règle utilise PHP `preg_match`. La regex doit avoir des délimiteurs, ex. `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> Lors de l'utilisation de `regex`/`not_regex`, si la regex contient `|`, utilisez la forme tableau pour éviter les conflits avec le séparateur `|`.

<a name="rule-nullable"></a>
#### nullable

Le champ peut être `null`.

<a name="rule-numeric"></a>
#### numeric

Le champ doit être [numérique](https://www.php.net/manual/en/function.is-numeric.php).

Utilisez le paramètre `strict` pour autoriser uniquement les types entier ou float ; les chaînes numériques seront invalides :

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

Le champ doit exister dans les données d'entrée.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

Lorsque _anotherfield_ est égal à l'une des _value_, le champ doit exister.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

Sauf si _anotherfield_ est égal à l'une des _value_, le champ doit exister.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

Lorsque l'un des champs spécifiés existe, le champ doit exister.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

Lorsque tous les champs spécifiés existent, le champ doit exister.

<a name="rule-prohibited"></a>
#### prohibited

Le champ doit être absent ou vide. « Vide » signifie :

<div class="content-list" markdown="1">

- La valeur est `null`.
- La valeur est une chaîne vide.
- La valeur est un tableau vide ou un objet Countable vide.
- Fichier uploadé avec chemin vide.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

Lorsque _anotherfield_ est égal à l'une des _value_, le champ doit être absent ou vide. « Vide » signifie :

<div class="content-list" markdown="1">

- La valeur est `null`.
- La valeur est une chaîne vide.
- La valeur est un tableau vide ou un objet Countable vide.
- Fichier uploadé avec chemin vide.

</div>

Pour des conditions complexes, utilisez `Rule::prohibitedIf` :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'role_id' => Rule::prohibitedIf($isAdmin),
])->validate();

Validator::make($data, [
    'role_id' => Rule::prohibitedIf(fn () => $isAdmin),
])->validate();
```

<a name="rule-prohibited-if-accepted"></a>
#### prohibited_if_accepted:_anotherfield_,...

Lorsque _anotherfield_ est `"yes"`, `"on"`, `1`, `"1"`, `true`, ou `"true"`, le champ doit être absent ou vide.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

Lorsque _anotherfield_ est `"no"`, `"off"`, `0`, `"0"`, `false`, ou `"false"`, le champ doit être absent ou vide.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

Sauf si _anotherfield_ est égal à l'une des _value_, le champ doit être absent ou vide. « Vide » signifie :

<div class="content-list" markdown="1">

- La valeur est `null`.
- La valeur est une chaîne vide.
- La valeur est un tableau vide ou un objet Countable vide.
- Fichier uploadé avec chemin vide.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

Lorsque le champ existe et n'est pas vide, tous les champs de _anotherfield_ doivent être absents ou vides. « Vide » signifie :

<div class="content-list" markdown="1">

- La valeur est `null`.
- La valeur est une chaîne vide.
- La valeur est un tableau vide ou un objet Countable vide.
- Fichier uploadé avec chemin vide.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

Le champ doit correspondre à l'expression régulière donnée.

Cette règle utilise PHP `preg_match`. La regex doit avoir des délimiteurs, ex. `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> Lors de l'utilisation de `regex`/`not_regex`, si la regex contient `|`, utilisez la forme tableau pour éviter les conflits avec le séparateur `|`.

<a name="rule-required"></a>
#### required

Le champ doit exister et ne pas être vide. « Vide » signifie :

<div class="content-list" markdown="1">

- La valeur est `null`.
- La valeur est une chaîne vide.
- La valeur est un tableau vide ou un objet Countable vide.
- Fichier uploadé avec chemin vide.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

Lorsque _anotherfield_ est égal à l'une des _value_, le champ doit exister et ne pas être vide.

Pour des conditions complexes, utilisez `Rule::requiredIf` :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'role_id' => Rule::requiredIf($isAdmin),
])->validate();

Validator::make($data, [
    'role_id' => Rule::requiredIf(fn () => $isAdmin),
])->validate();
```

<a name="rule-required-if-accepted"></a>
#### required_if_accepted:_anotherfield_,...

Lorsque _anotherfield_ est `"yes"`, `"on"`, `1`, `"1"`, `true`, ou `"true"`, le champ doit exister et ne pas être vide.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

Lorsque _anotherfield_ est `"no"`, `"off"`, `0`, `"0"`, `false`, ou `"false"`, le champ doit exister et ne pas être vide.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

Sauf si _anotherfield_ est égal à l'une des _value_, le champ doit exister et ne pas être vide. Si _value_ est `null` (ex. `required_unless:name,null`), le champ peut être vide uniquement lorsque le champ de comparaison est `null` ou absent.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

Lorsque l'un des champs spécifiés existe et n'est pas vide, le champ doit exister et ne pas être vide.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

Lorsque tous les champs spécifiés existent et ne sont pas vides, le champ doit exister et ne pas être vide.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

Lorsque l'un des champs spécifiés est vide ou absent, le champ doit exister et ne pas être vide.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

Lorsque tous les champs spécifiés sont vides ou absents, le champ doit exister et ne pas être vide.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

Le champ doit être un tableau et doit contenir au moins les clés spécifiées.

<a name="validating-when-present"></a>
#### sometimes

Appliquer les règles de validation suivantes uniquement lorsque le champ existe. Couramment utilisé pour les champs « optionnels mais doivent être valides lorsqu'ils sont présents » :

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

Le champ doit être identique à _field_.

<a name="rule-size"></a>
#### size:_value_

La taille du champ doit être égale à la _value_ donnée. Pour les chaînes : nombre de caractères ; pour les nombres : entier spécifié (à utiliser avec `numeric` ou `integer`) ; pour les tableaux : nombre d'éléments ; pour les fichiers : taille en Ko. Exemple :

```php
Validator::make($data, [
    'title' => 'size:12',
    'seats' => 'integer|size:10',
    'tags' => 'array|size:5',
    'image' => 'file|size:512',
])->validate();
```

<a name="rule-starts-with"></a>
#### starts_with:_foo_,_bar_,...

Le champ doit commencer par l'une des valeurs spécifiées.

<a name="rule-string"></a>
#### string

Le champ doit être une chaîne. Pour autoriser `null`, utilisez avec `nullable`.

<a name="rule-timezone"></a>
#### timezone

Le champ doit être un identifiant de fuseau horaire valide (depuis `DateTimeZone::listIdentifiers`). Vous pouvez passer les paramètres pris en charge par cette méthode :

```php
Validator::make($data, [
    'timezone' => 'required|timezone:all',
])->validate();

Validator::make($data, [
    'timezone' => 'required|timezone:Africa',
])->validate();

Validator::make($data, [
    'timezone' => 'required|timezone:per_country,US',
])->validate();
```

<a name="rule-unique"></a>
#### unique:_table_,_column_

Le champ doit être unique dans la table spécifiée.

**Spécifier un nom de table/colonne personnalisé :**

Vous pouvez spécifier directement le nom de la classe du modèle :

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

Vous pouvez spécifier le nom de la colonne (par défaut le nom du champ lorsqu'il n'est pas spécifié) :

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**Spécifier la connexion à la base de données :**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**Ignorer un ID spécifié :**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        'required',
        Rule::unique('users')->ignore($user->id),
    ],
])->validate();
```

> [!WARNING]
> `ignore` ne doit pas recevoir d'entrée utilisateur ; utilisez uniquement des ID uniques générés par le système (ID auto-incrémenté ou UUID du modèle), sinon un risque d'injection SQL peut exister.

Vous pouvez également passer une instance de modèle :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

Si la clé primaire n'est pas `id`, spécifiez le nom de la clé primaire :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

Par défaut utilise le nom du champ comme colonne unique ; vous pouvez également spécifier le nom de la colonne :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**Ajouter des conditions supplémentaires :**

```php
use Illuminate\Database\Query\Builder;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->where(
            fn (Builder $query) => $query->where('account_id', 1)
        ),
    ],
])->validate();
```

**Ignorer les enregistrements supprimés en douceur :**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

Si la colonne de suppression douce n'est pas `deleted_at` :

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

Le champ doit être en majuscules.

<a name="rule-url"></a>
#### url

Le champ doit être une URL valide.

Vous pouvez spécifier les protocoles autorisés :

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

Le champ doit être un [ULID](https://github.com/ulid/spec) valide.

<a name="rule-uuid"></a>
#### uuid

Le champ doit être un UUID RFC 9562 valide (version 1, 3, 4, 5, 6, 7 ou 8).

Vous pouvez spécifier la version :

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# Validateur top-think/think-validate

## Description

Validateur officiel ThinkPHP

## URL du projet

https://github.com/top-think/think-validate

## Installation

`composer require topthink/think-validate`

## Démarrage rapide

**Créer `app/index/validate/User.php`**

```php
<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
        'email' => 'email',    
    ];

    protected $message  =   [
        'name.require' => 'Le nom est obligatoire',
        'name.max'     => 'Le nom ne peut pas dépasser 25 caractères',
        'age.number'   => 'L\'âge doit être un nombre',
        'age.between'  => 'L\'âge doit être compris entre 1 et 120',
        'email'        => 'Format email incorrect',    
    ];

}
```

**Utilisation**

```php
$data = [
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
];

$validate = new \app\index\validate\User;

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

> **Note**
> webman ne prend pas en charge la méthode `Validate::rule()` de think-validate

<a name="respect-validation"></a>
# Validateur workerman/validation

## Description

Ce projet est une version localisée de https://github.com/Respect/Validation

## URL du projet

https://github.com/walkor/validation


## Installation

```php
composer require workerman/validation
```

## Démarrage rapide

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use support\Db;

class IndexController
{
    public function index(Request $request)
    {
        $data = v::input($request->post(), [
            'nickname' => v::length(1, 64)->setName('Surnom'),
            'username' => v::alnum()->length(5, 64)->setName('Nom d\'utilisateur'),
            'password' => v::length(5, 64)->setName('Mot de passe')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```

**Accès via jQuery**

  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```

Résultat :

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

Explication :

`v::input(array $input, array $rules)` valide et collecte les données. En cas d'échec de la validation, elle lance `Respect\Validation\Exceptions\ValidationException` ; en cas de succès, elle retourne les données validées (tableau).

Si le code métier ne capture pas l'exception de validation, le framework webman la capturera et retournera du JSON (comme `{"code":500, "msg":"xxx"}`) ou une page d'exception normale selon les en-têtes HTTP. Si le format de réponse ne correspond pas à vos besoins, vous pouvez capturer `ValidationException` et retourner des données personnalisées, comme dans l'exemple ci-dessous :

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

class IndexController
{
    public function index(Request $request)
    {
        try {
            $data = v::input($request->post(), [
                'username' => v::alnum()->length(5, 64)->setName('Nom d\'utilisateur'),
                'password' => v::length(5, 64)->setName('Mot de passe')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

## Guide du validateur

```php
use Respect\Validation\Validator as v;

// Validation d'une seule règle
$number = 123;
v::numericVal()->validate($number); // true

// Validation en chaîne
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Obtenir la première raison d'échec de validation
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// Obtenir toutes les raisons d'échec de validation
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Affichera
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // Affichera
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// Messages d'erreur personnalisés
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username may only contain letters and numbers',
        'noWhitespace' => 'Username must not contain spaces',
        'length' => 'length satisfies the rule, so this will not be shown'
    ]);
    // Affichera 
    // array(
    //    'alnum' => 'Username may only contain letters and numbers',
    //    'noWhitespace' => 'Username must not contain spaces'
    // )
}

// Valider un objet
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Valider un tableau
$data = [
    'parentKey' => [
        'field1' => 'value1',
        'field2' => 'value2'
        'field3' => true,
    ]
];
v::key(
    'parentKey',
    v::key('field1', v::stringType())
        ->key('field2', v::stringType())
        ->key('field3', v::boolType())
    )
    ->assert($data); // Peut également utiliser check() ou validate()
  
// Validation optionnelle
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Règle de négation
v::not(v::intVal())->validate(10); // false
```

## Différence entre les méthodes du validateur `validate()` `check()` `assert()`

`validate()` retourne un booléen, ne lance pas d'exception

`check()` lance une exception en cas d'échec de validation ; obtenir la première raison d'échec via `$exception->getMessage()`

`assert()` lance une exception en cas d'échec de validation ; obtenir toutes les raisons d'échec via `$exception->getFullMessage()`


## Règles de validation courantes

`Alnum()` Uniquement lettres et chiffres

`Alpha()` Uniquement lettres

`ArrayType()` Type tableau

`Between(mixed $minimum, mixed $maximum)` Valide que l'entrée est entre deux valeurs.

`BoolType()` Type booléen

`Contains(mixed $expectedValue)` Valide que l'entrée contient une certaine valeur

`ContainsAny(array $needles)` Valide que l'entrée contient au moins une valeur définie

`Digit()` Valide que l'entrée ne contient que des chiffres

`Domain()` Valide un nom de domaine valide

`Email()` Valide une adresse email valide

`Extension(string $extension)` Valide l'extension du fichier

`FloatType()` Type float

`IntType()` Type entier

`Ip()` Valide une adresse IP

`Json()` Valide des données JSON

`Length(int $min, int $max)` Valide que la longueur est dans l'intervalle

`LessThan(mixed $compareTo)` Valide que la longueur est inférieure à la valeur donnée

`Lowercase()` Valide les minuscules

`MacAddress()` Valide une adresse MAC

`NotEmpty()` Valide non vide

`NullType()` Valide null

`Number()` Valide un nombre

`ObjectType()` Type objet

`StringType()` Type chaîne

`Url()` Valide une URL

Voir https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ pour plus de règles de validation.

## Plus

Visitez https://respect-validation.readthedocs.io/en/2.0/

