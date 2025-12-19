FROM php:8.4-fpm-trixie

COPY --from=composer:2.8.1 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && apt-get install -y --allow-unauthenticated --no-install-recommends curl \
    apt-utils \
    aptitude \
    git \
    iputils-ping \
    libzip-dev \
    zip \
    vim \
    unzip \
    libicu-dev \
    libpq-dev \
    libgmp-dev \
    libffi-dev \
    libssl-dev \
    cron \
    libpng-dev \
    libfreetype6-dev \
    librabbitmq-dev \
    gcc \
    g++ \
    make \
    memcached

RUN docker-php-ext-configure intl && docker-php-ext-install intl
RUN docker-php-ext-install \
    bcmath \
    gd \
    gmp \
    ffi \
    opcache \
    pcntl \
    pdo \
    pdo_mysql \
    sockets \
    zip \
    && pecl install amqp \
    && docker-php-ext-enable amqp

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN apt-get autoremove --purge -y && apt-get autoclean -y && apt-get clean -y

COPY --link --from=ghcr.io/symfony-cli/symfony-cli:latest /usr/local/bin/symfony /usr/local/bin/symfony
