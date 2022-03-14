![PHP Composer](https://github.com/mbuechner/ddbpro-new/workflows/PHP%20Composer/badge.svg) [![Docker](https://github.com/mbuechner/ddbpro-new/actions/workflows/docker-publish.yml/badge.svg)](https://github.com/mbuechner/ddbpro-new/actions/workflows/docker-publish.yml)
# DDBpro
DDBpro ist das Portal für die professionelle Nutzung der Deutschen Digitalen Bibliothek.

## Docker

This Drupal project is available as non-root Docker container from GitHub. See https://github.com/mbuechner?tab=packages&repo_name=ddbpro-new

To execute the pre-compiled Docker container, run the following command with the variables set for your environment.
```shell
docker run -d -p 8080:8080 -P \
  --env "MYSQL_HOSTNAME=sql.example.com" \
  --env "MYSQL_DATABASE=myddbprodatabase" \
  --env "MYSQL_USER=myddbprouser" \
  --env "MYSQL_PASSWORD=myddbpropassword" \
  --env "MYSQL_PORT=3306" \
  --env "HASH_SALT=myverysecretddbprohashsalt" \
  --env "UPDATE_FREE_ACCESS=FALSE" \
  --env "FILE_PUBLIC_PATH=sites/default/files" \
  --env "TRUSTED_HOST_PATTERNS=\"^localhost\$, ^127.0.0.1\$\"" \
  ghcr.io/mbuechner/ddbpro-new/ddbpro:latest
```

There are four services running within the Docker container (see also [/config/supervisord/supervisord.conf](/config/supervisord/supervisord.conf)):

1. [nginx](/config/nginx/) on port 8080 (HTTP with gzip and br) and port 4430 (HTTP2 with self-signed certificate, gzip and br)
2. [PHP 8.0](/config/php/) (php-fpm)
3. [Cron job](/config/cron) triggered every minute by supercronic
4. Redis memory cache

### Environment variables

Please also see [web/sites/default/settings.php](web/sites/default/settings.php) for additional information.

| Environment variable    | Description                                                                                    | Example                         |
|-------------------------|------------------------------------------------------------------------------------------------|---------------------------------|
| MYSQL_HOSTNAME          | Database connection. Server host name.                                                         | `ddbpro.example.com`            |
| MYSQL_DATABASE          | Database connection. Name of the database.                                                     | `myddbprodatabase`              |
| MYSQL_USER              | Database connection. Login name for database.                                                  | `myddbprouser`                  |
| MYSQL_PASSWORD          | Database connection. Password for database.                                                    | `myddbpropassword`              |
| MYSQL_PORT              | Database connection. Server Connection Port.                                                   | `3306`                          |
| HASH_SALT               | Salt for Drupal's one-time login links, cancel links, form tokens, etc.                        | `myverysecretddbprohashsalt`    |
| UPDATE_FREE_ACCESS      | Access control for update.php script.                                                          | `FALSE`                         |
| FILE_PUBLIC_PATH        | Public file path.                                                                              | `sites/default/files`           |
| FILE_PRIVATE_PATH       | Private file path.                                                                             | `sites/default/files/private`   |
| TRUSTED_HOST_PATTERNS   | Trusted host configuration.                                                                    | `"^localhost\$, ^127.0.0.1\$\"` |
| HTPASSWD_USER           | If `HTPASSWD_USER` and `HTPASSWD_PWD` are set, ...                                             | `user`                          |
| HTPASSWD_PWD            | the user will get a Basic HTTP Auth request when accessing the website.                        | `mypassword`                    |
| HTPASSWD_GREETING       | Customized HTTP Auth greeting                                                                  | `Hello, welcome!`               |
| UPDATEDB_ON_STARTUP     | If set to `yes`, `drush updatedb` will be executed once on container start. Default: `no`      | `yes`or `no`                    |
| CACHEREBUILD_ON_STARTUP | If set to `yes`, `drush cache-rebuild` will be executed once on container start. Default: `no` | `yes`or `no`                    |
| USE_REDIS               | Use Redis as memory cache. Default: `no`                                                       | `yes`or `no`                    |
| REDIS_MAXMEMORY         | Set a memory usage limit for Redis to the specified amount of bytes. Default: `1gb`            | `1gb` or `100mb`                |

### Build and start Docker container locally

Run in the folder with `Dockerfile`:
```shell
docker build -t ddbpro .
```

And start Docker container with:
```shell
docker run -d -p 8080:8080 -P \
  --env "MYSQL_HOSTNAME=ddbpro.example.com" \
  --env "MYSQL_DATABASE=myddbprodatabase" \
  --env "MYSQL_USER=myddbprouser" \
  --env "MYSQL_PASSWORD=myddbpropassword" \
  --env "MYSQL_PORT=3306" \
  --env "HASH_SALT=myverysecretddbprohashsalt" \
  --env "UPDATE_FREE_ACCESS=FALSE" \
  --env "FILE_PUBLIC_PATH=sites/default/files" \
  --env "TRUSTED_HOST_PATTERNS=\"^localhost\$, ^127.0.0.1\$\"" \
  ddbpro
```
