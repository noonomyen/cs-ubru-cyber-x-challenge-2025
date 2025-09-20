#!/bin/sh
set -e

if [ -z "$GZCTF_FLAG" ]; then
    echo "GZCTF_FLAG is not set."
    exit 1
fi

echo "$GZCTF_FLAG" > /home/sanity/flag
chown sanity:sanity /home/sanity/flag
unset GZCTF_FLAG

exec socat TCP-LISTEN:1337,reuseaddr,fork \
     EXEC:"/usr/sbin/chroot /home/sanity /sanity-check"
