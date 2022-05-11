<?php

namespace Drupal\ddbp_tools\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Displays the three most recent nodes of type Aggregator portrait.
 *
 * @Block(
 *   id = "aggregators_block",
 *   admin_label = @Translation("Aggregator portrait teasers"),
 *   category = @Translation("Custom"),
 * )
 */

class AggregatorsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data = [];

    $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
    $nids = $query->condition('type', 'aggregator_portrait')
                  ->condition('status', 1)
                  ->pager(3)
                  ->execute();
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    foreach ($nodes as $node) {
      $data[] = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'teaser');
    }

    return [
      '#theme' => 'aggregators_block',
      'aggregators' => $data,
    ];
  }

  /**
  * {@inheritdoc}
  */
  public function getCacheMaxAge() {
    return 0;
  }
}
