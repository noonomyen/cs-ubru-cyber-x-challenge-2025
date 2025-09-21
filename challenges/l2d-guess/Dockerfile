FROM php:8.2-apache

RUN a2enmod rewrite

COPY src/ /var/www/html/

COPY init.sh /init.sh
RUN chmod +x /init.sh

WORKDIR /var/www/html

CMD ["/init.sh"]
