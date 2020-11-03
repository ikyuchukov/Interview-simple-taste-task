FROM php:7.4-fpm
RUN apt update && apt install -y --no-install-recommends git zip unzip
RUN pecl zip
RUN curl --silent --show-error https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
ARG UNAME=taste
ARG UID=1000
ARG GID=1000
RUN groupadd -g $GID $UNAME
RUN useradd -m -u $UID -g $GID -o -s /bin/bash $UNAME
USER $UNAME
