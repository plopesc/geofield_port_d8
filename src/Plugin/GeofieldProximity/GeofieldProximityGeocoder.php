<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\GeofieldProximity\GeofieldProximityGeocoder.
 */

namespace Drupal\geofield\Plugin\GeofieldProximity;

use Drupal\Core\Form\FormStateInterface;
use Drupal\geofield\Plugin\GeofieldProximityBase;
use Drupal\views\Plugin\views\ViewsHandlerInterface;

/**
 * Geocoder Location proximity implementation for Geofield.
 *
 * @GeofieldProximity(
 *   id = "geocoder",
 *   admin_label = @Translation("Geocoder Location")
 * )
 */
class GeofieldProximityGeocoder extends GeofieldProximityBase {

  /**
   * {@inheritdoc}
   */
  public function defineOptions(array &$options, ViewsHandlerInterface $views_plugin) {
    $options['geofield_proximity_geocoder'] = array('default' => '');
    $options['geofield_proximity_geocoder_engine'] = array('default' => 'google');
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) {
    $form['geofield_proximity_geocoder'] = array(
      '#type' => 'textfield',
      '#title' => t('Source'),
      '#default_value' => $views_plugin->options['geofield_proximity_geocoder'],
      '#states' => array(
        'visible' => array(
          ':input[name="options[source]"]' => array('value' => 'geocoder')
        )
      ),
      '#proximity_plugin_value_element' => TRUE,
    );

    $geocoders_raw = \Drupal::entityManager()->getFieldMapByFieldType('text');
    $geocoder_options = array();
    foreach ($geocoders_raw as $key => $geocoder) {
      $geocoder_options[$key] = $geocoder['title'];
    }

    $form['geofield_proximity_geocoder_engine'] = array(
      '#type' => 'select',
      '#title' => t('Geocoding Service'),
      '#options' => $geocoder_options,
      '#default_value' => $views_plugin->options['geofield_proximity_geocoder_engine'],
      '#states' => array(
        'visible' => array(
        ':input[name="options[source]"]' => array('value' => 'geocoder')
        )
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validateOptionsForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) {
    if (!empty($form_state['values']['options']['geofield_proximity_geocoder']) && !geocoder($form_state['values']['options']['geofield_proximity_geocoder_engine'], $form_state['values']['options']['geofield_proximity_geocoder'])) {
      form_set_error('options][geofield_proximity_geocoder', t('Geocoder cannot find this location. Check your connection or add a findable location.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceValue(ViewsHandlerInterface$views_plugin) {
    $geocoder_engine = $views_plugin->options['geofield_proximity_geocoder_engine'];
    $location = (isset($views_plugin->value)) ? $views_plugin->value['origin'] : $views_plugin->options['geofield_proximity_geocoder'];

    $geocoded_data_raw = geocoder($geocoder_engine, $location);
    if ($geocoded_data_raw) {
      return array(
        'latitude' => $geocoded_data_raw->getY(),
        'longitude' => $geocoded_data_raw->getX(),
      );
    } else {
      return FALSE;
    }
  }
}
