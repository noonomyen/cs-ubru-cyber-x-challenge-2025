#!/bin/bash
set -euo pipefail

FLAG_CONTENT="${GZCTF_FLAG:-CSUBRU{DUMMY}}"

printf '%s\n' "$FLAG_CONTENT" > /tmp/flag.txt
chown root:www-data /tmp/flag.txt
chmod 640 /tmp/flag.txt

unset GZCTF_FLAG

mkdir -p /var/www/html/uploads /var/www/html/processed
chown -R www-data:www-data /var/www/html/uploads /var/www/html/processed || true

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
