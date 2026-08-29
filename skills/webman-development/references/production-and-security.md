# Webman 生产部署与安全边界

仅在部署、发布、Nginx/HTTPS/代理、生产故障、安全审查、文件权限或公网暴露相关任务时读取本文件。先确认实际主机、部署方式、反向代理、监听端口、进程用户、现有防火墙和变更授权；本页不替代组织的网络、身份、密钥、备份或漏洞管理规范。

## 先确认生产拓扑

部署前按当前项目检查：`composer.lock`、启动入口、`config/process.php` 或旧版 `config/server.php`、Nginx site 配置、`public/`、`runtime/`、上传目录、日志目录、既有 health check 与发布脚本。

| 边界 | 需要确认的事实 |
|---|---|
| Webman listener | 监听地址、端口、协议、worker 数和是否直接暴露公网。若 Nginx 是唯一入口，后端 listener 应只允许本机或受控内网访问，并由防火墙/安全组实际执行。 |
| Nginx | 域名、TLS 终止点、`root`、静态路径、动态代理、WebSocket/专用端口和 upstream 健康边界。 |
| 文件系统 | 代码、`runtime/`、日志、公开上传、私有上传与临时目录分别由谁读写；Web 进程不应对整个项目或任意系统路径拥有写权限。 |
| 运行身份 | HTTP worker 与自定义 process 的 `user`/`group`、Monitor 的实际权限需求，以及部署账户和 Web 运行账户是否分离。 |
| 发布流程 | 配置/代码/依赖/进程定义是否需要 reload 或 restart，迁移是否另有明确授权与回退计划。 |

生产环境通常使用 Linux；Windows 只用于开发/调试。具体启动、reload、restart 和 Windows 差异遵循 [core-runtime.md](core-runtime.md)，不能在未经授权的服务器上猜测或执行进程管理命令。

## Nginx 与 `public/` 是访问边界

Nginx 的 `root` 必须是项目的 `public/` 目录，绝不能是 Webman 项目根目录。否则 `.env`、配置、依赖、运行期文件或私有上传可能被直接下载。静态资源由 Nginx 处理；未命中静态文件的请求才代理到 Webman。

以下仅是已有 site 配置中的最小结构示意。域名、TLS、端口、WebSocket、缓存、限流和上传大小必须按真实部署补全；不能直接覆盖现有 Nginx 配置。

```nginx
upstream webman_backend {
    server 127.0.0.1:8787;
    keepalive 1024;
}

server {
    server_name example.com;
    root /srv/example/public;

    location / {
        try_files $uri @webman;
    }

    location @webman {
        proxy_set_header Host $host;
        # 只在 Nginx 是受控的唯一入口时，才把连接对端地址交给应用。
        proxy_set_header X-Forwarded-For $remote_addr;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        proxy_pass http://webman_backend;
    }

    location ~ \.php$ {
        return 404;
    }

    location ^~ /.well-known/ {
        try_files $uri =404;
    }

    location ~ /\. {
        return 404;
    }
}
```

- 若项目的静态/下载规则、子路径部署或 SPA fallback 已存在，保留并逐项验证它们与 `try_files` 的顺序。不能为了 Webman 代理而让 API、WebSocket、ACME 或前端深链接失效。
- TLS 证书、HTTP→HTTPS 跳转、HSTS、CORS、上传大小、缓存和响应 Header 都有域名/客户端影响。先审查现有 CDN、反向代理和前端，再做最小改动；不能把一组通用 Header 当作无风险的安全修复。
- 直接探测 Webman 后端端口只能用于受控运维验证，不能把它当成对外可访问入口。新增 listener、端口、代理路由或公开下载位置都属于部署面变更。

## 代理 Header、IP 与 URL 生成

Webman 看到的是直连 peer 地址；经 Nginx 后它通常是 Nginx 的地址。`X-Forwarded-For`、`X-Real-IP`、`X-Forwarded-Proto`、`Host` 等 Header 在绕过代理时都可由客户端伪造。

