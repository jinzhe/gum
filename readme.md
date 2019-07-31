# 概览
> `gum` 是基于`php+mysql`开发的一个轮子。一个可以快速用来做`RESTful API` 接口，也可以做传统动态网页。 `gum`  的核心设计理念是功能服务化，在`service` 文件夹中每一个文件代表着一个服务模块，简单清晰明了。

# 运行环境

- 操作系统：linux / macOS / Windows
- PHP版本：7.x 以上
- MYSQL 版本: 最新版即可
- 伪静态：需要开启
- 服务器：apache / nginx

# 安装说明

1. 上传服务器后需要运行安装程序 `https://域名/@install/`
2. 根据提示需要设置`upload` `backup` `config.php` `theme/default.txt`设置`777`写入权限。
3. 设置好后需要删除`@install`文件夹，以防被人利用。
4. 设置伪静态，根目录已经包含`apache`的`.htaccess`文件和`nginx`的`nginx.conf`。
5. 访问后台 `https://域名/admin/` 其中admin文件夹可以改名字或者是放在本地服务器，因为它就是一个纯前端客户端。

# 联系我们

QQ群：16740622
