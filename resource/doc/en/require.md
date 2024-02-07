# Required Environment

## Linux System
Webman relies on the built-in PHP extensions `posix` and `pcntl` for proper functioning on a Linux system. These extensions typically do not require separate installation.

If you are using the "宝塔" control panel, simply disabling or removing functions beginning with `pnctl_` within the control panel will suffice.

While the `event` extension is not mandatory, it is recommended to install it for improved performance.

## Windows System
Webman can run on a Windows system; however, due to limitations such as the inability to configure multiple processes and daemon processes, it is advisable to use Windows only for development purposes. For production environments, it is suggested to utilize a Linux system.

Please note that the `posix` and `pcntl` extensions are not required for the Windows system.
