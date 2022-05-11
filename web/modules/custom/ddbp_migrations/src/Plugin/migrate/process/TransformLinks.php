<?php

namespace Drupal\ddbp_migrations\Plugin\migrate\process;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Migrate plugin for transforming all <a> tags in XML glossary data.
 * It's always best to use XML parser for such tasks (instead of regex).
 *
 * @MigrateProcessPlugin(
 *   id = "transform_links",
 * )
 */
class TransformLinks extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $html = Html::load($value);
    $xpath = new \DOMXPath($html);
    // Get all link nodes.
    foreach ($xpath->query('//a') as $link) {
      $href = $link->getAttribute('href');
      // If link starts with glossary URL, replace it with internal link.
      if (str_starts_with($href, 'http://ddb.vocnet.org/glossar/')) {
        // Find the glossary_term node associated with this URI.
        $node = $this->entityTypeManager->getStorage('node')->loadByProperties(['field_uri' => $href]);
        if (count($node)) {
          $node = reset($node);
          $link->setAttribute('href', $node->toUrl()->toString());
          // Use trimmed body as data-tooltip value.
          $link->setAttribute('data-tooltip', text_summary(trim(strip_tags($node->body->value)), NULL, 200));
        }
        else {
          // If no node was found, simply unlink.
          $link->parentNode->replaceChild(new \DOMText($link->nodeValue), $link);
        }
      }
      else {
        // All other links will be external so set target to _blank.
        $link->setAttribute('target', '_blank');
      }
    }
    return Html::serialize($html);
  }

}
