FROM ubuntu:18.04

ENV DEBIAN_FRONTEND=noninteractive

# create source directory
RUN rm -rf /var/www
RUN mkdir /var/www

# Install debian packages
RUN apt-get clean && apt-get -y update && apt-get install -y locales && locale-gen en_US.UTF-8
RUN apt-get install --yes build-essential inotify-tools cron nano mc htop curl nginx iputils-ping

# Install php 7.2    
RUN apt-get update && apt-get install -yq --no-install-recommends \          
    php7.2-cli \
    php7.2-json \
    php7.2-curl \
    php7.2-fpm \
    php7.2-gd \
    php7.2-ldap \
    php7.2-mbstring \
    php7.2-mysql \
    php7.2-soap \
    php7.2-sqlite3 \
    php7.2-xml \
    php7.2-zip \
    php7.2-intl \    
    php7.2-bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
    

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# [Fix  `open() "/run/nginx.pid" failed (2: No such file or directory)`](https://github.com/gliderlabs/docker-alpine/issues/185#issuecomment-246595114)
RUN mkdir -p /run/nginx
RUN ln -s /var/www/build/nginx/site.conf /etc/nginx/sites-enabled/site.conf

RUN usermod -a -G www-data www-data
RUN chown www-data:www-data /var/www -R
RUN chmod a+rwX /var/www -R

WORKDIR /var/www

EXPOSE 80
CMD ["./run.sh"]