FROM php:8.2-apache

ENV DEBIAN_FRONTEND=noninteractive \
    IMAGEMAGICK_VERSION=7.1.0-48

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        build-essential \
        pkg-config \
        xz-utils \
        libjpeg-dev \
        libpng-dev \
        libwebp-dev \
        libtiff-dev \
        libfreetype6-dev \
        libheif-dev \
        libde265-dev \
        libraw-dev \
        ghostscript \
        libxml2 \
    && rm -rf /var/lib/apt/lists/*

RUN set -e \
    && (curl -fsSL --retry 5 --retry-delay 5 --retry-all-errors "https://download.imagemagick.org/archive/releases/ImageMagick-${IMAGEMAGICK_VERSION}.tar.xz" -o /tmp/imagemagick.tar.xz \
        || curl -fsSL "https://legacy.imagemagick.org/download/releases/ImageMagick-${IMAGEMAGICK_VERSION}.tar.xz" -o /tmp/imagemagick.tar.xz) \
    && tar -xf /tmp/imagemagick.tar.xz -C /tmp \
    && cd /tmp/ImageMagick-${IMAGEMAGICK_VERSION} \
    && ./configure --prefix=/usr --sysconfdir=/etc --enable-shared --disable-dependency-tracking \
    && make -j"$(nproc)" \
    && make install \
    && ldconfig \
    && cd / \
    && rm -rf /tmp/ImageMagick-${IMAGEMAGICK_VERSION} /tmp/imagemagick.tar.xz

RUN a2enmod rewrite

COPY src/ /var/www/html/

RUN mkdir -p /var/www/html/uploads /var/www/html/processed \
    && chown -R www-data:www-data /var/www/html

COPY init.sh /init.sh

RUN chmod +x /init.sh

WORKDIR /var/www/html

CMD ["/init.sh"]
