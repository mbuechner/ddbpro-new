FROM composer:2 AS COMPOSER_CHAIN
COPY / /tmp/ddbpro
WORKDIR /tmp/ddbpro
RUN composer install --no-dev

# Add git tag version to PHP file
RUN { \
        echo -e "$(git describe --tags)"; \
    } >> /tmp/ddbpro/version; \
    rm -rf .git/;

FROM php:8.0-fpm-alpine
MAINTAINER Michael Büchner <m.buechner@dnb.de>

# Install packages
RUN apk --no-cache add \
    curl \
    nginx \
    nginx-mod-http-brotli \
    redis \
    supervisor; \
    apk add --no-cache -X http://dl-cdn.alpinelinux.org/alpine/edge/community \
    supercronic;

RUN set -eux; \
     \
     apk add --no-cache --virtual .build-deps \
          coreutils \
          freetype-dev \
          libjpeg-turbo-dev \
          libpng-dev \
          libzip-dev \
          pcre-dev \
          autoconf \
          g++ \
          make \
          git \
          # postgresql-dev is needed for https://bugs.alpinelinux.org/issues/3642
          postgresql-dev; \
     \
     docker-php-ext-configure gd \
          --with-freetype \
          --with-jpeg=/usr/include; \
     \
     docker-php-ext-install -j "$(nproc)" \
          gd \
          opcache \
          pdo_mysql \
          pdo_pgsql \
          zip; \
     pecl channel-update pecl.php.net; \
     pecl install oauth apcu redis; \
     docker-php-ext-enable apcu oauth redis; \
     \
     runDeps="$( \
          scanelf --needed --nobanner --format '%n#p' --recursive /usr/local \
               | tr ',' '\n' \
               | sort -u \
               | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
     )"; \
     apk add --no-network --virtual .drupal-phpexts-rundeps $runDeps; \
     apk del --no-network .build-deps \
          coreutils \
          freetype-dev \
          libjpeg-turbo-dev \
          libpng-dev \
          libzip-dev \
          pcre-dev \
          autoconf \
          g++ \
          make \
          git \
          postgresql-dev;

ENV RUN_USER nobody
ENV RUN_GROUP 0

# add PHP config
COPY --chown=${RUN_USER}:${RUN_GROUP} ./config/php/ /usr/local/etc/php/conf.d/

# add NGINX config
COPY --chown=${RUN_USER}:${RUN_GROUP} config/nginx/*.conf /etc/nginx/
COPY --chown=${RUN_USER}:${RUN_GROUP} config/nginx/mime.types /etc/nginx/mime.types
COPY --chown=${RUN_USER}:${RUN_GROUP} config/nginx/conf.d/ /etc/nginx/conf.d/ 
COPY --chown=${RUN_USER}:${RUN_GROUP} config/nginx/.authpasswd /etc/nginx/.authpasswd

# add cron jobs
COPY --chown=${RUN_USER}:${RUN_GROUP} config/cron/* /etc/crontabs/

# add supervisord config
COPY --chown=${RUN_USER}:${RUN_GROUP} config/supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Add application
WORKDIR /var/www/html
COPY --chown=${RUN_USER}:${RUN_GROUP} --from=COMPOSER_CHAIN /tmp/ddbpro/ .
ENV PATH=${PATH}:/var/www/html/vendor/bin

RUN \
    # Create symlink for php8
    ln -s /usr/bin/php8 /usr/bin/php; \
    # Use the default PHP production configuration
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
    # Move entrypoint script in place
    mv scripts/docker-php-entrypoint-drupal.sh /usr/local/bin/docker-php-entrypoint-drupal; \
    mv scripts/drupal-maintenance.sh /usr/local/bin/drupal-maintenance; \
    # Generate SSL certificates fpr HTTP2
    # Generating signing SSL private key
    openssl genrsa -des3 -passout pass:foobar -out /etc/ssl/mykey.pem 2048; \
    # Removing passphrase from private key
    cp /etc/ssl/mykey.pem /etc/ssl/mykey.pem.orig; \
    openssl rsa -passin pass:foobar -in /etc/ssl/mykey.pem.orig -out /etc/ssl/mykey.pem; \
    # Generating certificate signing request
    openssl req -new -key /etc/ssl/mykey.pem -out /etc/ssl/mycert.csr -subj "/C=DE/ST=DE/L=Frankfurt am Main/O=Deutsche Nationalbibliothek/OU=IT.DDB/CN=DDBpro"; \
    # Generating self-signed certificate
    openssl x509 -req -days 3650 -in /etc/ssl/mycert.csr -signkey /etc/ssl/mykey.pem -out /etc/ssl/mycert.pem; \
    # Make sure files/folders needed by the processes are accessable when they run under the nobody user
    mkdir /var/cache/nginx; \
    chgrp -R ${RUN_GROUP} /run /var/cache/nginx/ /var/lib/nginx/ /var/log/nginx/ /var/www/html/ /etc/ssl/mycert.pem /etc/ssl/mykey.pem /etc/nginx/.authpasswd; \
    chmod -R g=u /run/ /etc/nginx/conf.d/ /etc/nginx/*.conf /var/cache/nginx/ /var/lib/nginx/ /var/log/nginx/ /var/www/html/ /etc/ssl/mycert.pem /etc/ssl/mykey.pem /etc/nginx/.authpasswd; \
    chmod 751 /usr/local/bin/docker-php-entrypoint-drupal /usr/local/bin/drupal-maintenance /var/www/html/vendor/drush/drush/drush /var/www/html/web/sites/default; \
    chmod 440 /var/www/html/web/sites/default/settings.php; \
    # add permissions for suervisor & nginx user
    touch /run/supervisord.pid && chgrp -R ${RUN_GROUP} /run/supervisord.pid && chmod -R g=u /run/supervisord.pid; \
    touch /run/nginx/nginx.pid && chgrp -R ${RUN_GROUP} /run/nginx/nginx.pid && chmod -R g=u /run/nginx/nginx.pid;

# Switch to use a non-root user
USER ${RUN_USER}:${RUN_GROUP}

ENTRYPOINT ["docker-php-entrypoint-drupal"]

# Expose the ports for nginx
EXPOSE 8080 4430

# supervisord starts nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
