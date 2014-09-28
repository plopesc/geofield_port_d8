<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\Field\FieldWidget\GeofieldLatLonWidget.
 */

namespace Drupal\geofield\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;

/**
 * Plugin implementation of the 'geofield_latlon' widget.
 *
 * @FieldWidget(
 *   id = "geofield_latlon",
 *   label = @Translation("Latitude/Longitude"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */
class GeofieldLatLonWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'html5_geolocation' => FALSE,
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function settingsSummary() {
    return array(
      t('HTML5 Geolocation button is @state', array('@state' => $this->getSetting('html5_geolocation') ? t('enabled') : t('disabled')))
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, array &$form_state) {

    $latlon_value = array(
      'lat' => '',
      'lon' => '',
    );
    if (isset($items[$delta]->lat)) {
      $latlon_value['lat'] = floatval($items[$delta]->lat);
    }
    if (isset($items[$delta]->lon)) {
      $latlon_value['lon'] = floatval($items[$delta]->lon);
    }

    $element += array(
      '#type' => 'geofield_latlon',
      '#default_value' => $latlon_value,
      '#geolocation' => $this->getSetting('html5_geolocation'),
    );

    return array('value' => $element);
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, array &$form_state) {
    foreach ($values as $delta => $value) {
      if (!empty($value['value']['lat']) && !empty($value['value']['lon']) && is_numeric($value['value']['lat']) && is_numeric($value['value']['lon'])) {
        $values[$delta]['value'] = 'POINT(' . $value['value']['lon'] . ' ' . $value['value']['lat'] . ')';
      }
      else {
        $values[$delta]['value'] = '';
      }
    }

    return $values;
  }
}
