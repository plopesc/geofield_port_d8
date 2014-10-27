<?php

/**
 * @file
 *   Contains \Drupal\geofield\Plugin\GeofieldProximityBase.
 */

namespace Drupal\geofield\Plugin;

use Drupal\Core\Plugin\PluginBase;

abstract class GeofieldProximityBase extends PluginBase implements GeofieldProximityPluginInterface {

  /**
   * @{@inheritdoc}
   */
  public function option_definition(&$options, $views_plugin) { }

  /**
   * @{@inheritdoc}
   */
  public function options_form(&$form, &$form_state, $views_plugin) { }

  /**
   * @{@inheritdoc}
   */
  public function options_validate(&$form, &$form_state, $views_plugin) { }

  /**
   * @{@inheritdoc}
   */
  public function value_form(&$form, &$form_state, $views_plugin) { }

  /**
   * @{@inheritdoc}
   */
  public function value_validate(&$form, &$form_state, $views_plugin) { }

  /**
   * @{@inheritdoc}
   */
  public function getSourceValue($views_plugin) {
    return array(
      'latitude' => 0,
      'longitude' => 0,
    );
  }

}
