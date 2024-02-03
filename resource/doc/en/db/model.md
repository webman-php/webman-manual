# Getting Started

webman Model is based on [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Each database table has a corresponding "model" to interact with the table. You can use the model to query data from the table and insert new records into the table.

Before getting started, make sure to configure the database connection in `config/database.php`.

> Note: To support model observers, additional import of `composer require "illuminate/events"` is required. [Example](#model-observers)

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
     * Override the default primary key "id"
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
You can specify a custom table by defining the table attribute on the model:
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
Eloquent assumes that each table has a primary key column named "id". You can override this convention by defining a protected $primaryKey attribute:
```php
class User extends Model
{
    /**
     * Override the default primary key "id"
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent assumes the primary key is an auto-incrementing integer, meaning that by default, the primary key will automatically be cast to an int type. If you want to use a non-incrementing or non-numeric primary key, you need to set the public $incrementing attribute to false:
```php
class User extends Model
{
    /**
     * Indicates if the model's primary key is auto-incrementing
     *
     * @var bool
     */
    public $incrementing = false;
}
```

If your primary key is not an integer, you need to set the protected $keyType attribute on the model to "string":
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
By default, Eloquent expects your table to have created_at and updated_at columns. If you don't want Eloquent to manage these columns automatically, set the $timestamps attribute in the model to false:
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

If you need to customize the format of the timestamps, you can set the $dateFormat attribute in your model. This attribute determines how date attributes are stored in the database and serialized to arrays or JSON format in the model:
```php
class User extends Model
{
    /**
     * The storage format of the timestamps
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

If you need to customize the field names for storing timestamps, you can set the values of CREATED_AT and UPDATED_AT constants in the model:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Database Connection
By default, Eloquent models will use the default database connection configured for your application. If you want to specify a different connection for the model, set the $connection attribute in the model:
```php
class User extends Model
{
    /**
     * The name of the connection for the model
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Default Attribute Values
If you want to define default values for certain attributes of the model, you can define the $attributes attribute on the model:
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
Once you have created a model and associated it with a database table, you can query data from the database. Think of each Eloquent model as a powerful query builder that allows you to quickly query the associated database table. For example:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Tip: Since Eloquent models also act as query builders, you should also read all the methods available in [Query Builder](queries.md). You can use these methods in Eloquent queries.

## Additional Constraints
The all method in Eloquent will return all results from the model. Since each Eloquent model acts as a query builder, you can also add query constraints and then use the get method to retrieve the query results:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Reloading Model
You can use the fresh and refresh methods to reload a model. The fresh method will retrieve the model from the database without affecting the existing model instance:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

The refresh method will reassign the existing model with new data from the database. Additionally, any loaded relationships will be reloaded:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Collection
The all and get methods in Eloquent can query multiple results and return an `Illuminate\Database\Eloquent\Collection` instance. The `Collection` class provides many helper functions to manipulate Eloquent results:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Using Cursor
The cursor method allows you to iterate through the database using a cursor, executing the query only once. When dealing with large amounts of data, the cursor method can significantly reduce memory usage:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

The cursor returns an `Illuminate\Support\LazyCollection` instance. [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) allow you to use most of the collection methods in Laravel, loading only a single model into memory at a time:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Select Subqueries
Eloquent provides advanced support for subqueries, allowing you to extract information from related tables in a single query. For example, suppose there is a destinations table and a flights table representing flights to the destinations. The flights table contains an arrival_at field indicating when the flight arrived at the destination.

Using the select and addSelect methods provided by subquery functionality, you can query all destinations and the names of the last flights to each destination in a single statement:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Sorting by Subquery
Additionally, the orderBy function of the query builder also supports subqueries. You can use this feature to sort all destinations based on the time of the last flight's arrival at each destination. Again, this can be done with a single query to the database:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Retrieving Single Model / Collection
In addition to retrieving all records from a specified table, you can use the find, first, or firstWhere methods to retrieve a single record. These methods return a single model instance instead of a model collection:
```php
// Find a model by its primary key...
$flight = app\model\Flight::find(1);

// Find the first model that matches the query...
$flight = app\model\Flight::where('active', 1)->first();

// Find the first model that matches the query in a concise way...
$flight = app\model\Flight::firstWhere('active', 1);
```

You can also use an array of primary keys as a parameter to the find method, which will return a collection of matching records:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Sometimes, you may want to perform other actions when finding the first result if it doesn't exist. The firstOr method will return the first result if found, and if not, will execute the given callback. The return value of the callback will be the return value of the firstOr method:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
The firstOr method also accepts an array of columns for the query:
```php
$model = app\model\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## "Not Found" Exception
Sometimes you may want to throw an exception when a model is not found. This is particularly useful in controllers and routes. The findOrFail and firstOrFail methods will retrieve the first result of the query and throw an Illuminate\Database\Eloquent\ModelNotFoundException exception if not found:
```php
$model = app\model\Flight::findOrFail(1);
$model = app\model\Flight::where('legs', '>', 100)->firstOrFail();
```

## Retrieving Collection
You can also use the count, sum, and max methods provided by the query builder, along with other collection functions, to operate on a collection. These methods will only return appropriate scalar values rather than a model instance:
```php
$count = app\model\Flight::where('active', 1)->count();

$max = app\model\Flight::where('active', 1)->max('price');
```

## Insertion
To insert a new record into the database, create a new model instance, set the attributes, and then call the save method:
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
        // Validate the request

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

The created_at and updated_at timestamps will be automatically set (if the $timestamps attribute in the model is true) without the need for manual assignment.

## Update
The save method can also be used to update models that already exist in the database. To update a model, you need to first retrieve it, set the properties to be updated, and then call the save method. Similarly, the updated_at timestamp will be automatically updated, so manual assignment is not required:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Batch Update
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Checking Property Changes
Eloquent provides the isDirty, isClean, and wasChanged methods to check the internal state of the model and determine how its properties have changed since they were originally loaded.
The isDirty method determines whether any properties have been changed since the model was loaded. You can pass a specific property name to determine if a specific property is dirty. The isClean method is the opposite of isDirty and also accepts an optional property parameter:
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
The wasChanged method determines whether any properties were changed the last time the model was saved during the current request cycle. You can also pass a property name to see if a specific property has been changed:
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
You can also use the create method to save new models. This method will return a model instance. However, before using it, you need to specify the fillable or guarded properties on the model, as all Eloquent models are not mass assignable by default.

When unexpected HTTP parameters are passed in by a user and those parameters change fields in the database that you don't want to change, a mass assignment vulnerability occurs. For example, a malicious user could pass an is_admin parameter through an HTTP request and then pass it to the create method, allowing the user to upgrade themselves to an administrator.

So, before you start, you should define which attributes on the model can be mass assigned. You can achieve this through the $fillable property on the model. For example, if you want the name attribute of the Flight model to be mass assignable:
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
Once we have set the attributes that can be mass assigned, we can insert new data into the database using the create method. The create method will return the saved model instance:
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
If you already have a model instance, you can pass an array to the fill method to assign values:
```php
$flight->fill(['name' => 'Flight 22']);
```
$fillable can be thought of as a "whitelist" for mass assignment, and you can also use the $guarded property to achieve this. The $guarded property contains an array of attributes that cannot be mass assigned. In other words, $guarded functionally behaves more like a "blacklist". Note: you can only use either $fillable or $guarded, not both at the same time. In the following example, all properties except for price can be mass assigned:
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
If you want all properties to be mass assignable, you can define $guarded as an empty array:
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
There are two methods you might use for mass assignment: firstOrCreate and firstOrNew. The firstOrCreate method will match the given key/value pairs to the data in the database. If the model is not found in the database, a record will be inserted containing the properties of the first parameter and the optional properties of the second parameter.

The firstOrNew method attempts to find a record in the database using the given attributes, and if it cannot find the model, it will return a new model instance. Note that the model instance returned by firstOrNew has not been saved to the database, so you need to call the save method manually:
```php
// Find the flight by name, or create it if not found...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// Find the flight by name, or create it with the delayed and arrival_time properties...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Find the flight by name, or return a new instance...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// Find the flight by name, or return a new instance with the delayed and arrival_time properties...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```
You may also encounter situations where you want to update an existing model, or create a new model if it does not exist. This can be achieved in one step using the updateOrCreate method. Similar to the firstOrCreate method, updateOrCreate persists the model, so there is no need to call save():
```php
// If there is a flight from Oakland to San Diego, set the price to $99.
// If no matching model is found, create one.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## Delete Model
You can call the delete method on a model instance to delete the instance:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Delete Model by Primary Key
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```

## Delete Model by Query
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Copy Model
You can use the replicate method to copy a new unsaved instance, which is very useful when model instances share many of the same attributes:
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

## Model Comparison
Sometimes, you may need to determine if two models are "the same". The is method can be used to quickly verify if two models have the same primary key, table, and database connection:
```php
if ($post->is($anotherPost)) {
    //
}
```

## Model Observers
Refer to [Model Events and Observer in Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Note: Eloquent ORM requires additional composer import "illuminate/events"
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