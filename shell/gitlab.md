# gitlab

[TOC]

## 1 安装

### 1.1 编辑源

```sh
vim /etc/yum.repos.d/gitlab-ce.repo
```

```sh
[gitlab-ce]
name=gitlab-ce
baseurl=http://mirrors.tuna.tsinghua.edu.cn/gitlab-ce/yum/el6
repo_gpgcheck=0
gpgcheck=0
enabled=1
gpgkey=https://packages.gitlab.com/gpg.key
```

### 1.2 更新本地 YUM 缓存

```sh
yum makecache
```

### 1.3 安装 GitLab 社区版

```sh
yum install gitlab-ce

yum install gitlab-ce #(自动安装最新版)
yum install gitlab-ce-8.8.4-ce.0.el6 #(安装指定版本)
```

### 1.4 更改配置

```sh
vim /etc/gitlab/gitlab.rb
 # 找到 external_url 'http://000.00.00.00:8081'
 # 修改成你的地址
```

### 1.5 登录GitLab

```sh
Username: root 
Password: 5iveL!fe
```

## 2 其他问题

### 2.1 GitLab头像无法正常显示

> 原因：gravatar被墙

解决办法：

```sh
编辑 /etc/gitlab/gitlab.rb，将
# gitlab_rails['gravatar_plain_url'] = 'http://gravatar.duoshuo.com/avatar/%{hash}?s=%{size}&d=identicon'
修改为：
gitlab_rails['gravatar_plain_url'] = 'http://gravatar.duoshuo.com/avatar/%{hash}?s=%{size}
```

然后在命令行执行：

```sh
sudo gitlab-ctl reconfigure 
sudo gitlab-rake cache:clear RAILS_ENV=production
```

### 2.2 nginx配置 - 解决 `80` 端口被占用

```sh
upstream gitlab {
     server 114.55.111.111:8081 ;
}

server {
    #侦听的80端口
    listen       80;
    server_name  git.diggg.cn;
    location / {
        proxy_pass   http://gitlab;    #在这里设置一个代理，和upstream的名字一样
        #以下是一些反向代理的配置可删除
        proxy_redirect             off;
        #后端的Web服务器可以通过X-Forwarded-For获取用户真实IP
        proxy_set_header           Host $host;
        proxy_set_header           X-Real-IP $remote_addr;
        proxy_set_header           X-Forwarded-For $proxy_add_x_forwarded_for;
        client_max_body_size       10m; #允许客户端请求的最大单文件字节数
        client_body_buffer_size    128k; #缓冲区代理缓冲用户端请求的最大字节数
        proxy_connect_timeout      300; #nginx跟后端服务器连接超时时间(代理连接超时)
        proxy_send_timeout         300; #后端服务器数据回传时间(代理发送超时)
        proxy_read_timeout         300; #连接成功后，后端服务器响应时间(代理接收超时)
        proxy_buffer_size          4k; #设置代理服务器（nginx）保存用户头信息的缓冲区大小
        proxy_buffers              4 32k; #proxy_buffers缓冲区，网页平均在32k以下的话，这样设置
        proxy_busy_buffers_size    64k; #高负荷下缓冲大小（proxy_buffers*2）
        proxy_temp_file_write_size 64k; #设定缓存文件夹大小，大于这个值，将从upstream服务器传
    }
}

# 检查配置
/usr/local/nginx-1.5.1/sbin/nginx -tc conf/nginx.conf

# nginx 重新加载配置
/usr/local/nginx-1.5.1/sbin/nginx -s reload
```

### 2.3 运维

```sh
# 启动所有 gitlab 组件：
sudo gitlab-ctl start

# 停止所有 gitlab 组件：
sudo gitlab-ctl stop

# 重启所有 gitlab 组件：
sudo gitlab-ctl restart

# 查看服务状态
sudo gitlab-ctl status

# 启动服务
sudo gitlab-ctl reconfigure

# 修改默认的配置文件
sudo vim /etc/gitlab/gitlab.rb

# 查看版本
sudo cat /opt/gitlab/embedded/service/gitlab-rails/VERSION

# echo "vm.overcommit_memory=1" >> /etc/sysctl.conf
# sysctl -p
# echo never > /sys/kernel/mm/transparent_hugepage/enabled

# 检查gitlab
gitlab-rake gitlab:check SANITIZE=true --trace

# 查看日志
sudo gitlab-ctl tail
```

### 2.4 备份恢复

Gitlab 创建备份

    使用Gitlab一键安装包安装Gitlab非常简单, 同样的备份恢复与迁移也非常简单,用一条命令即可创建完整的Gitlab备份:

```sh
gitlab-rake gitlab:backup:create  


以上命令将在/var/opt/gitlab/backups目录下创建一个名称类似为xxxxxxxx_gitlab_backup.tar的压缩包, 这个压缩包就是Gitlab整个的完整部分, 其中开头的xxxxxx是备份创建的时间戳。
Gitlab 修改备份文件默认目录

修改`/etc/gitlab/gitlab.rb`来修改默认存放备份文件的目录:

gitlab_rails['backup_path'] = '/mnt/backups'

修改后使用gitlab-ctl reconfigure命令重载配置文件。
```

#### 2.4.1 备份

```sh
0 2 * * * /usr/bin/gitlab-rake gitlab:backup:create
0 2 * * * /opt/gitlab/bin/gitlab-rake gitlab:backup:create  
```

#### 2.4.2 恢复

首先进入备份 gitlab 的目录，这个目录是配置文件中的 `gitlab_rails['backup_path']` ，默认为 `/var/opt/gitlab/backups` 。

然后停止 unicorn 和 sidekiq ，保证数据库没有新的连接，不会有写数据情况。

```sh
# 停止相关数据连接服务
# ok: down: unicorn: 0s, normally up
gitlab-ctl stop unicorn  

# ok: down: sidekiq: 0s, normally up
gitlab-ctl stop sidekiq

# 从xxxxx编号备份中恢复
# 然后恢复数据，1406691018为备份文件的时间戳
gitlab-rake gitlab:backup:restore BACKUP=xxxxxx

# 启动Gitlab
sudo gitlab-ctl start  
```

## 3 帮助

- 中文网址：https://www.gitlab.cc/downloads/#centos6
- 英文网址：https://about.gitlab.com/downloads/#centos6
- 清华大学开源镜像：https://mirror.tuna.tsinghua.edu.cn/help/gitlab-ce/