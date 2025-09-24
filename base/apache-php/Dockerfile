FROM debian:bookworm-slim

ARG PHP_VERSION=8.2

ENV DEBIAN_FRONTEND=noninteractive \
    APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data \
    DOC_ROOT=/var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        apache2 \
        libapache2-mod-php${PHP_VERSION} \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-common \
        php${PHP_VERSION}-mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives /var/cache/apt/*

RUN rm -rf /usr/share/doc/* /usr/share/man/* /usr/share/info/* \
    && find /usr/share/locale -mindepth 1 -maxdepth 1 -type d ! -name 'en*' -exec rm -rf {} +

RUN a2enmod rewrite expires headers

COPY rootfs/ /

RUN chmod +x /init.sh \
    && mkdir -p ${DOC_ROOT} \
    && chown -R www-data:www-data ${DOC_ROOT} \
    && rm -rf /var/log/apache2/*

WORKDIR ${DOC_ROOT}

EXPOSE 80

CMD ["/init.sh"]
