#!/bin/bash

cd `dirname $0`
mkdir -p dist
git archive --format zip --output dist/music-stream-vote.zip master
