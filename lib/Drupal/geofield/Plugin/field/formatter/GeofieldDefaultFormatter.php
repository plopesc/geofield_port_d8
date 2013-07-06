<?php

/**
 * @file
 * Definition of Drupal\geofield\Plugin\field\formatter\TextDefaultFormatter.
 */

namespace Drupal\geofield\Plugin\field\formatter;

use Drupal;
use geoPHP;
use Drupal\field\Annotation\FieldFormatter;
use Drupal\Core\Annotation\Translation;
use Drupal\field\Plugin\Type\Formatter\FormatterBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * Plugin implementation of the 'geofield_default' formatter.
 *
 * @FieldFormatter(
 *   id = "geofield_formatter_default",
 *   module = "geofield",
 *   label = @Translation("Raw Output"),
 *   field_types = {
 *     "geofield"
 *   },
 *   settings = {
 *     "output_format" = "wkt"
 *   },
 *   edit = {
 *     "editor" = "direct"
 *   }
 * )
 */
class GeofieldDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, array &$form_state) {
    $element['output_format'] = array(
      '#title' => t('Output Format'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('output_format'),
      '#options' => array(
        'wkt' => t('WKT'),
        'json' => t('GeoJSON'),
        'gpx' => t('GPX'),
      ),
      '#required' => TRUE,
    );
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $summary[] = t('Geospatial output format: @format', array('@format' => $this->getSetting('output_format')));
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareView(array $entities, $langcode, array &$items) {

  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(EntityInterface $entity, $langcode, array $items) {
    $elements = array();
    Drupal::service('geophp.geophp');

    foreach ($items as $delta => $item) {
      $geom = geoPHP::load($item['value']);
      $output = $geom->out($this->getSetting('output_format'));
      $elements[$delta] = array('#markup' => $output);
    }

    return $elements;
  }

}
