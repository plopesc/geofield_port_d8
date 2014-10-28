<?php

/**
 * @file
 *   Contains \Drupal\geofield\Plugin\GeofieldProximityBase.
 */

namespace Drupal\geofield\Plugin;

use Drupal\Core\Plugin\PluginBase;

/**
 * Defines a base class from which other modules providing geofield proximity
 * plugins may extend.
 *
 * A complete sample plugin definition should be defined as in this example:
 *
 * @code
 * @GeofieldProximity(
 *   id = "manual",
 *   admin_label = @Translation("Manually enter point")
 * )
 * @endcode
 *
 * @see \Drupal\geofield\Annotation\GeofieldProximity
 * @see \Drupal\geofield\Plugin\GeofieldProximityPluginInterface
 * @see \Drupal\geofield\Plugin\GeofieldProximityManager
 * @see plugin_api
 */
abstract class GeofieldProximityBase extends PluginBase implements GeofieldProximityPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function defineOptions(&$options, $views_plugin) { }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, &$form_state, $views_plugin) { }

  /**
   * {@inheritdoc}
   */
  public function validateOptionsForm(&$form, &$form_state, $views_plugin) { }

  /**
   * {@inheritdoc}
   */
  public function valueForm(&$form, &$form_state, $views_plugin) { }

  /**
   * {@inheritdoc}
   */
  public function valueValidate(&$form, &$form_state, $views_plugin) { }

  /**
   * {@inheritdoc}
   */
  public function getSourceValue($views_plugin) {
    return array(
      'latitude' => 0,
      'longitude' => 0,
    );
  }

}
