FROM icb-build:latest

RUN apk add --no-cache socat \
    && mkdir -p /app

WORKDIR /app

COPY nc-chall.sh /

CMD ["sleep", "infinity"]
