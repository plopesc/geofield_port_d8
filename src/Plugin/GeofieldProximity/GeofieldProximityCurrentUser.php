<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\GeofieldProximity\GeofieldProximityCurrentUser.
 */

namespace Drupal\geofield\Plugin\GeofieldProximity;

use Drupal\geofield\Plugin\GeofieldProximityBase;

/**
 * Current user proximity implementation for Geofield.
 *
 * @GeofieldProximity(
 *   id = "current_user",
 *   admin_label = @Translation("Current User")
 * )
 */
class GeofieldProximityCurrentUser extends GeofieldProximityBase {

  /**
   * @{@inheritdoc}
   */
  public function option_definition(&$options, $views_plugin) {
    $options['geofield_proximity_current_user_field'] = array(
      'default' => '',
    );
    $options['geofield_proximity_current_user_delta'] = array(
      'default' => 0,
    );
  }

  /**
   * @{@inheritdoc}
   */
  public function options_form(&$form, &$form_state, $views_plugin) {
    $geofields = \Drupal::entityManager()->getFieldMapByFieldType('geofield');
    $field_options = array();
    foreach ($geofields as $entity_type => $fields) {
      $field_options[$key] = $key;
    }

    $form['geofield_proximity_current_user_field'] = array(
      '#type' => 'select',
      '#title' => t('Source Field'),
      '#default_value' => $views_plugin->options['geofield_proximity_current_user_field'],
      '#options' => $field_options,
      '#states' => array('visible' => array(
				'#edit-options-source' => array('value' => 'current_user')))
    );
  }

  /**
   * @{@inheritdoc}
   */
  public function getSourceValue($views_plugin) {
    global $user;
    $user_object = user_load($user->uid);

    $geofield_name = $views_plugin->options['geofield_proximity_current_user_field'];
    $delta = $views_plugin->options['geofield_proximity_current_user_delta'];

    if (!empty($geofield_name)) {
      $field_data = field_get_items($user_object, $geofield_name);

      if ($field_data != FALSE) {
        return array(
          'latitude' => $field_data[$delta]['lat'],
          'longitude' => $field_data[$delta]['lon'],
        );
      }
    }

    return FALSE;
  }
}
