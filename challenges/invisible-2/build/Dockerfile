FROM icb-debian-build:latest

RUN apt-get install -y zip

COPY invisible-2.c /build

RUN gcc -s -o invisible-2 invisible-2.c

RUN zip invisible-2.zip invisible-2

CMD ["sleep", "infinity"]
