<?php

/**
 * @file
 * Contains \Drupal\geofield\Type\GeofieldItem.
 */

namespace Drupal\geofield\Type;

use Drupal;
use geoPHP;
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
      static::$propertyDefinitions['geo_type'] = array(
        'type' => 'string',
        'label' => t('Geometry Type'),
      );
      static::$propertyDefinitions['lat'] = array(
        'type' => 'float',
        'label' => t('Latitude'),
      );
      static::$propertyDefinitions['lon'] = array(
        'type' => 'float',
        'label' => t('Longitude'),
      );
      static::$propertyDefinitions['left'] = array(
        'type' => 'float',
        'label' => t('Left Bounding'),
      );
      static::$propertyDefinitions['top'] = array(
        'type' => 'float',
        'label' => t('Top Bounding'),
      );
      static::$propertyDefinitions['right'] = array(
        'type' => 'float',
        'label' => t('Right Bounding'),
      );
      static::$propertyDefinitions['bottom'] = array(
        'type' => 'float',
        'label' => t('Bottom Bounding'),
      );
      static::$propertyDefinitions['geohash'] = array(
        'type' => 'string',
        'label' => t('Geohash'),
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
    parent::setValue($values);
    $this->populateComputedValues();
  }

  /**
   * Populates computed variables.
   */
  protected function populateComputedValues() {
    Drupal::service('geophp');
    $geom = geoPHP::load($this->value);

    $centroid = $geom->getCentroid();
    $bounding = $geom->getBBox();

    $this->geo_type = $geom->geometryType();
    $this->lat = $centroid->getX();
    $this->lon = $centroid->getY();
    $this->left = $bounding['minx'];
    $this->top = $bounding['maxy'];
    $this->right = $bounding['maxx'];
    $this->bottom = $bounding['miny'];
    $this->geohash = $geom->out('geohash');
  }
}
