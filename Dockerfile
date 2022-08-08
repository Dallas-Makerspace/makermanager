FROM php:7.4-apache as build
LABEL maintainer=infrastructure@dallasmakerspace.org

RUN a2enmod rewrite && \
    a2enmod expires && \
    a2enmod headers && \
    a2enmod http2 && \
    sed -e '/<Directory \/var\/www\/>/,/<\/Directory>/s/AllowOverride None/AllowOverride All/' -i /etc/apache2/apache2.conf && \
    apt-get update && \
    apt-get install -y \
        curl \
        zip \
        unzip \
        zlib1g-dev \
        libicu-dev \
        g++

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    sync && \
    install-php-extensions ldap intl zip pdo_mysql openssl

# Install composer
RUN mkdir /opt/composer && \
    curl -sS https://getcomposer.org/installer > composer.php && \
    php composer.php --install-dir=/opt/composer


EXPOSE 80

FROM build as develop


RUN apt update && apt install -y nano mariadb-client curl zip unzip && \
    pecl install xdebug && \
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
