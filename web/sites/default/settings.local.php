<?php
$databases['default']['default'] = array (
  'database' => getenv('MYSQL_DATABASE'),
  'username' => getenv('MYSQL_USER'),
  'password' => getenv('MYSQL_PASSWORD'),
  'prefix' => '',
  'host' => getenv('MYSQL_HOSTNAME'),
  'port' => getenv('MYSQL_PORT'),
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);
$settings['hash_salt'] = getenv('HASH_SALT');
$settings['update_free_access'] = (strtolower(getenv('UPDATE_FREE_ACCESS')) === 'true');
$settings['file_public_path'] = getenv('FILE_PUBLIC_PATH');
$settings['file_private_path'] = getenv('FILE_PRIVATE_PATH');
$settings['file_temp_path'] = getenv('TMP');
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';
$settings['trusted_host_patterns'] = preg_split('/,[\s]*/', getenv('TRUSTED_HOST_PATTERNS'));
$settings['entity_update_batch_size'] = 50;
$settings['config_sync_directory'] = '../config/sync';
if ((getenv('USE_REDIS') ?: 'no') == 'yes') {
  $settings['redis.connection']['interface'] = 'PhpRedis';
  $settings['redis.connection']['host'] = '127.0.0.1';
  $settings['cache']['default'] = 'cache.backend.redis';
}
