# supervisor

## 安装

```
# 如果没有 easy_install 需要安装 python-setuptools
$ yum install python-setuptools
$ easy_install supervisor
```

## 配置

```
# 设置默认配置
$ echo_supervisord_conf > /etc/supervisord.conf
$ vim /etc/supervisord.conf
```

将此项加入到结尾

```
[include]
files = /etc/supervisor/*.conf
```

Laravel队列示例

/etc/supervisor/laravel.conf

```
[program:laravel-base]
process_name=%(program_name)s_%(process_num)02d
command=php /www/website/base/artisan queue:work  --sleep=3 --tries=3 --daemon
autostart=true
autorestart=true
user=nobody
numprocs=1
redirect_stderr=true
stdout_logfile=/www/website/base/worker.log


[program:laravel-base-wechat]
process_name=%(program_name)s_%(process_num)02d
command=php /www/website/base/artisan queue:work --queue=wechat --sleep=3 --tries=3 --daemon
autostart=true
autorestart=true
user=nobody
numprocs=1
redirect_stderr=true
stdout_logfile=/www/website/base/worker.log
```

## 操作

设置服务脚本

```
$ vim /etc/init.d/supervisord
```

写入如下内容

```
#! /bin/sh
PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/bin:
PROGNAME=supervisord
DAEMON=/usr/bin/$PROGNAME
CONFIG=/etc/$PROGNAME.conf
PIDFILE=/tmp/$PROGNAME.pid
DESC="supervisord daemon"
SCRIPTNAME=/etc/init.d/$PROGNAME
# Gracefully exit if the package has been removed.
test -x $DAEMON || echo "$DAEMON is not exists" || exit 0
start()
{
        echo -n "Starting $DESC: $PROGNAME"
        $DAEMON -c $CONFIG
        echo "..."
}
stop()
{
        echo -n "Stopping $DESC: $PROGNAME"
        supervisor_pid=$(cat $PIDFILE)
        kill -15 $supervisor_pid
        echo "..."
}
case "$1" in
  start)
        start
        ;;
  stop)
        stop
        ;;
  restart)
        stop
        start
        ;;
  *)
        echo "Usage: $SCRIPTNAME {start|stop|restart}" >&2
        exit 1
        ;;
esac
exit 0
```

> 或者参考下面的文本
> https://github.com/cedricporter/supervisor_conf/blob/master/init.d/supervisor
> 需要修改 DAEMON SUPERVISORCTL 的路径
> 以及安装 start-stop-daemon

设置执行权限

```
$ chmod +x /etc/init.d/supervisord
```

启动

```
$ service supervisord start
```

停止

```
$ service supervisord stop
```

重启

> 修改配置之后，需要重新启动

```
$ service supervisord restart
```

查询状态

```
$ supervisorctl status
```

## 说明:

- 原文： https://www.load-page.com/base/manual/56#h1--supervisor0
- 廖雪峰教程：http://www.liaoxuefeng.com/article/0013738926914703df5e93589a14c19807f0e285194fe84000
- 教程：http://everet.org/supervisor.html