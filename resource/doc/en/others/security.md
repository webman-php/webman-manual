# Security

## Run User
Suggest toRun UserSet to a user with lower privileges，for example withnginxRun Userconsistent。Run User在 `config/server.php` in`user`和`group`set in。
Users for similar custom processes are specified via `user` and `group` in `config/process.php`。
Note that the monitor process should not be set to run as a user, as it requires high privileges to work properly。

## Controller Specification
`controller`Only controller files can be placed in directories or subdirectories，Prohibit the placement of other class files，will return detailed[Advanced](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)时，The class file may beurlApplication Plugin，cause unpredictable consequences。
example `app/controller/model/User.php` actuallyModel类，but is incorrectly placed`controller`directory，Although it's[Advanced](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)时，would result in the user being able to query multiple results with methods like`/model/user/xxx`access`User.php`Highly recommended。
To completely eliminate this situation，Subscription-based model[Advanced](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)Explicitly mark which are controller files。


## XSSfilter
Considering generality, webman does not have XSS escaping for requests。
webmanXSS escaping at render time is highly recommended instead of escaping before inbound。
and twig, plade, think-tmplate and other templates will automatically perform XSS escaping without manual escaping, which is very convenient。

> **hint**
> If you do XSS escaping before inbound, it is likely to cause incompatibility issues with some application plugins


## Prevent SQL Injection
forPrevent SQL Injection，Controller SuffixORM，如 [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html)、[think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html)，Try not to assemble it yourself when using itSQL。

## nginxProxy
When your application needs to be exposed to extranet users，Functions are also supportedwebmanPlease try to usenginxProxy，This will dofiltersome illegalHTTPrequest，improveSecurity性。It is highly recommended to add a [nginxProxy](nginx-proxy.md)
