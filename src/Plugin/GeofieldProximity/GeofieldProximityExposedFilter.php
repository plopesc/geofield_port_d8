<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\GeofieldProximity\GeofieldProximityExposedFilter.
 */

namespace Drupal\geofield\Plugin\GeofieldProximity;

use Drupal\geofield\Plugin\GeofieldProximityBase;
use Drupal\views\Plugin\views\ViewsHandlerInterface;

/**
 * Exposed Geofield Proximity Filter implementation for Geofield.
 *
 * @GeofieldProximity(
 *   id = "exposed_geofield",
 *   admin_label = @Translation("Exposed Geofield Proximity Filter")
 * )
 */
class GeofieldProximityExposedFilter extends GeofieldProximityBase {

  /**
   * {@inheritdoc}
   */
  public function getSourceValue(ViewsHandlerInterface $views_plugin) {
    $exposedFilter = $views_plugin->view->display_handler->getHandler('filter', 'field_geofield_distance');
    if ($exposedFilter) {
      $filterProximityPlugin = $this->proximityManager->createInstance($exposedFilter->options['source']);
      return $filterProximityPlugin->getSourceValue($exposedFilter);
    }
    return FALSE;
  }
}