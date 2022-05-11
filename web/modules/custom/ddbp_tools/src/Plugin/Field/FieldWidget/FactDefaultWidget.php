<?php

namespace Drupal\ddbp_tools\Plugin\Field\FieldWidget;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *   id = "fact_item_default",
 *   label = @Translation("Default"),
 *   field_types = {
 *     "fact_item"
 *   }
 * )
 */
class FactDefaultWidget extends WidgetBase implements WidgetInterface {
  /**
   * Returns the form for a single field widget.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   Array of default values for this field.
   * @param int $delta
   *   The order of this item in the array of sub-elements (0, 1, 2, etc.).
   * @param array $element
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form elements for a single widget for this field.
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item =& $items[$delta];
    $element += [
      '#type' => 'fieldset',
    ];

    $element['fact_label'] = [
      '#title' => t('Fact label'),
      '#type' => 'textfield',
      '#maxlength' => 250,
      '#default_value' => isset($item->fact_label) ? $item->fact_label : '',
    ];

    $element['fact_value'] = [
      '#title' => t('Fact value'),
      '#type' => 'text_format',
      '#format' => 'basic_html',
      '#default_value' => isset($item->fact_value) ? $item->fact_value : '',
    ];

    return $element;
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $value) {
      $values[$delta]['fact_value'] = $value['fact_value']['value'];
    }

    return $values;
  }

}
