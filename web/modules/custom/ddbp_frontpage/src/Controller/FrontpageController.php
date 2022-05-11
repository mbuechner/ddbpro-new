<?php

namespace Drupal\ddbp_frontpage\Controller;

use Drupal\config_pages\Entity\ConfigPages;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides a controller to render the Front page config_page.
 * This way there is no need to create a fully configured CT for singleton pages
 * and then explain to the client that they can only have 1 instance of the CT.
 */
class FrontpageController extends ControllerBase {

  /**
   * Renders config page entity.
   *
   * @return array
   */
  public function render(): array {
    $frontpage = ConfigPages::config('front_page');

    if (!$frontpage instanceof ConfigPages) {
      $frontpage = ConfigPages::create(['type' => 'front_page']);
    }

    return $this->entityTypeManager()
      ->getViewBuilder($frontpage->getEntityTypeId())
      ->view($frontpage, 'full');
  }

  /**
   * Generates page title.
   *
   * @return string
   */
  public function generateTitle(): string {
    $page = ConfigPages::config('front_page');
    if ($page) {
      if (isset($page->field_title->value)) {
        return $page->field_title->value;
      }
    }
    return $this->t('Home');
  }

}
