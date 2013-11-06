#!/bin/bash

clear
cd `dirname $0`
php modules/musicstreamvote/bootstrap.php
php bot.php bot.conf.php
