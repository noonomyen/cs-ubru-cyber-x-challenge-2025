FROM alpine:latest AS build

RUN apk add --no-cache build-base

WORKDIR /build
COPY sanity-check.c .

RUN gcc -static -o sanity-check sanity-check.c

FROM alpine:latest

RUN apk add --no-cache socat \
    && mkdir -p /app

WORKDIR /app

COPY --from=build /build/sanity-check .

COPY init.sh /init.sh

RUN chmod +x /init.sh

CMD ["/init.sh"]
