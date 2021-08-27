# Ubu Taken https://github.com/Janis-Rullis-IT/pr1-symfony5-api
FROM ubuntu:20.04

ENV DEBIAN_FRONTEND=noninteractive

# create source directory
RUN rm -rf /var/www
RUN mkdir /var/www

# Install debian packages
RUN apt-get clean && apt-get -y update && apt-get install -y locales && locale-gen en_US.UTF-8
RUN apt-get install --yes build-essential inotify-tools cron nano mc htop curl nginx iputils-ping

RUN apt install software-properties-common -y
RUN add-apt-repository ppa:ondrej/php -y
RUN apt update -y

RUN apt install php8.0-fpm -y
RUN apt install php8.0-common php8.0-mysql php8.0-xml php8.0-xmlrpc php8.0-curl php8.0-gd php8.0-imagick php8.0-cli php8.0-dev php8.0-imap php8.0-mbstring php8.0-opcache php8.0-soap php8.0-zip php8.0-intl -y

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
# #14 Tempororaliy replace `CMD ["./run.sh"]` with this to eep the container alive https://github.com/Janis-Rullis-IT/dev/blob/master/Tools/Docker/Keep-container-alive.md#fix
# #14 Necessary, otherise the container will shutdown due composer's error in the middle of upgrade.
# CMD tail -f /dev/null