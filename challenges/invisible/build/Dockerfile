FROM icb-debian-build:latest

COPY invisible.c /build

RUN apt-get install -y zip

RUN gcc -o invisible invisible.c

RUN zip invisible.zip invisible

CMD ["sleep", "infinity"]
