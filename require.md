# 所需环境


## linux系统
linux系统依赖`posix`和`pcntl`扩展，这两个扩展是php内置的扩展，一般来说无需安装即可使用。

如果您是宝塔用户，则只需要在宝塔中将`pnctl_`开头的函数禁用删除即可。

`event`扩展不是必须的，但是为了更好的性能建议安装此扩展。

## windows系统
webman可以运行在windows系统，但是由于无法设置多进程、守护进程等原因，建议windows仅作为开发环境，生产环境请使用linux系统。

注意：windows系统下不依赖`posix`和`pcntl`扩展。