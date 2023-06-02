FROM composer:2 as composer

FROM php:7.4-fpm-alpine

WORKDIR /srv

VOLUME /data
VOLUME /srv/public

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN set -eux; \
        echo "► Building php extensions"; \
# Ensure that the php source is extracted & install the build dependencies
        docker-php-source extract; \
        apk add --no-cache icu-libs libidn; \
        apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS icu-dev libidn-dev; \
        \
        docker-php-ext-install intl \
        && docker-php-ext-enable intl; \
# Install & Enable extensions: opcache
        { \
            extDir="$(php -d 'display_errors=stderr' -r 'echo ini_get("extension_dir");')"; \
            \
# Opcache is sometimes built with the source
            if [ -f "${extDir}/opcache.so" ]; then \
                docker-php-ext-enable opcache; \
            else \
                docker-php-ext-install opcache; \
            fi; \
        }; \
        \
# Install extensions: APCu (for user caching)
        pecl install apcu \
        && docker-php-ext-enable apcu; \
	rm -rf /tmp/pear ~/.pearrc; \
        \
# Remove the build dependencies & delete the source
        apk del --no-cache --no-network .phpize-deps; \
        docker-php-source delete

COPY --chown=www-data:www-data composer.json composer.lock /srv/

RUN set -eu; \
    echo "► Setting default timezone..."; \
    printf "[www]\nphp_admin_value[date.timezone] = America/Toronto" | tee /usr/local/etc/php-fpm.d/timezone.conf > /dev/null; \
    echo "► Running Composer Install..."; \
    composer install --optimize-autoloader --apcu-autoloader --no-dev -n --no-progress --no-scripts; \
    echo "► Checking Platform Requirements"; \
    composer check-platform-reqs; \
    chown -R www-data:www-data /srv

COPY --chown=www-data:www-data . /srv/

USER www-data:www-data
