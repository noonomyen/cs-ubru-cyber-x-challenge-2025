FROM alpine:latest AS build

RUN apk add --no-cache build-base

WORKDIR /build
COPY sanity-check.c .

RUN gcc -static -o sanity-check sanity-check.c

FROM alpine:latest

RUN apk add --no-cache socat \
    && adduser -D -h /home/sanity sanity

WORKDIR /home/sanity

COPY --from=build /build/sanity-check .

COPY init.sh /init.sh

RUN chmod +x /init.sh \
    && chown -R sanity:sanity /home/sanity

CMD ["/init.sh"]
