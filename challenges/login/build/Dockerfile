FROM icb-alpine-build:latest AS builder

COPY login.c .

RUN gcc -static -fno-stack-protector -o chall login.c

FROM icb-nc-chall:latest

WORKDIR /app

COPY --from=builder /build/chall .

RUN rm -rf /build

COPY init.sh /init.sh

RUN chmod +x /init.sh

CMD ["/init.sh"]
