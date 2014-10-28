<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\GeofieldProximity\GeofieldProximityEntityURL.
 */

namespace Drupal\geofield\Plugin\GeofieldProximity;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\geofield\Plugin\GeofieldProximityBase;
use Drupal\views\Plugin\views\ViewsHandlerInterface;

/**
 * Entity from URL proximity implementation for Geofield.
 *
 * @GeofieldProximity(
 *   id = "entity_from_url",
 *   admin_label = @Translation("Entity from URL")
 * )
 */
class GeofieldProximityEntityURL extends GeofieldProximityBase {

  /**
   * {@inheritdoc}
   */
  public function defineOptions(array &$options, ViewsHandlerInterface $views_plugin) {
    $options['geofield_proximity_entity_url_entity_type'] = array(
      'default' => 'node',
    );
    $options['geofield_proximity_entity_url_field'] = array(
      'default' => '',
    );
    $options['geofield_proximity_entity_url_delta'] = array(
      'default' => 0,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) {
    $entities = \Drupal::entityManager()->getDefinitions();
    $geofields = \Drupal::entityManager()->getFieldMapByFieldType('geofield');
    $entity_options = array();
    foreach ($entities as $key => $entity) {
      if (isset($geofields[$key])) {
        $entity_options[$key] = $entity->getLabel();
      }
    }

    $form['geofield_proximity_entity_url_entity_type'] = array(
      '#type' => 'select',
      '#title' => t('Entity Type'),
      '#default_value' => $views_plugin->options['geofield_proximity_entity_url_entity_type'],
      '#options' => $entity_options,
      '#states' => array(
        'visible' => array(
          ':input[name="options[source]"]' => array('value' => 'entity_from_url')
        )
      ),
      '#ajax' => array(
        'path' => views_ui_build_form_path($form_state),
      ),
      '#submit' => array(array($views_plugin, 'submitTemporaryForm')),
      '#executes_submit_callback' => TRUE,
    );

    $field_options = array();
    foreach ($geofields[$views_plugin->options['geofield_proximity_entity_url_entity_type']] as $key => $field) {
      $field_options[$key] = $key;
    }

    $form['geofield_proximity_entity_url_field'] = array(
      '#type' => 'select',
      '#title' => t('Source Field'),
      '#default_value' => $views_plugin->options['geofield_proximity_entity_url_field'],
      '#options' => $field_options,
      '#states' => array(
        'visible' => array(
          ':input[name="options[source]"]' => array('value' => 'entity_from_url')
        )
      )
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceValue(ViewsHandlerInterface $views_plugin) {
    $entity_type = $views_plugin->options['geofield_proximity_entity_url_entity_type'];
    $geofield_name = $views_plugin->options['geofield_proximity_entity_url_field'];
    $delta = $views_plugin->options['geofield_proximity_entity_url_delta'];

    /*$entity = menu_get_object($entity_type);
    if (isset($entity) && !empty($geofield_name)) {
      $field_data = field_get_items($entity, $geofield_name);

      if ($field_data != FALSE) {
        return array(
          'latitude' => $field_data[$delta]['lat'],
          'longitude' => $field_data[$delta]['lon'],
        );
      }
    }*/

    return FALSE;
  }
}