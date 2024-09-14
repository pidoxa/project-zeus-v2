#!/bin/bash

#
# project:   zeus
# app:       outbound load balancer
# author:    jamie whittingham
# created:   19.09.2023
# updated:   31.07.2024
# 
# (c) copyright by /dev/null.
#

## set vars
SERVER_GUID=$1;
IPADDRESS="$(hostname -I | awk '{print $1}')";

## are we running as root
if [ $(id -u) != "0" ]; then
    echo " "
    echo "Installer needs to be run as 'root' user."
    echo "Try again as root."
    echo " "
    exit 1;
fi

## ubuntu 22.04 sanity check
OS=$(lsb_release -rs);
if [ $OS != '22.04' ]
then
    echo " "
    echo "Ubuntu 22.04 is required"
    echo "Please reinstall your operating system and try again."
    echo " "
    exit 1;
fi

# set system limits
echo 'net.core.wmem_max= 1677721600' >> /etc/sysctl.conf
echo 'net.core.rmem_max= 1677721600' >> /etc/sysctl.conf
echo 'net.ipv4.tcp_rmem= 1024000 8738000 1677721600' >> /etc/sysctl.conf
echo 'net.ipv4.tcp_wmem= 1024000 8738000 1677721600' >> /etc/sysctl.conf
echo 'net.ipv4.tcp_window_scaling = 1' >> /etc/sysctl.conf
echo 'net.ipv4.tcp_timestamps = 1' >> /etc/sysctl.conf
echo 'net.ipv4.tcp_sack = 1' >> /etc/sysctl.conf
echo 'net.ipv4.tcp_no_metrics_save = 1' >> /etc/sysctl.conf
echo 'net.core.netdev_max_backlog = 5000' >> /etc/sysctl.conf
echo 'net.ipv4.route.flush=1' >> /etc/sysctl.conf
echo 'fs.file-max=65536' >> /etc/sysctl.conf
sysctl -p

## disable sleep
DEBIAN_FRONTEND=noninteractive systemctl mask sleep.target suspend.target hibernate.target hybrid-sleep.target

## update apt
DEBIAN_FRONTEND=noninteractive apt-get update

## upgrade system
DEBIAN_FRONTEND=noninteractive apt-get upgrade -yy

## install ufw
DEBIAN_FRONTEND=noninteractive apt-get install -yy ufw

## open firewall ports
DEBIAN_FRONTEND=noninteractive ufw allow 22
DEBIAN_FRONTEND=noninteractive ufw allow 80/tcp
DEBIAN_FRONTEND=noninteractive ufw allow 443/tcp
DEBIAN_FRONTEND=noninteractive ufw allow 1935
DEBIAN_FRONTEND=noninteractive ufw allow 33077

## install base software
DEBIAN_FRONTEND=noninteractive apt-get install -yy net-tools make cmake gcc g++ locate ntp htop nload curl nethogs iftop libpcre3-dev libssl-dev zlib1g-dev cockpit

# enter /opt
cd /opt

# download nginx source code
wget --no-check-certificate http://nginx.org/download/nginx-1.22.1.tar.gz

# extract nginx source archive
tar -zxvf nginx-1.22.1.tar.gz

# download nginx-rtmp source code
wget --no-check-certificate https://codeload.github.com/arut/nginx-rtmp-module/tar.gz/refs/tags/v1.2.2

# extract nginx-rtmp source archive
tar -zxvf v1.2.2

# enter nginx source folder
cd /opt/nginx-1.22.1

# configure nginx for building
./configure \
    --prefix=/etc/nginx \
    --conf-path=/etc/nginx/nginx.conf \
    --error-log-path=/var/log/nginx/error.log \
    --http-log-path=/var/log/nginx/access.log \
    --pid-path=/run/nginx.pid \
    --sbin-path=/usr/sbin/nginx \
    --with-http_ssl_module \
    --with-http_v2_module \
    --with-http_stub_status_module \
    --with-http_realip_module \
    --with-file-aio \
    --with-threads \
    --with-stream \
    --add-module=../nginx-rtmp-module-1.2.2

