<?php

namespace Drupal\ddbp_migrations\Plugin\migrate\process;

use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;

/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "strip_tags"
 * )
 *
 * @code
 * field_text:
 *   plugin: strip_tags
 *   source: text
 * @endcode
 */
class StripTags extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   * @fixme: @Karol, why is '&amp;' being replaced by nothing instead of '&'?
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $value['value'] = str_replace(['&#13;', '&nbsp;', '&amp;'], ['', ' ', ''], strip_tags($value['value']));
    return $value;
  }

}
