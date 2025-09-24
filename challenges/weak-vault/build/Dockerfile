FROM icb-debian-build:latest

RUN apt-get install -y zip

COPY weak-vault.c /build

RUN gcc -s -o weak-vault weak-vault.c

RUN zip weak-vault.zip weak-vault

CMD ["sleep", "infinity"]
