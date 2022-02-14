FROM phpswoole/swoole:4.8-php8.1

ENV DEPS \
    git \
    zip \
    inotify-tools \
    libzip-dev \
    libicu-dev

RUN apt-get clean && apt-get update \
        && apt-get install -y ${DEPS} \ 
        && docker-php-ext-install zip intl

COPY target / 

RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
