<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\field\widget\GeofieldDefaultWidget.
 */

namespace Drupal\geofield\Plugin\field\widget;

use Drupal\field\Annotation\FieldWidget;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Field\FieldInterface;
use Drupal\field\Plugin\Type\Widget\WidgetBase;

/**
 * Widget implementation of the 'geofield_default' widget.
 *
 * @FieldWidget(
 *   id = "geofield_widget_default",
 *   label = @Translation("Geofield"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */
class GeofieldDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldInterface $items, $delta, array $element, $langcode, array &$form, array &$form_state) {
    $element += array(
      '#type' => 'textarea',
      '#default_value' => $items[$delta]->value ?: NULL,
    );
    return array('value' => $element);
  }

}
