##学习使用PHPStorm代码IDE
-工欲善其事，必先利其器。熟悉IDE对编写代码效率有很大的提升。
-PhpStorm是一个轻量级且便捷的PHP -IDE，其旨在提供用户效率，可深刻理解用户的编码，提供智能代码补全，快速导航以及即时错误检查。

###PHP代码的运行
-PHP的代码并不能直接被机器所识别，我们需要WEB容器去编译执行。

####WEB容器(apache)的配置
#####基本配置
-配置apache的原因是我们需要告诉它，我们的代码在哪，在什么事情发生的情况下(触发)apache就去编译执行。
-相应的配置需要在文件中修改，apache在每次开启时会读取配置文件，所以修改配置文件后需要重启apache才能生效。
-conf为配置目录，httpd.conf是主要的配置文件，类似于C语言的main函数，Include 后面加文件的相对路径就能扩充其他的配置文件。
-Directory标签下可以设置apache在什么情况下执行代码，而你代码在哪就是这个标签的路径属性，类似于HTML标签。

#####多个目录
-如果了解了上面，你就能告诉apache你的代码在哪了，但是现在只能配一个目录下的代码。
-当apache知道你不止有一个目录的情况下，你输入localhost Apache会执行那个目录下的代码呢？
-第一种我们可以通过端口号让apache知道要执行哪个目录下的代码。
######方法一
-这个思路下第一步增加apache监听的端口。打开httpd.conf文件，添加Listen + 端口号。
-第二步打开httpd-vhosts.conf文件，如果没有在httpd.conf引入需要引入，一般都是引入的。
-参考已有的VirtualHost标签，自己写个并在属性里写上地址加端口号(如*:801)，标签里DocumentRoot写上你的文件路径。
-这样通过端口号就能知道执行哪个文件了。
######方法二
-和方法一很类型，我们改HOST文件去加一个指向127.0.0.1的域名，和方法一一样通过VirtualHost配置虚拟主机指向你的文件路径。


###代码段

###远程连接

###Xdebug调试

-Xdebug是一个开放源代码的PHP程序调试器(即一个Debug工具)，可以用来跟踪，调试和分析PHP程序的运行状况。
-首先需要安装Xdebug扩展，去官网下载对应的版本并安装。
-在php.ini中我们要使调试的端口和phpstorm监听的端口相一致，所以添加下面设置。
-xdebug.remote_port=9000
-xdebug.remote_enable =on
-设置Chrome浏览器的监听，在扩展插件中安装Xdebug helper插件,并在插件选项中设置IDE为Phpstorm。
-打开phpstorm File-Settings-Languages & Frameworks-PHP 在CLI Interpreters设置的PHP执行文件
-PHP language level 为PHP执行版本。
-在 File-Settings-Settings-Languages & Frameworks-PHP-Servers 中添加本地运行地址与端口。
-右上角的小框里找到Run/Debug Configurations 可以设置Defaults-PHP Web Page 配置我们执行的域名以及
-本地运行的Server和浏览器。
-也能在左上角点开加号配置专有的配置，上一步的配置类似公共配置。


###快捷键

-Ctrl + shift + X 打开浏览器中的Xdebug
-Ctrl + Alt + S 打开phpstorm设置
-Alt + shift + F9 打开phpstorm debug 设置
-shift + F9 phpstorm开始debug
-Alt + 2 查看断点标记
