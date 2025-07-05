FROM php:8.4.3-fpm-alpine

# Set working directory
WORKDIR /app

# Install user management
RUN apk add --no-cache shadow

# Add user for laravel application
RUN groupadd -g 1000 www \
  && useradd -u 1000 -ms /bin/sh -g www www

# Install dependencies libraries
RUN apk add \
  libpq-dev \
  libzip-dev \
  icu-data-full \
  oniguruma-dev \
  libpng-dev \
  libjpeg-turbo-dev \
  freetype-dev \
  libxml2-dev \
  musl-locales \
  musl-locales-lang \
  jpegoptim \
  optipng \
  pngquant \
  gifsicle \
  git \
  vim \
  redis \
  autoconf \
  g++ \
  make \
  nginx

# Install extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd mbstring pdo pdo_mysql exif xml pcntl zip \
  && pecl install redis && docker-php-ext-enable redis

# Install composer 2.8.5
RUN curl -sS https://getcomposer.org/installer| php -- --install-dir=/usr/local/bin --filename=composer --version=2.8.5

# Edit PHP-DPM Runtime User
RUN sed -i 's/^user = .*/user = www;/' /usr/local/etc/php-fpm.d/www.conf \
  && sed -i 's/^group = .*/group = www;/' /usr/local/etc/php-fpm.d/www.conf \
  && sed -i 's/^listen.owner = .*/listen.owner = www;/' /usr/local/etc/php-fpm.d/www.conf \
  && sed -i 's/^listen.group = .*/listen.group = www;/' /usr/local/etc/php-fpm.d/www.conf \
  && sed -i 's/^listen.mode = .*/listen.mode = 0666;/' /usr/local/etc/php-fpm.d/www.conf \
  && sed -i 's/^listen.acl_users = .*/listen.acl_users = www;/' /usr/local/etc/php-fpm.d/www.conf \
  && sed -i 's/^listen.acl_groups = .*/listen.acl_groups = www;/' /usr/local/etc/php-fpm.d/www.conf

# Clean Cache
RUN apk cache clean --force && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Run Entrypoint
COPY --chown=1000:1000 ./.infra/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/bin/sh", "/entrypoint.sh"]

# Run Command
CMD [ "sh", "-c", "php-fpm" ]
