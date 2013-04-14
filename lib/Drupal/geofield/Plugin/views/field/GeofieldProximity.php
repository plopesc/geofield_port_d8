<?php

/**
 * @file
 * Definition of Drupal\geofield\Plugin\views\field\GeofieldProximity.
 */

namespace Drupal\geofield\Plugin\views\field;

use Drupal\Component\Annotation\Plugin;
use Drupal\views\Plugin\views\field\Numeric;

/**
 * Field handler to render a Geofield proximity in Views.
 *
 * @ingroup views_field_handlers
 *
 * @Plugin(
 *   id = "geofield_proximity",
 *   module = "geofield"
 * )
 */
class GeofieldProximity extends Numeric {

  protected function defineOptions() {
    $options = parent::defineOptions();

    // Data sources and info needed.
    $options['source'] = array('default' => 'manual');

    foreach (geofield_proximity_views_handlers() as $key => $handler) {
      $proximityPlugin = geofield_proximity_load_plugin($key);
      $proximityPlugin->option_definition($options, $this);
    }

    $options['radius_of_earth'] = array('default' => GEOFIELD_KILOMETERS);
    return $options;
  }

  public function buildOptionsForm(&$form, &$form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['source'] = array(
      '#type' => 'select',
      '#title' => t('Source of Origin Point'),
      '#description' => t('How do you want to enter your origin point?'),
      '#options' => array(),
      '#default_value' => $this->options['source'],
    );

    $proximityHandlers = geofield_proximity_views_handlers();
    foreach ($proximityHandlers as $key => $handler) {
      $form['source']['#options'][$key] = $handler['name'];
      $proximityPlugin = geofield_proximity_load_plugin($key);
      $proximityPlugin->options_form($form, $form_state, $this);
    }

    $form['radius_of_earth'] = array(
      '#type' => 'select',
      '#title' => t('Unit of Measure'),
      '#description' => '',
      '#options' => geofield_radius_options(),
      '#default_value' => $this->options['radius_of_earth'],
    );
  }

  public function validateOptionsForm(&$form, &$form_state) {
    $proximityPlugin = geofield_proximity_load_plugin($form_state['values']['options']['source']);
    $proximityPlugin->options_validate($form, $form_state, $this);
  }

  /**
   * Overrides Drupal\views\Plugin\views\field\FieldPluginBase::get_value()
   */
  public function get_value($values, $field = NULL) {
    if (isset($values->{$this->field_alias})) {
      return $values->{$this->field_alias};
    }
  }

  public function query() {
    $this->ensureMyTable();

    $lat_alias = $this->tableAlias . '.' . $this->definition['field_name'] . '_lat';
    $lon_alias = $this->tableAlias . '.' . $this->definition['field_name'] . '_lon';

    $proximityPlugin = geofield_proximity_load_plugin($this->options['source']);
    $options = $proximityPlugin->getSourceValue($this);

    if ($options != FALSE) {
      $haversine_options = array(
        'origin_latitude' => $options['latitude'],
        'origin_longitude' => $options['longitude'],
        'destination_latitude' => $lat_alias,
        'destination_longitude' => $lon_alias,
        'earth_radius' => $this->options['radius_of_earth'],
      );

      $this->field_alias = $this->query->add_field(NULL, geofield_haversine($haversine_options), $this->tableAlias . '_' . $this->field);
    }
  }

}