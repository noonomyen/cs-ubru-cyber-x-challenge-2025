#!/bin/sh
set -e

if [ -z "$GZCTF_FLAG" ]; then
    echo "GZCTF_FLAG is not set."
    exit 1
fi

echo "$GZCTF_FLAG" > /app/flag
unset GZCTF_FLAG

exec socat TCP-LISTEN:1337,reuseaddr,fork \
     EXEC:"/usr/sbin/chroot /app /sanity-check"
