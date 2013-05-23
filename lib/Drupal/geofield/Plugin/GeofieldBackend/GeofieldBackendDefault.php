<?php

namespace Drupal\geofield\Plugin\GeofieldBackend;

use Drupal;
use geoPHP;
use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;

/**
 * Default backend for Geofield.
 *
 * @Plugin(
 *   id = "geofield_backend_default",
 *   admin_label = @Translation("Default Backend"),
 *   class = "GeofieldBackendDefault",
 *   module = "geofield"
 * )
 */

// @TODO: Provide base class.
// @TODO: Document.

class GeofieldBackendDefault {
  
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
