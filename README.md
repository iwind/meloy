# 数据管理平台
提供数据管理工具框架，可以方便地植入数据相关管理工具。

#安装需求
* PHP7及以上版本
* pdo扩展
* pdo_mysql扩展
* json扩展(一般内置)

#安装方法

##最快启动方法
在命令行下执行：
~~~
cd TeaData/
php -S localhost:3000
~~~

如有需要把其中的`php`和localhost、端口3000换成你自己的，然后即可在浏览器中访问
~~~
http://localhost:3000
~~~

##nginx和apache
直接将 *TeaData/* 放到网站目录下即可访问。