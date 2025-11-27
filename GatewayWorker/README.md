GatewayWorker windows 版本
=================

GatewayWorker基于[Workerman](https://github.com/walkor/Workerman)开发的一个项目框架，用于快速开发长连接应用，例如app推送服务端、即时IM服务端、游戏服务端、物联网、智能家居等等。

GatewayWorker使用经典的Gateway和Worker进程模型。Gateway进程负责维持客户端连接，并转发客户端的数据给Worker进程处理；Worker进程负责处理实际的业务逻辑，并将结果推送给对应的客户端。Gateway服务和Worker服务可以分开部署在不同的服务器上，实现分布式集群。

GatewayWorker提供非常方便的API，可以全局广播数据、可以向某个群体广播数据、也可以向某个特定客户端推送数据。配合Workerman的定时器，也可以定时推送数据。

GatewayWorker Linux 版本
======================
Linux 版本GatewayWorker 在这里 https://github.com/walkor/GatewayWorker

启动
=======
双击start_for_win.bat

Applications\YourApp测试方法
======
使用telnet命令测试（不要使用windows自带的telnet）
```shell
 telnet 127.0.0.1 8282
Trying 127.0.0.1...
Connected to 127.0.0.1.
Escape character is '^]'.
Hello 3
3 login
haha
3 said haha
```

手册
=======
http://www.workerman.net/gatewaydoc/

使用GatewayWorker-for-win开发的项目
=======
## [tadpole](http://kedou.workerman.net/)  
[Live demo](http://kedou.workerman.net/)  
[Source code](https://github.com/walkor/workerman)  
![workerman-todpole](http://www.workerman.net/img/workerman-todpole.png)   

## [chat room](http://chat.workerman.net/)  
[Live demo](http://chat.workerman.net/)  
[Source code](https://github.com/walkor/workerman-chat)  
![workerman-chat](http://www.workerman.net/img/workerman-chat.png)  

启动与停止
注意Workerman启动停止等命令都是在命令行中完成的。

要启动Workerman，首先需要有一个启动入口文件，里面定义了服务监听的端口及协议。

这里以workerman-chat为例，它的启动入口为start.php。

以debug（调试）方式启动

php start.php start

以daemon（守护进程）方式启动

php start.php start -d

停止
php start.php stop

重启
php start.php restart

平滑重启
php start.php reload

查看状态
php start.php status

debug和daemon方式区别
1、以debug方式启动，代码中echo、var_dump、print等打印函数会直接输出在终端。

2、以daemon方式启动，代码中echo、var_dump、print等打印会默认重定向到/dev/null文件，可以通过设置Worker::$stdoutFile = '/your/path/file';来设置这个文件路径。

3、以debug方式启动，终端关闭后workerman会随之关闭并退出。

4、以daemon方式启动，终端关闭后workerman继续后台正常运行。

workerman启动时报错pcntl_fork被禁用
解决方法
pcntl_fork 被禁用了。
找到php.ini文件并打开
从disable_functions中删除pcntl_fork
重启apache或者nginx

在宝塔中找到已安装软件，点进去在禁用函数中去掉pcntl_fork，pcntl_wait了,pcntl_signal等相关项目然后重启PHP
