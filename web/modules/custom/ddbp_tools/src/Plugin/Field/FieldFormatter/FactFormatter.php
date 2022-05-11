<?php

namespace Drupal\ddbp_tools\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * @FieldFormatter(
 *   id = "fact_item_default",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "fact_item"
 *   }
 * )
 */
class FactFormatter extends FormatterBase {
  /**
   * Builds a renderable array for a field value.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field values to be rendered.
   * @param string $langcode
   *   The language that should be used to render the field.
   *
   * @return array
   *   A renderable array for $items, as an array of child elements keyed by
   *   consecutive numeric indexes starting from 0.
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $output = [];

    foreach ($items as $delta => $item) {
      $build = [
        'label' => [
          '#markup' => $item->fact_label,
        ],
        'value' => [
          '#type' => 'processed_text',
          '#text' => ($item->fact_value) ? $item->fact_value : '',
          '#format' => 'basic_html',
        ],
      ];

      $output[$delta] = $build;
    }

    return $output;

  }
}
