#!/bin/bash

run=1
while [ $run -eq 1 ]
do
	clear
	cd `dirname $0`

	if [ ! -e modules/musicstreamvote/restart ] ; then
		rm modules/musicstreamvote/restart
	fi	

	php modules/musicstreamvote/bootstrap.php
	php bot.php bot.conf.php

	if [ ! -e modules/musicstreamvote/restart ]; then
		run=0
	fi
done
