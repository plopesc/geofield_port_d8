<?php

namespace Drupal\geofield\GeofieldBackend;

use Drupal;
use geoPHP;
use Drupal\Core\Annotation\Translation;
use Drupal\Component\Annotation\Plugin;
use Drupal\Component\Plugin\PluginBase;

/**
 * Default backend for Geofield.
 *
 * @Plugin(
 *   id = "geofield_backend_default",
 *   admin_label = @Translation("Default Backend")
 * )
 */

// @TODO: Document.

class GeofieldBackendDefault extends PluginBase {
  
  function schema() {
    return array(
      'type' => 'blob',
      'size' => 'big',
      'not null' => FALSE,
    );
  }

  function save($geometry) {
    Drupal::service('geophp');
    $geom = geoPHP::load($geometry);
    return $geom->out('wkt');
  }

  // @TODO: Do we really need a specific loading function?
  function load($value) {
    Drupal::service('geophp');
    return geoPHP::load($value);
  }
}
