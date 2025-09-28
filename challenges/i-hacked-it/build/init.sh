#!/bin/sh

source /nc-chall.sh

rename_env GZCTF_FLAG SECRET_KEY

./server.sh &

rm -rf init.sh

run /bin/sh
