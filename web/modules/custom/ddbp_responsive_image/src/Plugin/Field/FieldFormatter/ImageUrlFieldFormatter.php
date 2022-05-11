<?php

namespace Drupal\ddbp_responsive_image\Plugin\Field\FieldFormatter;

use Drupal\file\Entity\File;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Plugin implementation of a media formatter, which outputs multiple
 * source URLs for use inside responsive <picture> tag.
 *
 * @FieldFormatter(
 *   id = "image_responsive_meta",
 *   label = @Translation("Responsive Image"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class ImageUrlFieldFormatter extends EntityReferenceFormatterBase {
  /**
   * {@inheritdoc}
   *
   * Currently, we need to have this formatter available only for all
   * entity reference fields of type image (media bundle: image)
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
    $fieldSettings = $field_definition->getSettings();

    if (isset($fieldSettings['handler_settings']['target_bundles']['image']) &&
      $field_definition->getFieldStorageDefinition()->getSetting('target_type') === 'media') {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings']
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return array(
        'image_style_0px' => 'default',
        'image_style_421px' => 'default',
        'image_style_701px' => 'default',
        'image_style_1181px' => 'default',
        'image_style_1481px' => 'default',
      ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];
    $image_styles = $this->getImageStyles();

    $image_style_0px = $this->getSetting('image_style_0px');
    $image_style_421px = $this->getSetting('image_style_421px');
    $image_style_701px = $this->getSetting('image_style_701px');
    $image_style_1181px = $this->getSetting('image_style_1181px');
    $image_style_1481px = $this->getSetting('image_style_1481px');

    $summary[] = $this->t('Image style for 0px - 420px: @style', [
      '@style' => $image_styles[$image_style_0px],
    ]);
    $summary[] = $this->t('Image style for 421px - 700px: @style', [
      '@style' => $image_styles[$image_style_421px],
    ]);
    $summary[] = $this->t('Image style for 701px - 1180px: @style', [
      '@style' => $image_styles[$image_style_701px],
    ]);
    $summary[] = $this->t('Image style for 1181px - 1480px: @style', [
      '@style' => $image_styles[$image_style_1181px],
    ]);
    $summary[] = $this->t('Image style for > 1481px: @style', [
      '@style' => $image_styles[$image_style_1481px],
    ]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements['image_style_0px'] = array(
      '#type' => 'select',
      '#options' => $this->getImageStyles(),
      '#title' => $this->t('Image Style (width: 0px - 420px)'),
      '#default_value' => $this->getSetting('image_style_0px'),
      '#required' => TRUE,
    );

    $elements['image_style_421px'] = array(
      '#type' => 'select',
      '#options' => $this->getImageStyles(),
      '#title' => $this->t('Image Style (width: 421px - 700px)'),
      '#default_value' => $this->getSetting('image_style_421px'),
      '#required' => TRUE,
    );

    $elements['image_style_701px'] = array(
      '#type' => 'select',
      '#options' => $this->getImageStyles(),
      '#title' => $this->t('Image Style (width: 701px - 1180px)'),
      '#default_value' => $this->getSetting('image_style_701px'),
      '#required' => TRUE,
    );

    $elements['image_style_1181px'] = array(
      '#type' => 'select',
      '#options' => $this->getImageStyles(),
      '#title' => $this->t('Image Style (width: 1181px - 1480px)'),
      '#default_value' => $this->getSetting('image_style_1181px'),
      '#required' => TRUE,
    );

    $elements['image_style_1481px'] = array(
      '#type' => 'select',
      '#options' => $this->getImageStyles(),
      '#title' => $this->t('Image Style (width: >1481px)'),
      '#default_value' => $this->getSetting('image_style_1481px'),
      '#required' => TRUE,
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $styles = [];

    $view_mode = $this->getSetting('image_style_0px');
    $styles[] = \Drupal::entityTypeManager()->getStorage('image_style')->load($view_mode);

    $view_mode = $this->getSetting('image_style_421px');
    $styles[] = \Drupal::entityTypeManager()->getStorage('image_style')->load($view_mode);

    $view_mode = $this->getSetting('image_style_701px');
    $styles[] = \Drupal::entityTypeManager()->getStorage('image_style')->load($view_mode);

    $view_mode = $this->getSetting('image_style_1181px');
    $styles[] = \Drupal::entityTypeManager()->getStorage('image_style')->load($view_mode);

    $view_mode = $this->getSetting('image_style_1481px');
    $styles[] = \Drupal::entityTypeManager()->getStorage('image_style')->load($view_mode);

    $medias = $this->getEntitiesToView($items, $langcode);

    $image_breakpoints = array_keys($this->defaultSettings());

    foreach ($medias as $delta => $media) {
      if ($media->bundle() == 'image') {
        $image_data = $media->get('field_media_image')->first()->getValue();
        $image_alt = $media->get('field_media_image')->first()->get('alt')->getString();
        $image_title = $media->get('name')->getString();
        $image_desc = $media->get('field_description')->value;
        // copyright info
        $image_copyright = empty($media->get('field_copyright')->value)
          ? ''
          : $media->get('field_copyright')->value;
        $image_copyright_link = $media->get('field_link')->first()
          ? $media->get('field_link')->first()->getUrl()->toString()
          : '';

        $image_url = NULL;
        $id = $image_data['target_id'];

        if (is_numeric($id)) {
          if ($file = File::load($id)) {
            $file_uri = $file->getFileUri();

            if ($styles == NULL && $file_uri) {
              $elements[$delta] = [
                '#markup' => \Drupal::service('file_url_generator')->generateAbsoluteString($file_uri)
              ];
            } else {
              $image = \Drupal::service('image.factory')->get($file_uri);
              $image_src = $image->getSource();
              $image_original = \Drupal::service('file_url_generator')->generateAbsoluteString($image_src);

              $elements[$delta] = [
                'images' => [],
                '#cache' => [
                  'max-age' => 0,
                  'contexts' => [],
                  'tags' => $media->getCacheTags(),
                ],
              ];

              $cnt = 0;

              $image_default_original = [];

              foreach ($styles as $style) {
                $image_bp = ltrim($image_breakpoints[$cnt], 'image_style_');

                if ($style === NULL) {
                  $elements[$delta]['images'][] = [
                    'src' => [
                      '#type'=>'markup',
                      '#markup' => $image_original,
                    ],
                    'alt' => [
                      '#type'=>'markup',
                      '#markup' => $image_alt,
                    ],
                    'title' => [
                      '#type'=>'markup',
                      '#markup' => $image_title,
                    ],
                    'description' => [
                      '#type'=>'markup',
                      '#markup' => $image_desc,
                    ],
                  ];
                } else {
                  $image_uri = $style->buildUri($image_src);

                  // Generate image style file if it doesn't exist
                  if (!file_exists($image_uri)) {
                    $style->createDerivative($image_src, $image_uri);
                  }

                  $image_url = $style->buildUrl($image_src);
                  $image_url = preg_replace('/(\?(.*)?itok=(.*))$/', '', $image_url, 1);

                  $image_copy_style = \Drupal::entityTypeManager()->getStorage('image_style')->load('scale_small_xs');
                  $image_copy_url = $image_copy_style->buildUrl($image_src);

                  if (end($styles)) {
                    $image_default_original = [
                      'src' => [
                        '#type'=>'markup',
                        '#markup' => $image_url,
                      ],
                      'alt' => [
                        '#type'=>'markup',
                        '#markup' => $image_alt,
                        ],
                      'title' => [
                        '#type'=>'markup',
                        '#markup' => $image_title,
                        ],
                      'description' => [
                        '#type'=>'markup',
                        '#markup' => $image_desc,
                      ],
                      'copyright' => [
                        'image' => $image_copy_url,
                        'title' => $image_title,
                        'text' => $image_copyright,
                        'link' => $image_copyright_link,
                      ],
                    ];
                  }

                  $elements[$delta]['images'][] = [
                    'src' => [
                      '#type' => 'markup',
                      '#markup' => $image_url,
                    ],
                    'media' => [
                      '#type' => 'markup',
                      '#markup' => '(min-width: ' . $image_bp . ')',
                    ],
                  ];
                }

                $cnt++;
              }

              $elements[$delta]['images'] = array_reverse($elements[$delta]['images'], true);

              // default original image - XL
              $elements[$delta]['images'][] = $image_default_original;
            }
          }
        }
      } else {
        \Drupal::messenger()->addMessage(t('The Media Image URL is planned to be used only on media references to image fields.'), 'warning');
      }

      // getting a single default image, if any
      $elements[$delta]['images'] = array_unique($elements[$delta]['images'], SORT_REGULAR);
    }

    return $elements;
  }

  /**
   * Function for collecting all image styles that are available for use and
   * putting them in an array.
   */
  public function getImageStyles(): array {
    $styles = ImageStyle::loadMultiple();
    $result = ['default' => 'Default'];

    foreach ($styles as $style) {
      $result[$style->id()] = $style->label();
    }

    return $result;
  }

}
