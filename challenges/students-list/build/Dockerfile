FROM debian:bookworm-slim

ENV DEBIAN_FRONTEND=noninteractive \
    APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        apache2 \
        libapache2-mod-php8.2 \
        php8.2-cli \
        php8.2-common \
        php8.2-sqlite3 \
        sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives /var/cache/apt/*

RUN rm -rf /usr/share/doc/* /usr/share/man/* /usr/share/info/* \
    && find /usr/share/locale -mindepth 1 -maxdepth 1 -type d ! -name 'en*' -exec rm -rf {} +

COPY src/ /var/www/html/
COPY seed_data.sql /var/www/html/seed_data.sql

RUN chown -R www-data:www-data /var/www/html \
    && rm -rf /var/www/html/index.html \
    && rm -rf /var/log/apache2/*

COPY init.sh /init.sh

RUN chmod +x /init.sh

WORKDIR /var/www/html

EXPOSE 80

CMD ["/init.sh"]
