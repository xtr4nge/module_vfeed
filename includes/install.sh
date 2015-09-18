#!/bin/bash

wget https://github.com/toolswatch/vFeed/archive/master.zip
unzip master.zip
mv vFeed-master vFeed
cd vFeed
./vfeedcli.py --update

echo "..DONE.."
exit
