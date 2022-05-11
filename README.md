# Deutsche Digitale Bibliothek Pro

### Local setup for DDBpro Drupal 9

1. run `composer install`
2. create **settings.local.php** file in web/sites/default and add your database connection; don't forget to add your local development hostname to 'trusted_host_patterns'
3. import database and sync files from STAG (or unzip files archive if you have one) in web/sites/default/files
4. perform drush 'flow' in case your database state is older than the codebase (yml config):
    * `drush updb`
    * `drush cim`
    * `drush cr`
    
5. build FE: Node >= 16 is **required** ⚠️
    * run `yarn` to install all FE dependencies
    * run `yarn build-dev` to build the FE for development
    * run `yarn build-watch` to watch for CSS/JS changes (during development)


### Setup with lando
1. [Lando](https://docs.lando.dev/) needs to be installed on the machine
2. copy *example-[x86-64/aarch64].lando.yml* (2 versions for different architectures available) and name it *.lando.yml* in project root
3. copy lando-example.settings.local.php in web/sites/default and rename to settings.local.php
4. `lando start`
5. `lando composer install`
6. `lando db-import database.sql`
