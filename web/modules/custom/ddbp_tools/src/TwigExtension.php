<?php

namespace Drupal\ddbp_tools;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Twig extension with some useful functions and filters.
 *
 * Dependencies are not injected for performance reason.
 */
class TwigExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFunctions(): array {
    return [
      new TwigFunction('get_faq', [$this, 'getFaq']),
      new TwigFunction('get_nodes', [$this, 'getNodes']),
      new TwigFunction('get_latest_events', [$this, 'getLatestEvents']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return array(
      new TwigFilter('format_bytes', array($this, 'formatBytes')),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'ddbp_tools_twig_extension';
  }

  /**
   * Return CT FAQ content based on taxonomy id.
   * 
   * @param string $tid
   * 
   * @return array
   */
  public function getFaq($tid): array {
    if (!isset($tid)) return [];
    $data = [];

    $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
    $nids = $query->condition('type', 'faq')
                  ->condition('status', 1)
                  ->condition('field_faq_category', $tid, 'IN')
                  ->sort('created' , 'DESC')
                  ->execute();
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    foreach ($nodes as $node) {
      $content = $node->get('field_answer')->value;

      $data['items'][] = [
        'title' => $node->getTitle(),
        'content' => [
          '#type' => 'processed_text',
          '#text' => $content,
          '#format' => 'basic_html',
        ],
      ];
    }

    // get term name for header
    $term_name = Term::load($tid)->get('name')->value;
    $data['term'] = $term_name;

    return $data;
  }

  /**
   * Get nodes of CTs.
   * 
   * @param string $type
   * @param string $id
   * 
   * @return array
   */
  public function getNodes($type, $id = null): array {
    $data = [];

    $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
    $query->condition('type', $type)->condition('status', 1);

    if ($id) {
      $query->condition('nid', $id, 'NOT IN');
    }

    // sorting
    switch ($type) {
      case 'aggregator_portrait':
        $query->sort('created' , 'DESC');
        break;
      case 'department':
        $query->sort('field_weight', 'ASC');
        break;
      
      default:
        break;
    }

    $nids = $query->execute();

    if ('aggregator_portrait' == $type) {
      // add current to the first position
      array_unshift($nids, $id);
    }

    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    foreach ($nodes as $node) {
      $data[] = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'teaser_mini');
    }

    return $data;
  }

  /**
   * Get latest upcoming N CT Events.
   * 
   * @param int $count
   * 
   * @return array
   */
  public function getLatestEvents($count = 3): array {
    $data = [];
    $now = new DrupalDateTime('now');
    $now->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));

    $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
    $nids = $query->condition('type', 'event')
                  ->condition('status', 1)
                  ->condition('field_date', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=')
                  ->sort('field_date' , 'ASC')
                  ->pager($count)
                  ->execute();
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    foreach ($nodes as $node) {
      $data[] = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'teaser');
    }

    return $data;
  }

  /**
   * Transform file size to readable number.
   * 
   * @param $bytes
   * @param int $precision
   * 
   * @return string
   */
  public function formatBytes($bytes, $precision = 2): string {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0); 
    $value = ($bytes / pow(1024, floor(2))); // MB

    $result = round($value, $precision);

    return number_format($result, $precision, ',', '.') . ' ' . 'MB';
  }
}
