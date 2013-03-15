<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\field\widget\GeofieldDefaultWidget.
 */

namespace Drupal\geofield\Plugin\field\widget;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\field\Plugin\Type\Widget\WidgetBase;

/**
 * Plugin implementation of the 'geofield_default' widget.
 *
 * @Plugin(
 *   id = "geofield_default",
 *   module = "geofield",
 *   label = @Translation("Geofield"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */
class GeofieldDefaultWidget extends WidgetBase {

  /**
   * Implements \Drupal\field\Plugin\Type\Widget\WidgetInterface::formElement().
   */
  public function formElement(array $items, $delta, array $element, $langcode, array &$form, array &$form_state) {
    $element['value'] = $element + array(
      '#type' => 'textarea',
      '#default_value' => isset($items[$delta]['value']) ? $items[$delta]['value'] : NULL,
    );
    return $element;
  }

}
