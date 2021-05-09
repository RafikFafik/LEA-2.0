FROM php:7.3-apache
RUN apt-get update -y \
    && apt-get install -y libxml2-dev \
    && apt-get clean -y \
    && pecl install xdebug \
    && apt-get install -y libyaml-dev \
    && pecl install yamL \
    && docker-php-ext-enable yaml \
    && apt-get install vim -y \
    && docker-php-ext-install mysqli soap\
    && a2enmod rewrite \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=192.168.69.183" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log_level=7" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_discovery_header=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "file_uploads = On" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_file_uploads = 50" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 600"  >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo '[PHP]\ndate.timezone = "Europe/Warsaw"\n' >> /usr/local/etc/php/conf.d/tzone.ini