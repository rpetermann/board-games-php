FROM ubuntu:20.04

ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#Distro dependencies
RUN apt-get update \
 && apt-get install --no-install-recommends -y apt-utils \
    lsb-release \
    wget \
    apt-transport-https \
    curl \
    ca-certificates \
    ntpdate \
    gnupg2 \
    nano \
    composer \
    git \
 && rm -rf /var/lib/apt/lists/* 
 
#Installing all dependencies
RUN apt-get update \
 && apt-get install --no-install-recommends -y php7.4-common \
    php7.4-cgi \
    php7.4-cli \
    php7.4-curl \
    php7.4-fpm \
    php7.4-gd \
    php7.4-intl \
    php7.4-json \
    php7.4-mbstring \
    php7.4-mysql \
    php7.4-odbc \
    php7.4-opcache \
    php7.4-readline \
    php7.4-xml \
    php7.4-dom \
    php7.4-xdebug \
    nginx \
    supervisor \
 && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
 && php composer-setup.php --install-dir=/usr/bin --filename=composer

#Editing PHP-FPM conf
RUN sed -i "s/;cgi.fix_pathinfo=.*/cgi.fix_pathinfo=0/g" /etc/php/7.4/fpm/php.ini

COPY ./git/hook/pre-commit .git/hooks/pre-commit
COPY ./nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./supervisor/supervisor-services.conf /etc/supervisor/conf.d/services.conf
COPY ./supervisor/supervisor-config.conf /etc/supervisor/supervisord.conf
COPY ./nginx/app.conf /etc/nginx/conf.d/app.conf

WORKDIR /var/www/app
WORKDIR /

ENTRYPOINT ["/usr/bin/supervisord","-n","-c","/etc/supervisor/supervisord.conf"]
