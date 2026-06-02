FROM wordpress:7.0
LABEL authors="Samuel Jiang"

RUN apt-get update && apt-get install -y \
    curl \
    zip \
    vim \
    unzip \
    nodejs \
    npm \
    subversion \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN npm install -g @google/gemini-cli

WORKDIR /var/www/html/wp-content/plugins/training-registration

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html