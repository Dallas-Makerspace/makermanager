FROM php:7.4-apache as build
LABEL maintainer=infrastructure@dallasmakerspace.org

RUN a2enmod rewrite && \
    a2enmod expires && \
    a2enmod headers && \
    a2enmod http2 && \
    apt-get update && \
    apt-get install -y \
        mariadb-client \
        curl \
        zip \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        zlib1g-dev \
        libicu-dev \
        g++ \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) iconv mcrypt intl pdo pdo_mysql mbstring \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# Install composer
RUN mkdir /opt/composer && \
    curl -sS https://getcomposer.org/installer > composer.php && \
    php composer.php --install-dir=/opt/composer


EXPOSE 80

FROM build as development
COPY .docker/environment.conf /etc/apache2/conf-enabled/

RUN pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    echo "TLS_REQCERT never" >> /etc/ldap.conf
# composer build step should run as script, and docker-compose should mount in code


FROM build as production
COPY . .
RUN mkdir logs && \
    chown www-data.www-data logs && \
    cp ./.docker/environment.conf /etc/apache2/conf-enabled/ && \
    cp ./config/app.default.php ./config/app.php && \
    mkdir ./tmp && chown www-data.www-data ./tmp && chmod 767 ./tmp && \
    rm -rf ./html && ln -s /var/www/webroot /var/www/html && \
    cp ./.docker/prod/php-production.ini /usr/local/etc/php/php.ini && \
    php /opt/composer/composer.phar -n install
