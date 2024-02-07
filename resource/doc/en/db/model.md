# Quick Start

webman is a high-performance PHP framework based on workerman. The webman model is based on Eloquent ORM. Each database table has a corresponding "model" to interact with the table. You can use the model to query data from a table and insert new records into the table.

Before getting started, make sure to configure the database connection in `config/database.php`.

> Note: To support model observers in Eloquent ORM, you need to import `composer require "illuminate/events"` [example](#model-observers).

## Example
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The primary key for the model
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Indicates if the model should be timestamped
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Table Name
You can specify a custom table for the model by defining the `table` property on the model:
```php
class User extends Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Primary Key
Eloquent assumes that each table has a primary key column named `id`. You can override this convention by defining a protected `$primaryKey` property:
```php
class User extends Model
{
    /**
     * The primary key for the model
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

By default, Eloquent assumes the primary key is an incrementing integer value, which means the primary key will be automatically cast to an `int` type. If you want to use a non-incrementing or non-numeric key, you can set the `$incrementing` property to `false` publicly:
```php
class User extends Model
{
    /**
     * Indicates if the model's primary key is incrementing
     *
     * @var bool
     */
    public $incrementing = false;
}
```

If your primary key is not an integer, you need to set the protected `$keyType` property on the model to `string`:
```php
class User extends Model
{
    /**
     * The "type" of the auto-incrementing ID
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Timestamps
By default, Eloquent expects your table to have `created_at` and `updated_at` columns. If you don't want Eloquent to automatically manage these columns, you can set the `$timestamps` property to `false` in the model:
```php
class User extends Model
{
    /**
     * Indicates if the model should be timestamped
     *
     * @var bool
     */
    public $timestamps = false;
}
```

If you need to customize the format of timestamps, you can set the `$dateFormat` property in your model. This property determines the storage format of date attributes in the database, as well as the format in which the model is serialized to an array or JSON:
```php
class User extends Model
{
    /**
     * The storage format of the model's date columns
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

If you need to customize the column names used for storing timestamps, you can set the values of the `CREATED_AT` and `UPDATED_AT` constants in the model:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Database Connection
By default, Eloquent models will use the default database connection configured for your application. If you want to specify a different connection for the model, you can set the `$connection` property:
```php
class User extends Model
{
    /**
     * The connection name for the model
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Default Attribute Values
If you want to define default values for some attributes of the model, you can set the `$attributes` property on the model:
```php
class User extends Model
{
    /**
     * The default attribute values for the model
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Model Retrieval
Once you have created a model and associated it with a database table, you can start querying data from the database. Think of each Eloquent model as a powerful query builder that allows you to quickly query the associated database table. For example:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Tip: Since Eloquent models are also query builders, you should also read through all the methods available for [Query Builders](queries.md). You can use these methods in Eloquent queries.

## Additional Constraints
The `all` method in Eloquent returns all results from the model. Since each Eloquent model also acts as a query builder, you can add query constraints and use the `get` method to retrieve the results:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Reloading Models
You can use the `fresh` and `refresh` methods to reload models. The `fresh` method will retrieve the model from the database again. The existing model instance remains unaffected:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```
The `refresh` method reassigns the existing model with new data from the database. Additionally, any loaded relationships will also be reloaded:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Collections
The `all` and `get` methods in Eloquent can retrieve multiple results and return an instance of `Illuminate\Database\Eloquent\Collection`. The `Collection` class provides a large number of helper functions to handle Eloquent results:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Using Cursors
The `cursor` method allows you to iterate through the database using a cursor, with only one query executed. When dealing with a large amount of data, the `cursor` method can greatly reduce memory usage:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```
The `cursor` method returns an instance of `Illuminate\Support\LazyCollection`. [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) allow you to use most of the collection methods available in Laravel, loading only one model into memory at a time:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Subqueries in Selects
Eloquent provides advanced support for subqueries, allowing you to extract information from related tables in a single query. For example, suppose we have a `destinations` table and a `flights` table that represents flights to destinations. The `flights` table contains an `arrival_at` field, indicating when the flight arrives at the destination.

Using the `select` and `addSelect` methods provided by the subquery feature, we can query all destinations and the name of the last flight to each destination in a single query:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Sorting by Subqueries
Additionally, the `orderBy` function in the query builder also supports subqueries. We can use this feature to sort all destinations by the time of the last flight arriving at each destination. Again, this can be achieved with a single query to the database:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```
## Retrieving a Single Model/Collection
In addition to retrieving all records from a specified table, you can use the find, first, or firstWhere methods to retrieve a single record. These methods return a single model instance instead of a collection of models:

```php
// Find a model by primary key...
$flight = app\model\Flight::find(1);

// Find the first model that matches the query...
$flight = app\model\Flight::where('active', 1)->first();

// A shortcut to find the first model that matches the query...
$flight = app\model\Flight::firstWhere('active', 1);
```

You can also pass an array of primary keys to the find method, which will return a collection of matching records:

```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Sometimes you may want to perform other actions when finding the first result but not finding a value. The firstOr method will return the first result when found, and if there is no result, it will execute the given callback. The return value of the callback will be the return value of the firstOr method:

```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
    // ...
});
```
The firstOr method also accepts an array of columns to query:

```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```
## "Model Not Found" Exception
Sometimes you may want to throw an exception when a model is not found. This is useful in controllers and routes. The findOrFail and firstOrFail methods will retrieve the first result of the query, and if not found, they will throw the Illuminate\Database\Eloquent\ModelNotFoundException exception:

```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## Retrieving Collections
You can also use the count, sum, and max methods provided by the query builder, as well as other collection functions, to operate on collections. These methods will only return the appropriate scalar value instead of a model instance:

```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## Inserting
To insert a new record into the database, create a new model instance, set the attributes on the instance, and then call the save method:

```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Add a new record to the user table.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate the request

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

The created_at and updated_at timestamps will be automatically set (if the $timestamps property is set to true in the model), so you don't need to assign them manually.


## Updating
The save method can also be used to update an existing model in the database. To update a model, you need to retrieve it, set the attributes to be updated, and then call the save method. Similarly, the updated_at timestamp will be automatically updated, so you don't need to assign it manually:

```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Batch Update
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom'']);
```

## Checking for Attribute Changes
Eloquent provides the isDirty, isClean, and wasChanged methods to check the internal state of a model and determine how its attributes have changed since they were initially loaded.
The isDirty method determines if any attributes have changed since the model was loaded. You can pass a specific attribute name to determine if a specific attribute has changed. The isClean method is the opposite of isDirty and also accepts an optional attribute parameter:

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
The wasChanged method determines if any attributes have changed since the last time the model was saved in the current request cycle. You can also pass an attribute name to see if a specific attribute has changed:

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
## Mass Assignment
You can also use the create method to save a new model. This method will return the model instance. However, before using it, you need to specify the fillable or guarded attributes on the model, as all Eloquent models are not allowed to be mass-assigned by default.

There is a mass assignment vulnerability when unexpected HTTP parameters are passed in by users, and these parameters change fields in the database that you did not intend to change. For example, a malicious user may pass in an is_admin parameter via an HTTP request and then pass it to the create method, allowing the user to upgrade themselves to an administrator.

So, before starting, you should define which attributes on the model can be mass-assigned. You can do this by using the $fillable property on the model. For example, let the name attribute of the Flight model be mass assignable:

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```
Once we have defined the attributes that can be mass assigned, we can use the create method to insert new data into the database. The create method will return the saved model instance:

```php
$flight = app\model\Flight::create(['name' => 'Flight 10']);
```
If you already have a model instance, you can pass an array to the fill method to assign values:

```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable can be seen as a "whitelist" for mass assignment, and you can achieve the same functionality using the $guarded property. The $guarded property contains an array of attributes that are not allowed to be mass assigned. In other words, $guarded will be more like a "blacklist" functionally. Note: You can only use either $fillable or $guarded, not both at the same time. In the following example, all attributes except price can be mass assigned:

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

If you want all attributes to be mass assignable, you can define $guarded as an empty array:

```php
/**
 * The attributes that aren't mass assignable.
 *
 * @var array
 */
protected $guarded = [];
```
## Other Creation Methods
firstOrCreate/ firstOrNew
Here are two methods you may use to perform bulk assignment: firstOrCreate and firstOrNew. The firstOrCreate method will attempt to locate a database record using the given key / value pairs. If the model can't be found in the database, a record will be inserted with the first parameter's attributes, plus any additional attributes that you pass.
 
The firstOrNew method is like the firstOrCreate method; however, if it can't find a matching model in the database, a new model instance will be returned. Note that the instance returned by firstOrNew has not yet been persisted to the database. You will need to call the save method manually to save it:
 
```php
// Retrieve the flight by the given attributes or create it if it doesn't exist...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// Retrieve the flight by the given attributes or create it with certain attributes...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Retrieve the flight by the given attributes or instantiate it if it doesn't exist...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// Retrieve the flight by the given attributes or instantiate it with certain attributes...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

```
You may also come across scenarios where you want to update an existing model if it exists, or create a new model if it doesn't. You may use the updateOrCreate method to achieve this in a single, convenient step. Like the firstOrCreate method, updateOrCreate persists the model, so there is no need to call save():
```php
// If a flight from Oakland to San Diego exists, set the price to $99.
// If the model does not exist, create it.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);

```

## Deleting Models
You may call the delete method on a model instance to delete the instance:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Deleting Models By Primary Key
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## Deleting Models By Query
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Copying Models
You may use the replicate method to create a new instance of a model that hasn't been saved to the database. This is convenient when you want to create a new model instance with many of the same attributes as an existing model instance:
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

## Comparing Models
Sometimes you may need to determine if two models are "equal" to each other. The is method can be used to quickly verify if two models have the same primary key, table, and database connection:
```php
if ($post->is($anotherPost)) {
    //
}
```

## Model Observers
Please refer to [Model Events and Observer in Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Note: Eloquent ORM requires an additional package "illuminate/events" to support model observers. You can import it with composer require "illuminate/events".

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
