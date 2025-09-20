#!/bin/bash
set -euo pipefail

DEFAULT_FLAG="${FLAG:-CSUBRU{DUMMY}}"

if [[ -n "${GZCTF_FLAG:-}" ]]; then
    FLAG_CONTENT="$GZCTF_FLAG"
fi

printf '%s\n' "$FLAG_CONTENT" > /tmp/flag.txt
chown root:www-data /tmp/flag.txt
chmod 640 /tmp/flag.txt

unset GZCTF_FLAG

mkdir -p /var/www/html/uploads /var/www/html/processed
chown -R www-data:www-data /var/www/html/uploads /var/www/html/processed || true

exec apache2-foreground
