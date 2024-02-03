# Upgrade Method

`composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install`

> **Note**
> Due to the termination of data synchronization from the official composer source to the Alibaba Cloud composer proxy, it is currently not possible to upgrade to the latest webman using the Alibaba Cloud composer proxy. Please use the following command to restore the use of the official composer data source: `composer config -g --unset repos.packagist`.