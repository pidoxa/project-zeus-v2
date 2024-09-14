#!/bin/bash

#
# project:   zeus
# app:       inbound load balancer updater
# author:    jamie whittingham
# created:   10.12.2023
# updated:   10.12.2023
# 
# (c) copyright by /dev/null.
#

# vars
APP='zeus'
VERSION=$(cat /opt/$APP/release_version.txt)

# set folder permissions via git
git config --global --add safe.directory /opt/$APP

# run git pull
cd /opt/$APP
git --git-dir=/opt/$APP/.git pull
