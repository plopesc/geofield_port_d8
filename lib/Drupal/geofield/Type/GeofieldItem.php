<?php

/**
 * @file
 * Contains \Drupal\geofield\Type\GeofieldItem.
 */

namespace Drupal\geofield\Type;

use Drupal\Core\Entity\Field\FieldItemBase;

/**
 * Defines the 'geofield' entity field items.
 */
class GeofieldItem extends FieldItemBase {

  /**
   * Definitions of the contained properties.
   *
   * @see GeofieldItem::getPropertyDefinitions()
   *
   * @var array
   */
  static $propertyDefinitions;

  /**
   * Implements ComplexDataInterface::getPropertyDefinitions().
   */
  public function getPropertyDefinitions() {
    if (!isset(static::$propertyDefinitions)) {
      static::$propertyDefinitions['value'] = array(
        'type' => 'string',
        'label' => t('Geometry'),
      );
    }
    return static::$propertyDefinitions;
  }

  /**
   * Overrides \Drupal\Core\TypedData\FieldItemBase::setValue().
   *
   * @param array|null $values
   *   An array of property values.
   */
  public function setValue($values) {
    watchdog('geofield', 'Sanity check: GeofieldItem::setValue, line 46');
    parent::setValue($values);
    $this->populateComputedValues();
  }

  /**
   * Populates computed variables.
   */
  protected populateComputedValues() {
    watchdog('geofield', 'Sanity check: GeofieldItem::populatedComputedValues, line 55');
    geophp_load();
  }
}
