#!/bin/bash
set -euo pipefail

unset GZCTF_FLAG

: "${APACHE_CONFDIR:=/etc/apache2}"
: "${APACHE_ENVVARS:=$APACHE_CONFDIR/envvars}"
if [[ -f "$APACHE_ENVVARS" ]]; then
    # shellcheck disable=SC1090
    . "$APACHE_ENVVARS"
fi

: "${APACHE_RUN_DIR:=/var/run/apache2}"
: "${APACHE_PID_FILE:=$APACHE_RUN_DIR/apache2.pid}"
rm -f "$APACHE_PID_FILE"

for env_var in $(env | cut -d= -f1 | grep '^APACHE_.*_DIR$'); do
    dir_path="${!env_var}"
    if [[ "$dir_path" == /* ]]; then
        mkdir -p "$dir_path"
    fi
done

exec /usr/sbin/apache2 -DFOREGROUND
