# Required Environment


## linuxSystem
linuxThe system depends on the `posix` and `pcntl` extensions, which are built-in extensions to php and are generally available without installation。

If you are a pagoda user, then just disable and remove the functions starting with `pnctl_` from pagoda。

`event`Extensions are not required, but it is recommended to install this extension for better performance。

## windowsSystem
webmanSet the class inwindowsSystem，but since it is not possible to set up multiple processes、daemon，SuggestionswindowsOnly as a development environment，In the vast majority of caseslinuxSystem。

Note: Windows does not rely on the `posix` and `pcntl` extensions。