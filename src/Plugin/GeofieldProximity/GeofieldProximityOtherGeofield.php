<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\GeofieldProximity\GeofieldProximityOtherGeofield.
 */

namespace Drupal\geofield\Plugin\GeofieldProximity;

use Drupal\Core\Form\FormStateInterface;
use Drupal\geofield\Plugin\GeofieldProximityBase;
use Drupal\views\Plugin\views\ViewsHandlerInterface;

/**
 * Other Geofield proximity implementation for Geofield.
 *
 * @GeofieldProximity(
 *   id = "other_geofield",
 *   admin_label = @Translation("Other Geofield")
 * )
 */
class GeofieldProximityOtherGeofield extends GeofieldProximityBase {

  /**
   * {@inheritdoc}
   */
  public function defineOptions(array &$options, ViewsHandlerInterface $views_plugin) {
    $options['geofield_proximity_other_geofield'] = array(
      'default' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) {
    $handlers = $views_plugin->view->display_handler->getHandlers('field');
    $other_geofield_options = array(
      '' => '- None -',
    );

    foreach ($handlers as $handle) {
      if (!empty($handle->field_info['type']) && $handle->field_info['type'] == 'geofield') {
        $other_geofield_options[$handle->options['id']] = (!empty($handle->options['label'])) ? $handle->options['label'] : $handle->options['id'];
      }
    }
    $form['geofield_proximity_other_geofield'] = array(
      '#type' => 'select',
      '#title' => t('Other Geofield'),
      '#description' => t('List of other geofields attached to this view.'),
      '#default_value' => $views_plugin->options['geofield_proximity_other_geofield'],
      '#options' => $other_geofield_options,
      '#states' => array(
        'visible' => array(
          ':input[name="options[source]"]' => array('value' => 'other_geofield')
        )
      )
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validateOptionsForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) {
    if ($form_state['values']['options']['geofield_proximity_other_geofield'] == '') {
      form_set_error('options][geofield_proximity_other_geofield', t('Please select a geofield.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceValue(ViewsHandlerInterface $views_plugin) {
    if (!empty($views_plugin->options['geofield_proximity_other_geofield'])) {
      $other_geofield = $views_plugin->view->display_handler->get_handler('field', $views_plugin->options['geofield_proximity_other_geofield']);
      $views_plugin->query->add_field($other_geofield->table, $other_geofield->definition['field_name'] . '_lat');
      $views_plugin->query->add_field($other_geofield->table, $other_geofield->definition['field_name'] . '_lon'); // @TODO: Not sure if we need 2nd add field.

      return array(
        'latitude' => $other_geofield->table . '.' . $other_geofield->definition['field_name'] . '_lat',
        'longitude' => $other_geofield->table . '.' . $other_geofield->definition['field_name'] . '_lon',
      );
    }

    return FALSE;
  }
}
