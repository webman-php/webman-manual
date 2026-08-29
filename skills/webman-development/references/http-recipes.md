# Webman HTTP 高频配方

仅在实现或审查 Webman 的请求读取、响应、上传、下载或分段响应时读取本文件。先确认目标项目的 Webman 版本和现有错误响应格式；以下配方不替代参数校验、权限控制或业务规则。

## 请求对象与输入

在控制器方法中优先声明 `support\Request` 参数。仅在控制器以外且当前执行路径确实存在 HTTP 请求时，才使用 `request()` 助手函数；不要把 Request 保存到单例、静态变量、Timer 回调或跨请求对象中。

```php
use support\Request;

public function show(Request $request)
{
    $page = (int) $request->get('page', 1);
    $keyword = trim((string) $request->input('keyword', ''));

    // 输入值仍要按业务规则校验。
}
```

| 任务 | 推荐入口 | 注意事项 |
|---|---|---|
| 读取全部或单个查询参数 | `$request->get()`、`$request->get('key', $default)` | 未提供参数时，单值返回 `null` 或默认值。 |
| 读取全部或单个表单参数 | `$request->post()`、`$request->post('key', $default)` | 不要把未校验的输入直接写入查询或响应 HTML。 |
| 合并读取 GET 与 POST | `$request->input('key', $default)`、`$request->all()` | 需要明确来源时使用 `get()` 或 `post()`，不要让同名参数隐式覆盖业务判断。 |
| 获取白名单或排除字段 | `$request->only([...])`、`$request->except([...])` | 这不是验证；写入模型前仍要列出允许字段。 |
| 读取非表单原始包体 | `$request->rawBody()` | 对 JSON、XML 或签名回调先验证格式、签名及大小，再解析。 |
| 读取 Header | `$request->header('header-name', $default)` | Header 名在 Webman 中使用小写；外部 Header 均不可信。 |
| 读取 Cookie | `$request->cookie('name', $default)` | Cookie 值是客户端输入，不能视为认证或授权结论。 |

不要通过 `setPost()`、`setGet()` 或 `setHeader()` 做全局 HTML 转义。正常业务应验证并保存原始结构化数据，在 HTML 渲染时按上下文转义；只有中间件确实需要提供一个经过规范化的请求对象时，才明确重写完整参数数组。

## 请求元数据与真实 IP

```php
$method = $request->method();       // GET、POST、PUT 等
$path = $request->path();           // 不含 query string
$uri = $request->uri();             // 包含 path 与 query string
$query = $request->queryString();
$url = $request->url();             // 不含协议和 query string
$fullUrl = $request->fullUrl();     // 不含协议，包含 query string
```

- `url()` 与 `fullUrl()` 不含 `http` 或 `https`。在反向代理后要生成绝对 URL 时，先确认代理已正确传递协议 Header，且该代理受信任。
- `$request->getRemoteIp()` 是连接对端地址；经 Nginx 等代理时通常是代理 IP。
- 只有确认连接来自受信任代理时，才从该代理约定的 Header 读取真实 IP。`$request->getRealIp()` 会尝试多个可伪造 Header；不能把其结果直接作为高安全授权、风控或审计唯一依据。
- 需要协议协商时可用 `$request->expectsJson()`、`$request->acceptJson()`；不要仅凭 `isAjax()` 决定安全策略。

## 上传文件

上传表单必须使用 `multipart/form-data`。`$request->file()` 返回上传文件数组；`$request->file('avatar')` 返回单个 `Webman\Http\UploadFile` 或 `null`。

```php
use support\Request;

public function avatar(Request $request)
{
    $file = $request->file('avatar');
    if (!$file || !$file->isValid()) {
        return response(json_encode([
            'code' => 1,
            'message' => 'Upload failed',
        ], JSON_UNESCAPED_UNICODE), 422, [
            'Content-Type' => 'application/json',
        ]);
    }

    $extension = strtolower($file->getUploadExtension());
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($extension, $allowedExtensions, true)
        || $file->getSize() > 2 * 1024 * 1024) {
        return response(json_encode([
            'code' => 1,
            'message' => 'Unsupported file',
        ], JSON_UNESCAPED_UNICODE), 422, [
            'Content-Type' => 'application/json',
        ]);
    }

    $name = bin2hex(random_bytes(16)) . '.' . $extension;
    $file->move(public_path() . '/uploads/' . $name);

    return json([
        'code' => 0,
        'data' => ['path' => '/uploads/' . $name],
    ]);
}
```

