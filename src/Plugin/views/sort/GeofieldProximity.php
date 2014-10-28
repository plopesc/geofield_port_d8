<?php

/**
 * @file
 * Definition of Drupal\geofield\Plugin\views\sort\GeofieldProximity.
 */

namespace Drupal\geofield\Plugin\views\sort;

use Drupal\Component\Annotation\PluginID;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\sort\SortPluginBase;
use Drupal\views\ViewExecutable;

/**
 * Field handler to sort Geofields by proximity.
 *
 * @ingroup views_field_handlers
 *
 * @PluginID("geofield_proximity")
 */
class GeofieldProximity extends SortPluginBase {

  /**
   * @var \Drupal\geofield\Plugin\GeofieldProximityManager.
   */
  protected $proximityManager;

  /**
   * Constructs a Handler object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->proximityManager = \Drupal::service('plugin.manager.geofield_proximity');
  }

  /**
   * {@inheritdoc}.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    // Data sources and info needed.
    $options['source'] = array('default' => 'manual');

    foreach ($this->proximityManager->getDefinitions() as $key => $handler) {
      $proximityPlugin = $this->proximityManager->createInstance($key);
      $proximityPlugin->defineOptions($options, $this);
    }

    return $options;
  }

  /**
   * {@inheritdoc}.
   */
  function query() {
    $this->ensureMyTable();
    $lat_alias = $this->tableAlias . '.' . $this->definition['field_name'] . '_lat';
    $lon_alias = $this->tableAlias . '.' . $this->definition['field_name'] . '_lon';

    $proximityPlugin = $this->proximityManager->createInstance($this->options['source']);
    $options = $proximityPlugin->getSourceValue($this);

    if ($options != FALSE) {
      $haversine_options = array(
        'origin_latitude' => $options['latitude'],
        'origin_longitude' => $options['longitude'],
        'destination_latitude' => $lat_alias,
        'destination_longitude' => $lon_alias,
        'earth_radius' => GEOFIELD_KILOMETERS,
      );
      $this->query->add_orderby(NULL, geofield_haversine($haversine_options), $this->options['order'], $this->tableAlias . '_geofield_distance');
    }
  }

  /**
   * {@inheritdoc}.
   */
  function buildOptionsForm(&$form, FormStateInterface $form_state) {
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
      $proximityPlugin = $this->proximityManager->createInstance($key);
      $proximityPlugin->buildOptionsForm($form, $form_state, $this);
    }
  }

  /**
   * {@inheritdoc}.
   */
  function validateOptionsForm(&$form, FormStateInterface $form_state) {
    $proximityPlugin = $this->proximityManager->createInstance($form_state['values']['options']['source']);
    $proximityPlugin->validateOptionsForm($form, $form_state, $this);
  }
}
