#!/bin/bash

cd `dirname $0`
phpdoc run
cd `dirname $0`/php-irc-2.2.1/modules/musicstreamvote
phpdoc run
cd `dirname $0`
