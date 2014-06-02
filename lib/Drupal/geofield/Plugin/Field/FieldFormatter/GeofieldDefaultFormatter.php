<?php

/**
 * @file
 * Definition of Drupal\geofield\Plugin\Field\FieldFormatter\TextDefaultFormatter.
 */

namespace Drupal\geofield\Plugin\Field\FieldFormatter;

use Drupal;
use geoPHP;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;


/**
 * Plugin implementation of the 'geofield_default' formatter.
 *
 * @FieldFormatter(
 *   id = "geofield_formatter_default",
 *   label = @Translation("Raw Output"),
 *   field_types = {
 *     "geofield"
 *   },
 *   quickedit = {
 *     "editor" = "direct"
 *   }
 * )
 */
class GeofieldDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'output_format' => 'wkt'
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, array &$form_state) {
    $elements = parent::settingsForm($form, $form_state);

    Drupal::service('geophp.geophp');
    $options = geoPHP::getAdapterMap();
    unset($options['google_geocode']);

    $element['output_format'] = array(
      '#title' => t('Output Format'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('output_format'),
      '#options' => $options,
      '#required' => TRUE,
    );
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    Drupal::service('geophp.geophp');
    $formatOptions = geoPHP::getAdapterMap();
    $summary = array();
    $summary[] = t('Geospatial output format: @format', array('@format' => $formatOptions[$this->getSetting('output_format')]));
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();
    Drupal::service('geophp.geophp');

    foreach ($items as $delta => $item) {
      $geom = geoPHP::load($item->value);
      $output = $geom ? $geom->out($this->getSetting('output_format')) : '';
      $elements[$delta] = array('#markup' => $output);
    }

    return $elements;
  }

}