- 仅当 Webman listener 的网络层已限制为受信任代理时，才按代理约定读取真实 IP 或协议。高风险授权、审计、限流和风控不能盲目信任 `$request->getRealIp()` 解析出的任意 Header；详细 API 与限制见 [http-recipes.md](http-recipes.md)。
- 公开链接、redirect 和 Cookie `secure` 属性依赖实际 HTTPS 终止位置。代理转发协议 Header 后，应用仍应把用户提供的 Host/URL 视为不可信，使用受控站点配置或白名单构造外部 URL。
- 有 CDN、负载均衡或多层代理时，先确定每一层是否覆盖、追加或保留转发 Header，再定义唯一可信边界并从真实入口验证。不要靠应用代码猜测 Header 链。

## 最小权限与文件安全

- 为 HTTP worker 和每个自定义 process 使用满足业务所需的低权限 `user`/`group`；配置位置随版本可能在 `config/process.php` 或旧版 `config/server.php`。部署账户、Web 运行账户和数据库管理员账户不应不必要地共用。
- 只给运行账户写入 `runtime/`、日志、受控上传或缓存目录的权限。代码、Composer vendor、配置和私有密钥通常不应由 HTTP worker 写入；不要用递归宽权限掩盖部署问题。
- Monitor 可能需要高权限来管理其它 worker。先确认当前项目是否启用它和它的真实职责，不能机械地把 Monitor 降权后假定仍可工作，也不能因此让全部应用 worker 以高权限运行。
- 不把 `.env`、配置、私钥、数据库转储、日志或私有上传放在 `public/`。公开上传也禁止按原始文件名/扩展名提供可执行脚本；上传的文件校验、存储与授权细节见 [http-recipes.md](http-recipes.md)。

## 应用攻击面

- `app/controller/` 及其子目录只放控制器。非控制器类移到对应 service/model 等目录；检查 `controller_suffix` 等目标项目配置，减少默认路由把非控制器类或方法暴露为 URL 的风险。路由与中间件具体规则见 [routing-and-middleware.md](routing-and-middleware.md)。
- 校验、认证与对象级授权各自独立。校验字段形状后仍须检查当前用户是否有权访问该租户、记录、文件或后台操作；SQL 绑定、事务、唯一约束与多连接边界见 [data-recipes.md](data-recipes.md)。
- 不在入库时做通用 XSS 转义。保留原始结构化数据，在 HTML 模板按输出上下文转义；API JSON、URL、HTML 属性和 JavaScript 字符串的编码规则不同。不要因为模板默认转义就把 `raw`/未转义输出视为安全。
- 生产响应和日志不得暴露堆栈、SQL、内部路径、密钥、令牌、Cookie 或上传临时路径。先确认 `debug`、异常 Handler 与日志 channel 的实际配置；错误响应契约见 [application-services.md](application-services.md)。

## 发布、恢复与可验证性

- 业务代码和普通配置通常要求 reload；新增 Composer 依赖、扩展、`config/process.php`/`config/server.php`、端口、worker 数或事件循环要求 restart。发布前后按 [core-runtime.md](core-runtime.md) 区分，且只在用户授权时执行。
- 数据库迁移、队列消费者、后台进程、缓存失效、重建索引或密钥轮换各有外部影响，不能与常规 PHP reload 混为一步。先制定顺序、失败处理和回退边界；迁移细节见 [data-recipes.md](data-recipes.md)。
- 当 Webman 无法启动时，先确认实际 CLI PHP、`php --ini`、所需扩展和 `disable_functions`；不要在生产机上把网络下载脚本直接 pipe 到 PHP，也不要为了启动成功随意解除高风险函数限制。

每次部署或代理变更至少确认：

1. Nginx 配置语法和 reload 由有权限的运维流程实际验证。
2. 外部域名的 HTTPS/重定向、静态资源和动态路由均走预期入口。
3. 项目根目录、`.env`、配置、日志、私有上传、点文件和 `.php` 路径无法从公网读取或执行。
4. Webman 后端端口不对未授权网络开放；进程以预期用户和数量运行，`runtime/` 可写而代码不被进程写入。
5. 认证失败、授权拒绝、上传失败、错误响应和日志脱敏在真实代理路径下符合当前 API 契约。

语法检查、Nginx `-t`、单个 curl 或单机验证都不能证明 CDN、WAF、多实例、故障切换、容量、证书续期或攻击防护已正确。报告时必须区分已实际验证的入口与未验证的生产边界。
