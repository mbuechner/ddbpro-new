#!/bin/sh
UPDATEDB_ON_STARTUP="${UPDATEDB_ON_STARTUP:-no}";
CACHEREBUILD_ON_STARTUP="${CACHEREBUILD_ON_STARTUP:-no}";
REDIS_MAXMEMORY="${REDIS_MAXMEMORY:-1gb}";

if [ "$UPDATEDB_ON_STARTUP" = "yes" ]; then
  echo "Start Drupal Update DB...";
  /var/www/html/vendor/bin/drush -y --root /var/www/html/web updatedb;
fi;

if [ "$CACHEREBUILD_ON_STARTUP" = "yes" ]; then
  echo "Start Drupal Cache Rebuild...";
  /var/www/html/vendor/bin/drush -y --root /var/www/html/web cache-rebuild;
fi;

/usr/bin/redis-cli config set maxmemory "${REDIS_MAXMEMORY}";
/usr/bin/redis-cli config set maxmemory-policy volatile-lru;
