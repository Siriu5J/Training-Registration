FROM wordpress:latest
LABEL authors="Samuel Jiang"

# Install dependencies for composer
RUN apt-get update && apt-get install -y curl unzip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
