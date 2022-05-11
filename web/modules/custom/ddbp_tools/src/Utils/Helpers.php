<?php

namespace Drupal\ms_toolkit\Utils;

use Drupal\Core\Language\LanguageInterface;

/**
 * A service with useful functions to share across custom modules.
 */
class Helpers {
  /**
   * 
   * Function for building image styles.
   *
   * @param $entity
   * @param $image_style
   * 
   * @return array
   */
  // static function getStyledImage($entity, $image_style): array {
  //   $images = [];
  //   $image_styles = [
  //     $image_style . '_xl',
  //     $image_style . '_xl',
  //     $image_style . '_m',
  //     $image_style . '_m',
  //     $image_style . '_xs',
  //   ];
  //   $media = [
  //     '(min-width: 1481px)',
  //     '(min-width: 1181px)',
  //     '(min-width: 701px)',
  //     '(min-width: 421px)',
  //     '(min-width: 0px)',
  //   ];
  //   $image_field = '';

  //   // get the image field
  //   $entity->hasField('field_image') && $image_field = 'field_image';
  //   $entity->hasField('field_image_media') && $image_field = 'field_image_media';
  //   $entity->hasField('field_title_image') && $image_field = 'field_title_image';
  
  //   if (!$entity->get($image_field)->isEmpty() && $entity->get($image_field)->entity) {
  //     $image = $entity->get($image_field)->entity->get('field_media_image')->entity->getFileUri();
  //     $image_alt = $entity->get($image_field)->entity->get('field_media_image')->first()->get('alt')->getString();
  
  //     foreach ($image_styles as $idx => $image_style) {
  //       $style = \Drupal::entityTypeManager()->getStorage('image_style')->load($image_style);
  //       $destination = $style->buildUri($image);
  //       if (!file_exists($destination)) {
  //         $style->createDerivative($image, $destination);
  //       }
  
  //       $images[] = [
  //         'src' => $style->buildUrl($image),
  //         'media' => $media[$idx],
  //       ];
  //     }
  
  //     // get default image
  //     $default_style = \Drupal::entityTypeManager()->getStorage('image_style')->load($image_styles[0]);
  //     $default_destination = $default_style->buildUri($image);
  //     if (!file_exists($default_destination)) {
  //       $default_style->createDerivative($image, $default_destination);
  //     }
  
  //     // build images array
  //     $images[] = [
  //       'src' => $default_style->buildUrl($image),
  //       'alt' => $image_alt,
  //     ];
  
  //   }
  
  //   return $images;
  // }

  /**
   * Returns the current lang code.
   * Set $prefix to true if you want a langcode for building URLs.
   *
   * @param boolean $prefix
   * @param boolean $trailing_slash
   *
   * @return string
   */
  static function getCurrentLanguage($prefix = false, $trailing_slash = true): string {
    $language = \Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
    $prefixes = \Drupal::config('language.negotiation')->get('url.prefixes');
    $langcode = '';

    if (!$prefix) {
      return $language;
    }
    else {
      if ($trailing_slash) $langcode .= '/';
      return $langcode;
    }
  }

}
