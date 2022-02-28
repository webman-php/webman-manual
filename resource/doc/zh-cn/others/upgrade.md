# 升级方法

总体方案，重新创建一个webman项目，然后将app目录、config目录覆盖新项目，完成升级。

- `composer clear`
- `composer create-project workerman/webman`
- 将原有项目中app目录、config目录覆盖到新项目
- 查看原有项目composer.json 安装了哪些组件/插件，执行composer安装
