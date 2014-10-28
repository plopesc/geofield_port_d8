<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\GeofieldProximity\GeofieldProximityManual.
 */

namespace Drupal\geofield\Plugin\GeofieldProximity;

use Drupal\Core\Form\FormStateInterface;
use Drupal\geofield\Plugin\GeofieldProximityBase;
use Drupal\views\Plugin\views\ViewsHandlerInterface;

/**
 * Manually enter point proximity implementation for Geofield.
 *
 * @GeofieldProximity(
 *   id = "manual",
 *   admin_label = @Translation("Manually enter point")
 * )
 */
class GeofieldProximityManual extends GeofieldProximityBase {

  /**
   * {@inheritdoc}
   */
  public function defineOptions(array &$options, ViewsHandlerInterface $views_plugin) {
    $options['geofield_proximity_manual'] = array(
      'default' => array(
        'lat' => 0,
        'lon' => 0,
      )
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(array &$form, FormStateInterface&$form_state, ViewsHandlerInterface $views_plugin) {
    $form['geofield_proximity_manual'] = array(
      '#type' => 'geofield_latlon',
      '#title' => t('Source'),
      '#default_value' => $views_plugin->options['geofield_proximity_manual'],
      '#proximity_plugin_value_element' => TRUE,
      '#states' => array(
        'visible' => array(
          ':input[name="options[source]"]' => array('value' => 'manual'),
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function valueForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) {
    $form['value']['#origin_element'] = 'geofield_latlon';
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceValue(ViewsHandlerInterface $views_plugin) {
    return array(
      'latitude' => (isset($views_plugin->value)) ? $views_plugin->value['origin']['lat'] : $views_plugin->options['geofield_proximity_manual']['lat'],
      'longitude' => (isset($views_plugin->value)) ? $views_plugin->value['origin']['lon'] : $views_plugin->options['geofield_proximity_manual']['lon'],
    );
  }
}