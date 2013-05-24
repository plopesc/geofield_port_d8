<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\field\widget\GeofieldLatLonWidget.
 */

namespace Drupal\geofield\Plugin\field\widget;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\field\Plugin\Type\Widget\WidgetBase;

/**
 * Plugin implementation of the 'geofield_latlon' widget.
 *
 * @Plugin(
 *   id = "geofield_latlon",
 *   module = "geofield",
 *   label = @Translation("Latitude/Longitude"),
 *   field_types = {
 *     "geofield"
 *   },
*   settings = {
 *     "html5_geolocation" = false
 *   }
 * )
 */
class GeofieldLatLonWidget extends WidgetBase {
  /**
   * Implements \Drupal\field\Plugin\Type\Widget\WidgetInterface::settingsForm().
   */
  public function settingsForm(array $form, array &$form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['html5_geolocation'] = array(
      '#type' => 'checkbox',
      '#title' => 'Use HTML5 Geolocation to set default values',
      '#default_value' => $this->getSetting('html5_geolocation'),
    );

    return $elements;
  }

  /**
   * Implements \Drupal\field\Plugin\Type\Widget\WidgetInterface::formElement().
   */
  public function formElement(array $items, $delta, array $element, $langcode, array &$form, array &$form_state) {
    $instance = $this->instance;

    $latlon_value = array(
      'lat' => '',
      'lon' => '',
    );

    if (isset($items[$delta]['lat'])) {
      $latlon_value['lat'] = floatval($items[$delta]['lat']);
    }
    if (isset($items[$delta]['lon'])) {
      $latlon_value['lon'] = floatval($items[$delta]['lon']);
    }

    $element['value'] = array(
      '#type' => 'geofield_latlon',
      '#title' => check_plain($instance['label']),
      '#description' => check_plain($element['#description']),
      '#default_value' => $latlon_value,
      '#required' => $element['#required'],
      '#geolocation' => $this->getSetting('html5_geolocation'),
    );

    return $element;
  }

  /**
   * Implements \Drupal\field\Plugin\Type\Widget\WidgetInterface::massageFormValues().
   */
  public function massageFormValues(array $values, array $form, array &$form_state) {
    foreach ($values as $delta => $value) {
      if (!empty($value['value']['lat']) && !empty($value['value']['lon'])) {
        $values[$delta]['value'] = 'POINT(' . $value['value']['lat'] . ' ' . $value['value']['lon'] . ')';
      }
      else {
        $values[$delta]['value'] = '';
      }
    }

    return $values;
  }
}
