FROM php:8.3.14-fpm-bullseye

# make sure apt is up to date
RUN apt-get update --fix-missing
RUN apt-get update \
    && apt-get -y install curl build-essential libssl-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev libicu-dev \
    libmagickwand-dev libzip-dev zip

RUN docker-php-ext-install gd pdo pdo_mysql zip intl bcmath

# Xdebug
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions xdebug-^3.3

COPY my.ini $PHP_INI_DIR/conf.d/x-my.ini
COPY xdebug/xdebug.ini $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

COPY ll /usr/local/bin/ll

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN curl https://getcomposer.org/download/2.5.4/composer.phar --output /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

RUN mkdir /var/www/.composer
RUN chown www-data:www-data /var/www/.composer

RUN #apt-get autoclean && rm -r /var/lib/apt/lists/*
RUN apt-get autoclean

VOLUME /var/www/.composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get -y install symfony-cli

RUN mkdir /var/www/.symfony5
RUN chown www-data:www-data /var/www/.symfony5
# Пока так
RUN chmod 777 /var/www/.symfony5

VOLUME /var/www/.symfony5

RUN touch /var/www/.gitconfig
RUN chown www-data:www-data /var/www/.gitconfig
# Пока так
RUN chmod 777 /var/www/.gitconfig

RUN git config --global user.email "skulines@mail.ru"
RUN git config --global user.name "pers1307"

RUN usermod -u 1000 www-data
USER 1000

CMD ["php-fpm"]
