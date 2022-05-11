<?php

namespace Drupal\ddbp_migrations\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate_tools\MigrateExecutable;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for executing migrations programmatically.
 */
class MigrationsController extends ControllerBase {

  /**
   * The migration manager.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface
   */
  protected MigrationPluginManagerInterface $migrationManager;

  /**
   * Constructs a FieldFile plugin instance.
   *
   * @param \Drupal\migrate\Plugin\MigrationPluginManagerInterface $migration_manager
   *   Migration manager service.
   */
  public function __construct(MigrationPluginManagerInterface $migration_manager) {
    $this->migrationManager = $migration_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.migration')
    );
  }

  /**
   * Executes ddbp_glossary migration (triggered by a link in the admin menu).
   */
  public function importGlossary() {
    $this->messenger()->addMessage($this->t('Imported all glossary terms.'));
    $migration = $this->migrationManager->createInstance('ddbp_glossary');
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();
    // Redirect to home.
    return new RedirectResponse('/admin/content');
  }

}
