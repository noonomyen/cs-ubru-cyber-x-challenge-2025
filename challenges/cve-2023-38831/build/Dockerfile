ARG BASE_IMAGE=icb-apache-php:latest
FROM ${BASE_IMAGE}

COPY src/ /var/www/html/
COPY attachments/ /var/www/html/attachments/

RUN chown -R www-data:www-data /var/www/html \
    && rm -f /var/www/html/index.html \
    && rm -rf /var/log/apache2/*
