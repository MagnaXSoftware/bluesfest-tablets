FROM composer:2 as composer

FROM php:7.4-cli-bullseye

WORKDIR /srv/app

VOLUME /srv/data

RUN set -ex \
    && savedAptMark="$(apt-mark showmanual)" \
    && apt-get update \
    && apt-get install --no-install-recommends --no-install-suggests -y ca-certificates mercurial build-essential libssl-dev libpcre2-dev curl pkg-config \
    && mkdir -p /usr/lib/unit/modules /usr/lib/unit/debug-modules \
    && hg clone -u 1.30.0-1 https://hg.nginx.org/unit \
    && cd unit \
    && NCPU="$(getconf _NPROCESSORS_ONLN)" \
    && DEB_HOST_MULTIARCH="$(dpkg-architecture -q DEB_HOST_MULTIARCH)" \
    && CC_OPT="$(DEB_BUILD_MAINT_OPTIONS="hardening=+all,-pie" DEB_CFLAGS_MAINT_APPEND="-Wp,-D_FORTIFY_SOURCE=2 -fPIC" dpkg-buildflags --get CFLAGS)" \
    && LD_OPT="$(DEB_BUILD_MAINT_OPTIONS="hardening=+all,-pie" DEB_LDFLAGS_MAINT_APPEND="-Wl,--as-needed -pie" dpkg-buildflags --get LDFLAGS)" \
    && CONFIGURE_ARGS_MODULES="--prefix=/usr \
                --statedir=/var/lib/unit \
                --control=unix:/var/run/control.unit.sock \
                --pid=/var/run/unit.pid \
                --log=/var/log/unit.log \
                --tmpdir=/var/tmp \
                --user=unit \
                --group=unit \
                --openssl \
                --libdir=/usr/lib/$DEB_HOST_MULTIARCH" \
    && CONFIGURE_ARGS="$CONFIGURE_ARGS_MODULES \
                --njs" \
    && make -j $NCPU -C pkg/contrib .njs \
    && export PKG_CONFIG_PATH=$(pwd)/pkg/contrib/njs/build \
    && ./configure $CONFIGURE_ARGS --cc-opt="$CC_OPT" --ld-opt="$LD_OPT" --modulesdir=/usr/lib/unit/debug-modules --debug \
    && make -j $NCPU unitd \
    && install -pm755 build/sbin/unitd /usr/sbin/unitd-debug \
    && make clean \
    && ./configure $CONFIGURE_ARGS --cc-opt="$CC_OPT" --ld-opt="$LD_OPT" --modulesdir=/usr/lib/unit/modules \
    && make -j $NCPU unitd \
    && install -pm755 build/sbin/unitd /usr/sbin/unitd \
    && make clean \
    && ./configure $CONFIGURE_ARGS_MODULES --cc-opt="$CC_OPT" --modulesdir=/usr/lib/unit/debug-modules --debug \
    && ./configure php \
    && make -j $NCPU php-install \
    && make clean \
    && ./configure $CONFIGURE_ARGS_MODULES --cc-opt="$CC_OPT" --modulesdir=/usr/lib/unit/modules \
    && ./configure php \
    && make -j $NCPU php-install \
    && cd \
    && rm -rf unit \
    && for f in /usr/sbin/unitd /usr/lib/unit/modules/*.unit.so; do \
        ldd $f | awk '/=>/{print $(NF-1)}' | while read n; do dpkg-query -S $n; done | sed 's/^\([^:]\+\):.*$/\1/' | sort | uniq >> /requirements.apt; \
       done \
    && apt-mark showmanual | xargs apt-mark auto > /dev/null \
    && { [ -z "$savedAptMark" ] || apt-mark manual $savedAptMark; } \
    && ldconfig \
    && mkdir -p /var/lib/unit/ \
    && mkdir /docker-entrypoint.d/ \
    && groupadd --gid 999 unit \
    && useradd \
         --uid 999 \
         --gid unit \
         --no-create-home \
         --home /nonexistent \
         --comment "unit user" \
         --shell /bin/false \
         unit \
    && apt-get update \
    && apt-get --no-install-recommends --no-install-suggests -y install curl $(cat /requirements.apt) \
    && apt-get purge -y --auto-remove \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /requirements.apt \
    && ln -sf /dev/stdout /var/log/unit.log

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY docker/docker-entrypoint.sh /usr/local/bin/
COPY docker/nginx-unit-config.json /docker-entrypoint.d/

RUN set -eux; \
    chown -R www-data:www-data /srv; \
    usermod -a -G www-data unit; \
    echo "► Building php extensions"; \
  # Ensure that the php source is extracted & install the build dependencies
    docker-php-source extract; \
    apt-get update; \
    apt-get install --no-install-recommends --no-install-suggests -y libicu67 libidn2-0 unzip libicu-dev libidn2-dev; \
    \
    docker-php-ext-install intl; \
    docker-php-ext-enable intl ;\
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
    pecl install apcu; \
    docker-php-ext-enable apcu; \
    rm -rf /tmp/pear ~/.pearrc; \
    \
  # Remove the build dependencies & delete the source
    apt-get purge -y libicu-dev libidn2-dev; \
    apt-get purge -y --auto-remove; \
    rm -rf /var/lib/apt/lists/*; \
    docker-php-source delete

COPY --chown=www-data:www-data composer.json composer.lock /srv/app/
RUN set -eu; \
    echo "► Running Composer Install..."; \
    su -s /bin/sh -c "composer install --optimize-autoloader --apcu-autoloader --no-dev -n --no-progress --no-scripts" www-data; \
    echo "► Checking Platform Requirements"; \
    su -s /bin/sh -c "composer check-platform-reqs" www-data

COPY --chown=www-data:www-data . /srv/app

STOPSIGNAL SIGTERM

ENV DB_PATH "/srv/data/tablets.db"

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
EXPOSE 80
CMD ["unitd", "--no-daemon", "--control", "unix:/var/run/control.unit.sock"]

