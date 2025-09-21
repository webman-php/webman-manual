# 控制器

新建控制器文件 `app/controller/FooController.php`。

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

当访问 `http://127.0.0.1:8787/foo` 时，页面返回 `hello index`。

当访问 `http://127.0.0.1:8787/foo/hello` 时，页面返回 `hello webman`。

当然你可以通过路由配置来更改路由规则，参见[路由](route.md)。

> **提示**
> 如果出现404无法访问，请打开`config/app.php`，将`controller_suffix`设置为`Controller`，并重启。

## 控制器后缀
webman从1.3版本开始，支持在`config/app.php`设置控制器后缀，如果`config/app.php`里`controller_suffix`设置为空`''`，则控制器类似如下

`app\controller\Foo.php`。

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

强烈建议将控制器后缀设置为`Controller`，这样能能避免控制器与模型类名冲突，同时增加安全性。

## 说明
 - 框架会自动向控制器传递`support\Request` 对象，通过它可以获取用户输入数据(get post header cookie等数据)，参见[请求](request.md)
 - 控制器里可以返回数字、字符串或者`support\Response` 对象，但是不能返回其它类型的数据。
 - `support\Response` 对象可以通过`response()` `json()` `xml()` `jsonp()` `redirect()`等助手函数创建。
 

## 控制器参数绑定

#### 例子
webman支持通过控制器方法参数自动绑定请求参数，例如
```php
<?php
namespace app\controller;
use support\Response;

class UserController
{
    public function create(string $name, int $age): Response
    {
        return json(['name' => $name, 'age' => $age]);
    }
}
```

你可以通过`GET` `POST`方式传递`name`和`age`的值，也可以通过路由参数传递`name`和`age`参数，例如

```php
Route::any('/user/{name}/{age}', [app\controller\UserController::class, 'create']);
```

优先级为`路由参数` > `GET` > `POST`参数

#### 默认值

假设我们访问 `/user/create?name=tom`，我们将得到如下的错误

```html
Missing input parameter age
```
原因是我们没有传递`age`参数，可以通过给参数设置默认值来解决这个问题，例如

```php
<?php
namespace app\controller;
use support\Response;

class UserController
{
    public function create(string $name, int $age = 18): Response
    {
        return json(['name' => $name, 'age' => $age]);
    }
}
```

#### 参数类型
当我们访问 `/user/create?name=tom&age=not_int`，我们将得到如下的错误

> **提示**
> 这里为了方便测试，我们直接在浏览器地址栏输入参数，实际开发中应该通过`POST`方式传递参数

```html
Input age must be of type int, string given
```

这是因为接受的数据会按照类型进行转换，如果无法转换则会抛出`support\exception\InputTypeException`异常，
因为传递的`age`参数无法转换为`int`类型，所以得到如上错误。

#### 自定义错误
我们可以利用多语言自定义`Missing input parameter age` 和 `Input age must be of type int, string given` 这样的错误，
参考如下命令

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Input :parameter must be of type :exceptType, :actualType given' => '输入参数 :parameter 必须是 :exceptType 类型，传递的类型是 :actualType',
    'Missing input parameter :parameter' => '缺少输入参数 :parameter',
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

#### 其它类型
webman支持的参数类型有`int` `float` `string` `bool` `array` `object` `类实例`等，例如

```php
<?php
namespace app\controller;
use support\Response;

class UserController
{
    public function create(string $name, int $age, float $balance, bool $vip, array $extension): Response
    {
        return json([
            'name' => $name,
            'age' => $age,
            'balance' => $balance,
            'vip' => $vip,
            'extension' => $extension,
        ]);
    }
}
```

当我们访问 `/user/create?name=tom&age=18&balance=100.5&vip=true&extension[foo]=bar`，我们将得到如下的结果

```json
{
  "name": "tom",
  "age": 18,
  "balance": 100.5,
  "vip": true,
  "extension": {
    "foo": "bar"
  }
}
```

#### 类实例
webman支持通过参数类型提示传递类实例，例如

**app\service\Blog.php**
```php
<?php
namespace app\service;
class Blog
{
    private $title;
    private $content;
    public function __construct(string $title, string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }
    public function get()
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
        ];
    }
}
```

**app\controller\BlogController.php**
```php
<?php
namespace app\controller;
use app\service\Blog;
use support\Response;

class BlogController
{
    public function create(Blog $blog): Response
    {
        return json($blog->get());
    }
}
```

当访问 `/blog/create?blog[title]=hello&blog[content]=world`，我们将得到如下的结果

```json
{
  "title": "hello",
  "content": "world"
}
```

#### 模型实例

**app\model\User.php**
```php
<?php
namespace app\model;
use support\Model;
class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $timestamps = false;
    // 这里需要添加可填充字段，防止前端传入不安全字段
    protected $fillable = ['name', 'age'];
}
```

**app\controller\UserController.php**
```php
<?php
namespace app\controller;
use app\model\User;
class UserController
{
    public function create(User $user): int
    {
        $user->save();
        return $user->id;
    }
}
```

当访问 `/user/create?user[name]=tom&user[age]=18`，我们将得到类似如下的结果

```json
1
```

## 控制器生命周期

当`config/app.php`里`controller_reuse`为`false`时，每个请求都会初始化一次对应的控制器实例，请求结束后控制器实例销毁，这与传统框架运行机制相同。

当`config/app.php`里`controller_reuse`为`true`时，所有请求将复用控制器实例，也就是控制器实例一旦创建便常驻内存，所有请求复用。

> **注意**
> 开启控制器复用时，请求不应该更改控制器的任何属性，因为这些更改将影响后续请求，例如

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // 该方法将在第一次请求 update?id=1 后会保留下 model
        // 如果再次请求 delete?id=2 时，会删除 1 的数据
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **提示**
> 在控制器`__construct()`构造函数中return数据不会有任何效果，例如

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // 构造函数中return数据没有任何效果，浏览器不会收到此响应
        return response('hello'); 
    }
}
```

## 控制器不复用与复用区别
区别如下

#### 不复用控制器
每个请求都会重新new一个新的控制器实例，请求结束后释放该实例，并回收内存。不复用控制器和传统框架一样，符合大部分开发者习惯。由于控制器反复的创建销毁，所以性能会比复用控制器略差(helloworld压测性能差10%左右，带业务可以基本忽略)

#### 复用控制器
复用的话一个进程只new一次控制器，请求结束后不释放这个控制器实例，当前进程的后续请求会复用这个实例。复用控制器性能更好，但是不符合大部分开发者习惯。

#### 以下情况不能使用控制器复用

当请求会改变控制器的属性时，不能开启控制器复用，因为这些属性的更改会影响后续请求。

有些开发者喜欢在控制器构造函数`__construct()`里针对每个请求做一些初始化，这时候就不能复用控制器，因为当前进程构造函数只会调用一次，并不是每个请求都会调用。


