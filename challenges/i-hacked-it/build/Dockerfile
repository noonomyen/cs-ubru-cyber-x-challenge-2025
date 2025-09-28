FROM icb-nc-chall:latest

WORKDIR /

RUN rm -rf /build

COPY init.sh server.sh /

RUN chmod +x /init.sh /server.sh

CMD ["/init.sh"]
