# Install

webman RequirementPHP >= 7.2

## composerInstall

**1、Remove composer proxy**
`composer config -g --unset repos.packagist`

> **Description**
> Some composer proxy images are incomplete (e.g. Aliyun), use the above command to remove the composer proxy

**2、Create Project**

`composer create-project workerman/webman`

**3、Run**  

Go to webman directory   

debugRun as (for development debugging))
 
`php start.php start`

daemonRun as (for official environments))

`php start.php start -d`

**windowsThe user starts it by double-clicking windows.bat or running `php windows.php`**

**4、Access**

Browser access `http://ipaddress:8787`


