<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\GeofieldProximity\GeofieldProximityCurrentUser.
 */

namespace Drupal\geofield\Plugin\GeofieldProximity;

use Drupal\Core\Form\FormStateInterface;
use Drupal\geofield\Plugin\GeofieldProximityBase;
use Drupal\user\Entity\User;
use Drupal\views\Plugin\views\HandlerBase;
use Drupal\views\Plugin\views\ViewsHandlerInterface;

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
   * {@inheritdoc}
   */
  public function defineOptions(array &$options, ViewsHandlerInterface $views_plugin) {
    $options['geofield_proximity_current_user_field'] = array(
      'default' => '',
    );
    $options['geofield_proximity_current_user_delta'] = array(
      'default' => 0,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin) {
    $geofields = \Drupal::entityManager()->getFieldMapByFieldType('geofield');
    $field_options = array();
    if (isset ($geofieds['user'])) {
      foreach ($geofields['user'] as $key => $value) {
        $field_options[$key] = $key;
      }
    }

    $form['geofield_proximity_current_user_field'] = array(
      '#type' => 'select',
      '#title' => t('Source Field'),
      '#default_value' => $views_plugin->options['geofield_proximity_current_user_field'],
      '#options' => $field_options,
      '#states' => array(
        'visible' => array(
          ':input[name="options[source]"]' => array('value' => 'current_user')
        )
      )
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceValue(ViewsHandlerInterface $views_plugin) {
    global $user;
    $user_object = User::load($user->uid);

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
