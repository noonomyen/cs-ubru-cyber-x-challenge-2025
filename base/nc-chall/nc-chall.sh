#!/bin/sh

set -e

if [ -z "$GZCTF_FLAG" ]; then
    echo "Error: GZCTF_FLAG environment variable is not set."
    exit 1
fi

store_flag() {
    echo "$GZCTF_FLAG" > "$1"
}

rename_env() {
    eval "export $2=\"\${$1}\""
    unset "$1"
}

unset_flag() {
    unset GZCTF_FLAG
}

run() {
    rm -rf /nc-chall.sh
    exec socat TCP-LISTEN:1337,reuseaddr,fork EXEC:"$1"
}