上传实现必须同时处理以下边界：

- 先检查 `isValid()`；失败时可通过 `getUploadErrorCode()` 排查。文件在请求结束后会清除，必须在请求内移动。
- 使用 `$file->move($destination)`，不要使用 `move_uploaded_file()`。
- `getUploadName()`、`getUploadExtension()` 和 `getUploadMimeType()` 适合审计和初步白名单，不能单独证明文件内容安全。对可执行、可解析或公开访问的文件，按风险增加内容检测、病毒扫描、图像解码或专用存储。
- 新代码使用正确拼写的 `getUploadMimeType()`。已存在的 `getUploadMineType()` 调用可能是兼容旧接口；不要为了拼写修正而盲目破坏已有调用方。
- 生成服务端文件名，不能直接使用原始上传名作为保存路径。私有文件应存到非公开目录，并在下载时执行授权；示例中的 `public/uploads` 仅适合允许公开访问的资源。
- 大小还受 Webman 包大小配置限制。字段位置随版本可能是 `config/process.php` 或旧版 `config/server.php`；先核对项目配置和部署层限制，不要只修改业务校验。

## 常见响应

控制器应 `return` 一个 `support\Response`。`json()` 适合默认 200 JSON 响应；自定义 HTTP 状态时创建 `response()` 并显式设置 `Content-Type`。

```php
return response('ok');

return json([
    'code' => 0,
    'data' => $data,
]);

return response(json_encode([
    'code' => 1,
    'message' => 'Validation failed',
], JSON_UNESCAPED_UNICODE), 422, [
    'Content-Type' => 'application/json',
]);
```

| 任务 | 推荐入口 | 注意事项 |
|---|---|---|
| 文本或自定义状态/正文 | `response($body, $status, $headers)` | 不要把调试输出混入正文。 |
| 默认 JSON | `json($data)` | 默认状态为 200；非 2xx/3xx 时使用 `response()` 或项目统一响应辅助函数。 |
| XML | `xml($xml)` | XML 内容仍由业务负责生成和验证。 |
| 视图 | `view('path', $data)` | 仅在项目配置了对应视图处理器时使用；HTML 输出转义规则见后续应用服务 reference。 |
| 重定向 | `redirect('/target', 302)` | 目标来自输入时要限制为允许的相对路径或白名单，避免开放重定向。 |
| Header | `response()->header('Name', 'Value')` 或 `withHeaders([...])` | 用户输入不能未经校验写入 Header。 |
| Cookie | `response()->cookie(...)` | 按是否 HTTPS 设置 `secure`，认证 Cookie 应使用 `http_only` 并遵循项目的 SameSite 策略。 |

## 文件预览与下载

```php
// 浏览器预览或内联发送
return response()->file($absolutePath);

// 强制下载，并指定下载名
return response()->download($absolutePath, 'report.pdf');
```

- `file()` 对大文件采用分段发送，并处理合适的 Content-Type 和缓存协商；路径不存在时会得到 404。
- 不能将请求参数直接拼接到 `$absolutePath`。先从数据库或受控映射取得文件记录，做授权，再构造位于允许根目录内的绝对路径。
- `download()` 的下载名应由服务器生成或清理，避免 Header 注入和异常文件名。

## 分段响应

分段响应适用于需要持续向同一个客户端输出已产生结果的场景，不是可靠队列替代品。先返回带 `Transfer-Encoding: chunked` 的 Response，随后通过 `$request->connection` 发送 `Workerman\Protocols\Http\Chunk`；以空 Chunk 结束响应。

```php
use Workerman\Protocols\Http\Chunk;

$connection = $request->connection;
// 在实际异步回调中：$connection->send(new Chunk($payload));
// 完成时：$connection->send(new Chunk(''));

return response()->withHeaders([
    'Transfer-Encoding' => 'chunked',
]);
```

- 定时器或异步回调结束时必须停止自身，并保证只发送一次结束空 Chunk；否则会泄漏定时器或留下未完成响应。
- 客户端断开、背压、异常和超时必须按当前项目使用的 Workerman/HTTP 客户端 API 处理。
- 需要可靠投递、失败重试或突发削峰时，使用队列 reference 中的方案，不要依赖连接持续存在。

## 未覆盖的 API

本文件故意不列出所有 Request/Response 方法。需要 `sessionId()`、协议版本、应用/插件/控制器信息、参数重写等低频能力时，先检查目标项目锁定版本的 Webman 请求或响应文档与已安装源码，再添加最小实现。
