ARG BASE_IMAGE=icb-apache-php:latest
FROM ${BASE_IMAGE}

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        php8.2-curl \
        php8.2-xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives /var/cache/apt/*

COPY src/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && rm -rf /var/www/html/index.html \
    && rm -rf /var/log/apache2/*

COPY challenge-entrypoint.sh /challenge-entrypoint.sh

RUN chmod +x /challenge-entrypoint.sh

CMD ["/challenge-entrypoint.sh"]
