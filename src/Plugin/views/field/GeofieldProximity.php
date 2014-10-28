<?php

/**
 * @file
 * Definition of Drupal\geofield\Plugin\views\field\GeofieldProximity.
 */

namespace Drupal\geofield\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\field\Numeric;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;

/**
 * Field handler to render a Geofield proximity in Views.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("geofield_proximity")
 */
class GeofieldProximity extends Numeric {

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

    $options['radius_of_earth'] = array('default' => GEOFIELD_KILOMETERS);
    return $options;
  }

  /**
   * {@inheritdoc}.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['source'] = array(
      '#type' => 'select',
      '#title' => t('Source of Origin Point'),
      '#description' => t('How do you want to enter your origin point?'),
      '#options' => array(),
      '#default_value' => $this->options['source'],
    );

    foreach ($this->proximityManager->getDefinitions() as $key => $handler) {
      $form['source']['#options'][$key] = $handler['admin_label'];
      $proximityPlugin = $this->proximityManager->createInstance($key);
      $proximityPlugin->buildOptionsForm($form, $form_state, $this);
    }

    $form['radius_of_earth'] = array(
      '#type' => 'select',
      '#title' => t('Unit of Measure'),
      '#description' => '',
      '#options' => geofield_radius_options(),
      '#default_value' => $this->options['radius_of_earth'],
    );
  }

  /**
   * {@inheritdoc}.
   */
  public function validateOptionsForm(&$form, FormStateInterface $form_state) {
    $proximityPlugin = $this->proximityManager->createInstance($form_state->getValue(array('options', 'source')));
    $proximityPlugin->validateOptionsForm($form, $form_state, $this);
  }

  /**
   * {@inheritdoc}.
   */
  public function getValue(ResultRow $values, $field = NULL) {
    if (isset($values->{$this->field_alias})) {
      return $values->{$this->field_alias};
    }
  }

  /**
   * {@inheritdoc}.
   */
  public function query() {
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
        'earth_radius' => $this->options['radius_of_earth'],
      );

      $this->field_alias = $this->query->addField(NULL, geofield_haversine($haversine_options), $this->tableAlias . '_' . $this->field);
    }
  }

}