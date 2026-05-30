FROM wordpress:7.0
LABEL authors="Samuel Jiang"

# Install dependencies for composer and Node.js
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Gemini CLI
RUN npm install -g @google/gemini-cli

# Set working directory for the plugin
WORKDIR /var/www/html/wp-content/plugins/training-registration

# Ensure composer dependencies are installed (including dev for testing)
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY composer.json composer.lock ./
RUN composer install --no-interaction --dev

# Reset WORKDIR to WordPress default
WORKDIR /var/www/html