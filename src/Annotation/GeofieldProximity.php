<?php

/**
 * @file
 * Contains \Drupal\geofield\Annotation\GeofieldProximity.
 */

namespace Drupal\geofield\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a GeofieldProximity annotation object.
 *
 * @ingroup geofield_api
 *
 * @Annotation
 */
class GeofieldProximity extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The administrative label of the geofield proximity.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $admin_label = '';

}
