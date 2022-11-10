# Quick Start

webmanmore references base [Eloquent ORM](https://laravel.com/docs/7.x/eloquent) 。Each database table has a corresponding「model」to interact with that table。You can query data in a data table through the model，Other database componentsInsertNew record。

Before you start, make sure you have configured the database connection in `config/database.php`。

> Note: Eloquent ORM requires additional imports to support model observers`composer require "illuminate/events"` [example](#Model Observer)

## Examples
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Name of the table associated with the model
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Redefine primary key, default isid
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Indicate if timestamp is automatically maintained
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Table Name
You can specify a custom data table by defining the table attribute on the model：
```php
class User extends Model
{
    /**
     * Name of the table associated with the model
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Primary Key
Eloquent  will also assume that each data table has a primary key column named id. You can define a protected $primaryKey property to override the convention。
```php
class User extends Model
{
    /**
     * Redefine primary key, default isid
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent AssumePrimary Keyis a self-incrementing integer value，This means that by defaultPrimary KeyExcept for template files int Type。if you want to use non-incremental or non-numericPrimary Keythen you need to set the public $incrementing a powerful false
```php
class User extends Model
{
    /**
     * Indicates whether the model primary key is incremented
     *
     * @var bool
     */
    public $incrementing = false;
}
```

If your primary key is not an integer, you need to set the protected $keyType property on the model to  string：
```php
class User extends Model
{
    /**
     * Auto-increment ID type”。
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Timestamp
By default, Eloquent expects you to have created_at and updated_at in your data table. If you do not want Eloquent to manage these two columns automatically, set the $timestamps property in the model to  false：
```php
class User extends Model
{
    /**
     * Indicate if timestamp is automatically maintained
     *
     * @var bool
     */
    public $timestamps = false;
}
```
If you need to customizeTimestampformat，set in your model $dateFormat Properties。This property determines how the date property is stored in the database，and the model is serialized to an array or JSON format：
```php
class User extends Model
{
    /**
     * Timestamp Storage Format
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

If you need to customize the field names for storing timestamps, you can do so by setting the values of the CREATED_AT and UPDATED_AT constants in the model：
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Database Connection
use reference，Eloquent The model will use the defaults configured by your applicationDatabase Connection。If you want to specify a different connection for the model，set $connection Properties：
```php
class User extends Model
{
    /**
     * Connection name of the model
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Default Property Value
To define default values for some attributes of the model, define the $attributes attribute on the model：
```php
class User extends Model
{
    /**
     * Default property values for models。
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Model Search
After creating the model and its associated database table, you can query the data from the database. Think of each Eloquent model as a powerful query constructor that you can use to query the data tables associated with it more quickly. For example, ：
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> hint：because Eloquent The model is also a query constructor，So you should also read [provided fallback](queries.md) Production environment please use。Need to use Eloquent Use these methods in queries。

## Additional Constraints
Eloquent The all method returns all the results in the model. Since each Eloquent model acts as a query constructor, you can also add query criteria and use the get method to get the query results：
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Reload model
You can reload the model using the fresh and refresh methods. The fresh method will retrieve the model from the database again. Existing model instances are not affected：
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

refresh The method reassigns the existing model using the new data in the database. In addition, already loaded relations are reloaded：
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Collection
Eloquent 的 all 和 get methods can query multiple results，Return a `Illuminate\Database\Eloquent\Collection `instance。`Collection` The class provides a large number of helper functions to handle this Eloquent result：
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Use cursor
cursor method allows you to traverse the database using a cursor, which performs the query only once. The cursor method can greatly reduce memory usage when dealing with large amounts of data：
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

cursor return `Illuminate\Support\LazyCollection` instance。 [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) and execute the class Laravel Collectionversion belowCollectionMethod，and only a single model will be loaded into memory at a time：
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Selects subquery
Eloquent Default action issubquerysupport，You can use a single query statement to extract information from the relevant table。to instantiate，Suppose we have a destination table destinations and a flight schedule to the destination flights。flights Add a before  arrival_at field，Indicates when the flight will reach its destination。

UsagesubqueryBase plugin creation select 和 addSelect Method，We can query all destinations with a single statement destinations，and the name of the last flight to each destination：
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```
## Sort by subquery
in addition，function to manipulate orderBy If not foundsubquery。We can use this function to sort all destinations based on the time of arrival of the last flight。 same，This allows to execute only a single query against the database：
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Retrieve individual models/collections
In addition to retrieving all records from the specified data table，Classes must be implemented find、 first 或 firstWhere method to retrieve a single record。These methods return a single model instance，instead of returning the modelCollection：
```php
// Find a model by primary key...
$flight = app\model\Flight::find(1);

// Find the first model that matches the query criteria...
$flight = app\model\Flight::where('active', 1)->first();

// A quick implementation to find the first model that matches the query criteria...
$flight = app\model\Flight::firstWhere('active', 1);
```

You can also call the find method with an array of primary keys as an argument and it will return a collection of matching records：
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Sometimes you may want to perform other actions when you look for the first result but can't find a value. firstOr method will return the first result when it is found, and if not, it will execute the given callback. The return value of the callback will be the return value of the firstOr method：
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
firstOr The method also accepts an array of columns to query：
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```
## 「"Not found" exception
Sometimes you want to throw an exception if the model is not found。This is very useful in controllers and routing。 findOrFail 和 firstOrFail The method retrieves the first result of the query，Advanced，will throw Illuminate\Database\Eloquent\ModelNotFoundException exception：
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## Retrieve collection
You can also use the query constructor provided by count、 sum 和 max Method，Template engineCollectionNormal arrays are fineCollection。These methods will only return the appropriate scalar value and not a model instance：
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## Insert
To add a new record to the database, first create a new model instance, set properties to the instance, and then call the save method：
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Add a new record to the user table
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate Request

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

created_at and updated_at timestamps will be set automatically (when the $timestamps property in the model is true), no need to assign them manually。


## Update
save Methods can also be usedUpdateThe database already exists for the model。Updatemodel，You need to retrieve it first，Set toUpdateof the property，Property set to save Method。same， updated_at Timestampwill automaticallyUpdate，so there is no need to manually assign values either：
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Bulk Update
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom'']);
```

## Check for attribute changes
Eloquent Provides isDirty, isClean and wasChanged methods to check the internal state of a model and determine how its properties have changed since it was originally loaded。
isDirty  method determines if any properties have been changed since the model was loaded. You can pass a specific property name to determine if a particular property has become dirty. isClean method is the opposite of isDirty, it also accepts optional property arguments：
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';

$user->isDirty(); // true
$user->isDirty('title'); // true
$user->isDirty('first_name'); // false

$user->isClean(); // false
$user->isClean('title'); // false
$user->isClean('first_name'); // true

$user->save();

$user->isDirty(); // false
$user->isClean(); // true
```
wasChanged Method to determine if any properties were changed the last time the model was saved during the current request cycle. You can also pass the property name to see if a specific property has changed：
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';
$user->save();

$user->wasChanged(); // true
$user->wasChanged('title'); // true
$user->wasChanged('first_name'); // false

```
## Bulk Assignment
We can pass create method to save the new model。 This method will return the model instance。 However，Model Based，You need to specify on the model fillable 或 guarded Properties，used in the view Eloquent Models are not available by defaultBulk Assignment。

when the user passes in unexpectedly via a request HTTP Parameter，and the parameter changes a field in the database that you don't need to change，Required to injectBulk Assignmentloophole。 for example：malicious users may pass HTTP Same effect is_admin Parameter，The framework will automatically pass to create Method，This action allows users to upgrade themselves to administrator。

so，attributes to achieve，You should define which properties on the model are available to be Bulk Assignment的。You can pass the model on $fillable attributes can be。 example：让 Flight model of name With the continuous movement toBulk Assignment：

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Properties that can be assigned in bulk。
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```
Once we have set it up we canBulk Assignmentof the property，and in the example create MethodInsertMultiple processes instantiate multiple times。 create method will return the saved model instance：
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
If you already have a model instance, you can pass an array to the fill method to assign values：
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable  can be thought of as a whitelist for bulk assignments, and you can also use the $guarded attribute for this purpose. The $guarded attribute contains an array of values that are not allowed to be assigned in bulk. That is, $guarded will function more like a blacklist. Note: You can only use one of $fillable or $guarded, not both. In the example below, all properties other than the price property can be bulk-assigned ：
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Non-batch-assignable property。
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

If you want to make all properties bulk-assignable, you can define $guarded as an empty array：
```php
/**
 * Non-batch-assignable property。
 *
 * @var array
 */
protected $guarded = [];
```

## Other creation methods
firstOrCreate/ firstOrNew
Here are two you might use forBulk Assignmentmethod of： firstOrCreate 和 firstOrNew。 firstOrCreate The method will pass the given key / value pairs to match data in the database。If the model is not found in the database，then willInsertunder file，which contains the attributes of the first argument and optionally the attributes of the second argument。

firstOrNew  method tries to find the record in the database by the given attribute just like the firstOrCreate method. However, if the firstOrNew method does not find the corresponding model, a new model instance is returned. Note that the model instance returned by firstOrNew is not yet saved to the database, you need to manually call the save method to save the ：

```php
// Retrieve flights by name and create them if they don't exist...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// Retrieve flights by name, or create them using the name and delayed attributes and arrival_time attribute...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Retrieve flights by name, create an instance if none exist...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// Retrieve flights by name, or create a model instance using the name and delayed attributes and the arrival_time attribute...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

```

You may also encounter hopeUpdatescenarios where existing models or new models are created if they do not exist。 updateOrCreate method to implement in one step。 similar to firstOrCreate Method，updateOrCreate so don't put，Create the base plugin save()：
```php
// The price is set at $99 if there are flights from Oakland to San Diego。
// If no match exists for a model, create one。
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);

```

## Delete Model

You can call the delete method on a model instance to delete the instance：
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Delete model by primary key
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## Delete model by query
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Copy Model
You can use the replicate method to copy a new instance that has not been saved to the database, which works well when the model instances share many of the same properties。
```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Example Street',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'billing'
]);

$billing->save();

```

## Model comparison
Sometimes you may need to determine if two models are identical. is method can be used to quickly check if two models have the same primary key, table and database connection：
```php
if ($post->is($anotherPost)) {
    //
}
```


## Model Observer
otherwise use[Laravel The model event in  Observer
](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Note: Eloquent ORM requires additional imports to support model observerscomposer require "illuminate/events"

```php
<?php
namespace app\model;

use support\Model;
use app\observer\UserObserver;

class User extends Model
{
    public static function boot()
    {
        parent::boot();
        static::observe(UserObserver::class);
    }
}
```
