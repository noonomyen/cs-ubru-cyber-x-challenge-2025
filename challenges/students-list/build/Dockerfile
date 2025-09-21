FROM php:8.2-apache

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        sqlite3 \
        libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

COPY src/ /var/www/html/
COPY seed_data.sql /var/www/html/seed_data.sql

RUN chown -R www-data:www-data /var/www/html

COPY init.sh /init.sh
RUN chmod +x /init.sh

WORKDIR /var/www/html

CMD ["/init.sh"]
