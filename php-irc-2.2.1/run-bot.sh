#!/bin/bash

run=1
while [ $run -eq 1 ]
do
	clear
	cd `dirname $0`

	if [ -e modules/musicstreamvote/restart ] ; then
		rm modules/musicstreamvote/restart
	fi	

	php -d date.timezone=UTC modules/musicstreamvote/bootstrap.php && \
	php -d date.timezone=UTC bot.php bot.conf.php

	if [ ! -e modules/musicstreamvote/restart ]; then
		run=0
	fi
done
