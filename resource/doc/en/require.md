# Required Environment


## Linux System
The Linux system relies on the `posix` and `pcntl` extensions, both of which are built-in PHP extensions and generally do not require installation to use.

If you are a user of Baota, you only need to disable or remove functions starting with `pnctl_` in Baota.

The `event` extension is not mandatory, but its installation is recommended for better performance.

## Windows System
Webman can run on the Windows system; however, due to the inability to set up multiple processes, daemon processes, and other reasons, it is advisable to use Windows only as a development environment, and for the production environment, Linux should be used.

Note: The `posix` and `pcntl` extensions are not required for the Windows system.