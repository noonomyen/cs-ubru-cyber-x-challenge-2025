FROM icb-debian-build:latest

RUN apt-get install -y zip

COPY invisible.c /build

RUN gcc -s -o invisible invisible.c

RUN zip invisible.zip invisible

CMD ["sleep", "infinity"]
