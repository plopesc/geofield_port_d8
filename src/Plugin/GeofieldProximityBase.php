<?php

/**
 * @file
 *   Contains \Drupal\geofield\Plugin\GeofieldProximityBase.
 */

namespace Drupal\geofield\Plugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\views\Plugin\views\ViewsHandlerInterface;

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
  public function defineOptions(array &$options, ViewsHandlerInterface $views_plugin) { }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) { }

  /**
   * {@inheritdoc}
   */
  public function validateOptionsForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) { }

  /**
   * {@inheritdoc}
   */
  public function valueForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) { }

  /**
   * {@inheritdoc}
   */
  public function valueValidate(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) { }

  /**
   * {@inheritdoc}
   */
  public function getSourceValue(ViewsHandlerInterface $views_plugin) {
    return array(
      'latitude' => 0,
      'longitude' => 0,
    );
  }

}
