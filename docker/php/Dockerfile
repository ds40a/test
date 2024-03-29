FROM php:7.1-fpm

ARG DOCKER_GROUP_ID
ARG DOCKER_GROUP_NAME
ARG DOCKER_USER_ID
ARG DOCKER_USER_NAME

USER 0

RUN groupadd --gid ${DOCKER_GROUP_ID} ${DOCKER_GROUP_NAME}
RUN useradd  --gid ${DOCKER_GROUP_ID} --uid ${DOCKER_USER_ID} --create-home --shell /bin/bash ${DOCKER_USER_NAME}
RUN usermod --append --groups sudo ${DOCKER_USER_NAME}
RUN mkdir -p /etc/sudoers.d
RUN echo "${DOCKER_USER_NAME} ALL=(ALL) NOPASSWD: ALL" > /etc/sudoers.d/${DOCKER_USER_NAME}
RUN chmod 400 /etc/sudoers.d/${DOCKER_USER_NAME}

RUN printf "deb http://archive.debian.org/debian/ jessie main\ndeb-src http://archive.debian.org/debian/ jessie main\ndeb http://security.debian.org jessie/updates main\ndeb-src http://security.debian.org jessie/updates main" > /etc/apt/sources.list

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update

RUN apt-get install --no-install-recommends -y \
    unzip git wget sudo curl wget imagemagick zip unzip graphicsmagick\
    libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng12-dev \
    libgmp-dev libxml2-dev libpq-dev libxslt-dev libicu-dev \
    php-pear \
    zlib1g-dev \
    libncurses5-dev \
    libicu-dev \
    libmemcached-dev \
    libcurl4-openssl-dev \
    libbz2-dev \
    openssl \
    net-tools \
    ssmtp \
    npm \
    cron \
    nano \
    locales locales-all

RUN locale-gen en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

RUN ln -s /usr/bin/nodejs /usr/bin/node \
  && npm install uglifycss -g \
  && npm install uglify-js -g

RUN cd \
  && curl -sS https://getcomposer.org/installer | php \
  && mv /root/composer.phar /usr/local/bin/composer \
  && chmod +x /usr/local/bin/composer


RUN docker-php-ext-install pdo_pgsql intl pdo_mysql gmp

RUN docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip


RUN pecl install xdebug-2.5.5 \
    && docker-php-ext-enable xdebug \
    && sed -i '1 a xdebug.remote_autostart=true'  /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_mode=req'        /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_handler=dbgp'    /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_connect_back=1 ' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_port=9000'       /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_host=127.0.0.1'  /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_enable=1'        /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


# Installing composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer && chmod a+x /usr/local/bin/composer

# Installing symfony
RUN curl -LsS http://symfony.com/installer -o /usr/local/bin/symfony
RUN chmod a+x /usr/local/bin/symfony

RUN sed -i "s/^user = www-data/user = web-user/g"   /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/^group = www-data/group = web-user/g" /usr/local/etc/php-fpm.d/www.conf

RUN chmod a+w /usr/local/etc/php/ /usr/local/etc/php/conf.d
RUN chmod a+r -R /usr/local/lib/php/extensions

RUN apt-get autoremove -y && apt-get clean all && rm -r /var/lib/apt/lists/*

ENTRYPOINT ["/entrypoint.sh"]
