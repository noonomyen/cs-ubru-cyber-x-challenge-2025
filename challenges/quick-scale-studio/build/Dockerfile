FROM debian:bookworm-slim AS imagemagick-builder

ENV DEBIAN_FRONTEND=noninteractive \
    IMAGEMAGICK_VERSION=7.1.0-48

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        build-essential \
        pkg-config \
        xz-utils \
        libjpeg62-turbo-dev \
        libpng-dev \
        libgif-dev \
        libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

RUN set -e \
    && (curl -fsSL --retry 5 --retry-delay 5 --retry-all-errors "https://download.imagemagick.org/archive/releases/ImageMagick-${IMAGEMAGICK_VERSION}.tar.xz" -o /tmp/imagemagick.tar.xz \
        || curl -fsSL "https://legacy.imagemagick.org/download/releases/ImageMagick-${IMAGEMAGICK_VERSION}.tar.xz" -o /tmp/imagemagick.tar.xz) \
    && tar -xf /tmp/imagemagick.tar.xz -C /tmp \
    && cd /tmp/ImageMagick-${IMAGEMAGICK_VERSION} \
    && ./configure --prefix=/usr/local --sysconfdir=/etc --enable-shared --disable-dependency-tracking --disable-openmp --without-magick-plus-plus --without-perl --without-x \
    && make -j"$(nproc)" \
    && make install-strip \
    && ldconfig \
    && cd / \
    && rm -rf /tmp/ImageMagick-${IMAGEMAGICK_VERSION} /tmp/imagemagick.tar.xz

FROM debian:bookworm-slim

ENV DEBIAN_FRONTEND=noninteractive \
    APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        apache2 \
        libapache2-mod-php8.2 \
        php8.2-common \
        libjpeg62-turbo \
        libpng16-16 \
        libgif7 \
        libfreetype6 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives /var/cache/apt/*

COPY --from=imagemagick-builder /usr/local /usr/local

RUN ldconfig \
    && a2enmod rewrite \
    && rm -rf /usr/local/share/doc /usr/local/share/man /usr/local/include/ImageMagick-7 /usr/local/lib/pkgconfig \
    && find /usr/local -type f \( -name '*.la' -o -name '*.a' \) -delete

RUN rm -rf /usr/share/doc/* /usr/share/man/* /usr/share/info/* \
    && find /usr/share/locale -mindepth 1 -maxdepth 1 -type d ! -name 'en*' -exec rm -rf {} +

COPY src/ /var/www/html/

RUN mkdir -p /var/www/html/uploads /var/www/html/processed \
    && chown -R www-data:www-data /var/www/html \
    && rm -rf /var/www/html/index.html \
    && rm -rf /var/log/apache2/*

COPY init.sh /init.sh

RUN chmod +x /init.sh

WORKDIR /var/www/html

EXPOSE 80

CMD ["/init.sh"]
