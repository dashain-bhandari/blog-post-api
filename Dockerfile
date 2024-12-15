FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    pdo_mysql \
    gd \
    mbstring \
    xml \
    zip \
    opcache


# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html
COPY Seeder.php /var/www/seeder.php



# Copy the rest of your application files to the container
COPY . /app



# Enable Apache modules
RUN a2enmod rewrite

# Set correct permissions for files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80 for Apache
EXPOSE 80

# Run seeder and PHPUnit tests, then start Apache
CMD ["sh", "-c", "php /var/www/seeder.php  && apache2-foreground"]