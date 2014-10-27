<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\GeofieldProximityManager.
 */

namespace Drupal\geofield\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Defines the plugin manager Geofield proximity.
 */
class GeofieldProximityManager extends DefaultPluginManager {

  /**
   * Constructs a new \Drupal\geofield\Plugin\GeofieldProximityManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/GeofieldProximity', $namespaces, $module_handler, 'Drupal\geofield\Plugin\GeofieldProximityPluginInterface', 'Drupal\geofield\Annotation\GeofieldProximity');

    $this->alterInfo('geofield_proximity');
    $this->setCacheBackend($cache_backend, 'geofield_proximity_plugins');
  }
}
