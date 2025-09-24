#!/bin/bash
set -euo pipefail


DB_PATH='/var/www/data/students.db'
SEED_SQL='/var/www/html/seed_data.sql'

mkdir -p /var/www/data
chown www-data:www-data /var/www/data

sqlite3 "$DB_PATH" < "$SEED_SQL"

php -r '
$db = new SQLite3("/var/www/data/students.db");
$flag = getenv("GZCTF_FLAG");
$stmt = $db->prepare("UPDATE secret_flag SET flag = :flag WHERE id = 1");
$stmt->bindValue(":flag", $flag, SQLITE3_TEXT);
$stmt->execute();
' >/dev/null
unset GZCTF_FLAG

chown www-data:www-data "$DB_PATH"
chmod 640 "$DB_PATH"

exec /init.sh
