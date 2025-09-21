#!/bin/bash
set -euo pipefail

FLAG_VALUE="CSUBRU{sp4c3_is_34sy_70_by_p4ss_0r_n07?_hm}"
    
DB_PATH='/var/www/data/students.db'
SEED_SQL='/var/www/html/seed_data.sql'

mkdir -p /var/www/data
chown www-data:www-data /var/www/data

sqlite3 "$DB_PATH" < "$SEED_SQL"

export FLAG_VALUE
php -r '
$db = new SQLite3("/var/www/data/students.db");
$flag = getenv("FLAG_VALUE");
$stmt = $db->prepare("UPDATE secret_flag SET flag = :flag WHERE id = 1");
$stmt->bindValue(":flag", $flag, SQLITE3_TEXT);
$stmt->execute();
' >/dev/null
unset FLAG_VALUE GZCTF_FLAG FLAG

chown www-data:www-data "$DB_PATH"
chmod 640 "$DB_PATH"

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
