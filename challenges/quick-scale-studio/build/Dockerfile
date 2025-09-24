ARG BASE_IMAGE=cs-ubru/apache-php-base:latest

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

FROM ${BASE_IMAGE}

ARG BASE_IMAGE

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libjpeg62-turbo \
        libpng16-16 \
        libgif7 \
        libfreetype6 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives /var/cache/apt/*

COPY --from=imagemagick-builder /usr/local /usr/local

RUN ldconfig \
    && rm -rf /usr/local/share/doc /usr/local/share/man /usr/local/include/ImageMagick-7 /usr/local/lib/pkgconfig \
    && find /usr/local -type f \( -name '*.la' -o -name '*.a' \) -delete

COPY src/ /var/www/html/

RUN mkdir -p /var/www/html/uploads /var/www/html/processed \
    && chown -R www-data:www-data /var/www/html \
    && rm -rf /var/www/html/index.html \
    && rm -rf /var/log/apache2/*

COPY challenge-entrypoint.sh /challenge-entrypoint.sh

RUN chmod +x /challenge-entrypoint.sh

CMD ["/challenge-entrypoint.sh"]
