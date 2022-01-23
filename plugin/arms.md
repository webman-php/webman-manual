# ARMS 阿里云应用监控(链路追踪)
应用实时监控服务ARMS（Application Real-Time Monitoring Service）是一款阿里云应用性能管理（APM）类监控产品。借助ARMS可以监控webman相关指标如接口请求量、接口耗时、慢调用分析、调用链等。

> 插件需要webman>=1.2.0 webman-framework>=1.2.0

# 使用方法

## 1、开通ARMS

地址 https://arms.console.aliyun.com/ (一般有15天试用)

## 2、获得数据上报接入点url
进入 https://tracing.console.aliyun.com/#/globalSetting/cn-hangzhou/process 按照图示获得接入点url地址，留作步骤4使用。
> 如果你的服务器在阿里云上可以用阿里云vpc网络接入点，本示例用的是阿里云公网接入点

![](../img/arms-endpoint.png)

## 3、安装插件
`composer require webman/arms`

## 4、配置
打开 `config/plugin/webman/app.php`，配置应用名称以及`endpoint_url`(步骤2获得的接入点url)。

> **注意：**
> 如果使用的是thinkorm，请将config/thinkorm.php的trigger_sql开启，这样ARMS可以监控SQL。
> 如果是使用的Laravel的数据库，需要安装 `composer require "illuminate/events"`，这样ARMS可以监控SQL。

## 5、重启webman
`php start.php restart` 或者 `php start.php restart -d` 。并访问站点。

## 6、查看
访问地址 https://tracing.console.aliyun.com/ ,效果类似如下。
> 为了减少上报对应用的影响，中间件中设置的是每30秒统一上报一次数据，所以阿里云看到结果会有30秒延迟。

![](../img/arms-result.png)


![](../img/arms-result2.png)


## 7、更多内容参考文档
https://help.aliyun.com/document_detail/96187.html