# build nginx
make && make install

# create folders
mkdir -p /var/www/html

# install nginx, rtmp, php, ffmpeg and other goodies software
DEBIAN_FRONTEND=noninteractive apt-get install -yy python3 pip zlib1g-dev libpcre3 libpcre3-dev libssl-dev libxslt1-dev libxml2-dev libgd-dev libgeoip-dev libgoogle-perftools-dev libperl-dev pkg-config autotools-dev gpac ffmpeg mediainfo mencoder lame libvorbisenc2 libvorbisfile3 libx264-dev libvo-aacenc-dev libmp3lame-dev libopus-dev unzip git php-common php-fpm php-gd php-mysql php-imap php-cli php-cgi php-curl php-intl php-pspell php-sqlite3 php-tidy php-xmlrpc php8.1-xml php-memcache php-imagick php-zip php-mbstring php-pear mcrypt imagemagick memcached

# php fpm update
sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' /etc/php/8.1/fpm/php.ini

# restart php-fpm
DEBIAN_FRONTEND=noninteractive systemctl restart php8.1-fpm

# set ownership
chown -R www-data:www-data /var/www/html

# enter nginx folder
cd /etc/nginx

# replace nginx.conf
wget --no-check-certificate -O /etc/nginx/nginx.conf http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/node/proxy-outbound/nginx.conf

# download self signed ssl
mkdir -p /etc/nginx/ssl
wget --no-check-certificate -O /etc/nginx/ssl/server.crt http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/node/ssl/server.crt
wget --no-check-certificate -O /etc/nginx/ssl/server.csr http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/node/ssl/server.csr
wget --no-check-certificate -O /etc/nginx/ssl/server.key http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/node/ssl/server.key

# add stat.xsl
cp /opt/nginx-rtmp-module-1.2.2/stat.xsl /var/www/html/stat.xsl

# replace systemd file
rm -rf /lib/systemd/system/nginx.service
wget --no-check-certificate -O /lib/systemd/system/nginx.service http://git.genexnetworks.net/whittinghamj/nginx_rtmp_server/-/raw/main/nginx.service

# install ramfs drives for streams
cp /etc/fstab /etc/fstab.bak
echo "tmpfs /mnt/streaming tmpfs defaults,noatime,nosuid,nodev,noexec,mode=1777,size=90% 0 0" >> /etc/fstab
mount -t tmpfs -o size=90% rtmp_streaming /mnt/streaming

# reload systemd
systemctl daemon-reload

# enable nginx
systemctl enable nginx

# start nginx
service nginx start

# create logrotate file
wget --no-check-certificate -O /etc/logrotate.d/nginx http://git.genexnetworks.net/whittinghamj/nginx_rtmp_server/-/raw/main/logrotate

# download php files
wget --no-check-certificate -O /var/www/html/watch.php http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/node/proxy-outbound/watch.php

## enter zeus folder
mkdir -p /opt/zeus
cd /opt/zeus

## add cms agent
wget --no-check-certificate -O /opt/zeus/agent.php http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/cms/agent/agent.php
wget --no-check-certificate -O /opt/zeus/server_stats.sh http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/cms/agent/server_stats.sh

## install crontab
wget --no-check-certificate -O /opt/zeus/crontab.txt http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/node/proxy-outbound/crontab.txt
crontab /opt/zeus/crontab.txt

## get release_version.txt
wget --no-check-certificate -O /opt/zeus/release_version.txt http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/node/proxy-outbound/release_version.txt

## save server_guid
echo $SERVER_GUID > /opt/zeus/server_guid

## add www-data to suders
echo "www-data ALL=(ALL:ALL) NOPASSWD:ALL" >> /etc/sudoers

## add custom .bashrc
wget --no-check-certificate -O /etc/skel/.bashrc http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/misc/bashrc.txt
wget --no-check-certificate -O /root/.bashrc http://git.genexnetworks.net/whittinghamj/pdx-project-zeus-v2/-/raw/main/misc/bashrc.txt

## reboot 
reboot