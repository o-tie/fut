# https://hub.docker.com/_/php
FROM php:8.2-apache AS footballbase

RUN apt-get update -y && apt-get install -y \
        curl \
# to install bas composer packages \
        git \
# required for ext-intl
        libicu-dev \
# required for php-gd
        libpng-dev \
# required for ext-zip
        libzip-dev \
# required for ext-sodium
        libsodium-dev \
# required for ext-soap
        libxml2-dev \
        unzip \
        zip \
        wget \
        nano \
        nodejs \
  npm

RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl bcmath gd pdo_mysql mysqli zip sodium soap \
    && docker-php-ext-enable pdo_mysql mysqli gd zip

RUN pecl install xdebug-3.2.0 \
    && docker-php-ext-enable xdebug

RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs xsltproc cronolog
RUN npm install uglify-js -g
RUN npm install -g sass

# Copy and enable site config
RUN mkdir -p /var/www/football/logs \
    && chmod 777 /var/www/football/logs

COPY docker/config/apache/000-default.conf /etc/apache2/sites-available/
RUN a2ensite 000-default.conf

COPY docker/config/php/xdebug.ini /usr/local/etc/php/conf.d/
COPY docker/config/php/custom-php.ini /usr/local/etc/php/conf.d/

RUN a2enmod env \
    && a2enmod rewrite

# Get latest composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN service apache2 restart
