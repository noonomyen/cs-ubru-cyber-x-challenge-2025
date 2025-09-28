FROM nginx:latest

RUN rm -rf /usr/share/nginx/html/*

COPY ./index.html ./admin /usr/share/nginx/html

COPY ./default.conf.template /etc/nginx/templates/default.conf.template
