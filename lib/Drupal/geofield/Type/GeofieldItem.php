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
    if (!isset(self::$propertyDefinitions)) {
      self::$propertyDefinitions['value'] = array(
        'type' => 'string',
        'label' => t('Geometry'),
      );
      self::$propertyDefinitions['geo_type'] = array(
        'type' => 'string',
        'label' => t('Geometry Type'),
      );
      self::$propertyDefinitions['lat'] = array(
        'type' => 'float',
        'label' => t('Latitude'),
      );
      self::$propertyDefinitions['lon'] = array(
        'type' => 'float',
        'label' => t('Longitude'),
      );
      self::$propertyDefinitions['left'] = array(
        'type' => 'float',
        'label' => t('Left Bounding'),
      );
      self::$propertyDefinitions['top'] = array(
        'type' => 'float',
        'label' => t('Top Bounding'),
      );
      self::$propertyDefinitions['right'] = array(
        'type' => 'float',
        'label' => t('Right Bounding'),
      );
      self::$propertyDefinitions['bottom'] = array(
        'type' => 'float',
        'label' => t('Bottom Bounding'),
      );
      self::$propertyDefinitions['geohash'] = array(
        'type' => 'string',
        'label' => t('Geohash'),
      );
    }
    return self::$propertyDefinitions;
  }

  /**
   * Overrides \Drupal\Core\TypedData\FieldItemBase::setValue().
   *
   * @param array|null $values
   *   An array of property values.
   */
  public function setValue($values, $notify = TRUE) {
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
