<?php

## database settings
$databases['default']['default'] = [
    'database' => 'drupal9',
    'username' => 'drupal9',
    'password' => 'drupal9',
    'prefix' => '',
    'host' => 'database',
    'port' => '3306',
    'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
    'driver' => 'mysql',
];

## All messages, with backtrace information
$config['system.logging']['error_level'] = 'verbose';

## Enable local development services settings & disable caching
## ONLY for DEV purposes!! Remove the following lines for STAG + PROD instances!!
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['cache']['bins']['render'] = 'cache.backend.null';

$settings['trusted_host_patterns'] = array(
    'ddb-pro.lndo.site',
);
