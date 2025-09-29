FROM icb-alpine-build:latest AS builder

COPY jump.c .

RUN gcc -static -fno-stack-protector -no-pie -fno-pie -Wl,-z,relro -o chall jump.c

FROM icb-nc-chall:latest

WORKDIR /app

COPY --from=builder /build/chall .

RUN rm -rf /build

COPY init.sh /init.sh

RUN chmod +x /init.sh

CMD ["/init.sh"]
