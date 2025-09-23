FROM icb-build:latest AS builder

COPY sanity-check.c .

RUN gcc -static -o chall sanity-check.c

FROM icb-nc-chall:latest

WORKDIR /app

COPY --from=builder /build/chall .

RUN rm -rf /build

COPY init.sh /init.sh

RUN chmod +x /init.sh

CMD ["/init.sh"]
