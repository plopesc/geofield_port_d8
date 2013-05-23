<?php
namespace Drupal\geofield\Plugin\Type;

use Drupal\Component\Plugin\PluginManagerBase;
use Drupal\Core\Plugin\Discovery\AnnotatedClassDiscovery;
use Drupal\Core\Plugin\Discovery\CacheDecorator;
use Drupal\Component\Plugin\Factory\DefaultFactory;

/**
 * Defines the plugin manager Geofield backends.
 */
class GeofieldBackendPluginManager extends PluginManagerBase {

  /**
   * Constructs a new Geofield Backend plugin manager object.
  */
  public function __construct(\Traversable $namespaces) {
    $this->discovery = new AnnotatedClassDiscovery('GeofieldBackend', $namespaces);
    //$this->discovery = new CacheDecorator($this->discovery, 'block_plugins:' . language(LANGUAGE_TYPE_INTERFACE)->langcode, 'block', CacheBackendInterface::CACHE_PERMANENT, array('block'));
    $this->factory = new DefaultFactory($this);
  }
}