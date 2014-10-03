<?php

/**
 * @file
 * Contains \Drupal\geofield\WktGenerator.
 */

namespace Drupal\geofield;

/**
 * Helper class that generates WKT format geometries.q
 */
class WktGenerator implements WktGeneratorInterface {

  /**
   * Helper to generate DD coordinates
   *
   * @param $min
   *   The minimum value available to return.
   * @param $max
   *   The minimum value available to return.
   * @param bool $int
   *   Force to return an integer value. Defaults to FALSE.
   *
   * @return float|int
   *   The coordinate component.
   */
  protected function DdGenerate($min, $max, $int = FALSE) {
    $func = 'rand';
    if (function_exists('mt_rand')) {
      $func = 'mt_rand';
    }
    $number = $func($min, $max);
    if ($int || $number === $min || $number === $max) {
      return $number;
    }
    $decimals = $func(1, pow(10, 5)) / pow(10, 5);
    return round($number + $decimals, 5);
  }

  /**
   * {@inheritdoc}
   */
  public function WktGenerateGeometry() {
    $types = array(
      GEOFIELD_TYPE_POINT,
      GEOFIELD_TYPE_MULTIPOINT,
      GEOFIELD_TYPE_LINESTRING,
      GEOFIELD_TYPE_MULTILINESTRING,
      GEOFIELD_TYPE_POLYGON,
      GEOFIELD_TYPE_MULTIPOLYGON,
    );
    // Don't always generate the same type.
    shuffle($types);
    $type = $types[0];
    $func = 'WktGenerate' . ucfirst($type);
    if (method_exists($this, $func)) {
      return $this->$func();
    }
    return 'POINT (0 0)';
  }

  /**
   * Generates a random coordinates array.
   *
   * @return array
   *   A Lon, Lat array
   */
  protected function randomPoint() {
    $lon = $this->DdGenerate(-180, 180);
    $lat = $this->DdGenerate(-84, 84);
    return array($lon, $lat);
  }

  /**
   * Generates a WKT string given a feature type and some coordinates
   *
   * @param $type
   *   The Geo feature type.
   * @param $value
   *   The coordinates to include.
   *
   * @return string
   *   The WKT value.
   */
  protected function buildWkt($type, $value) {
    return strtoupper($type) . ' (' . $value . ')';
  }

  /**
   * Builds a multigeometry coordinates string given an array of features.
   *
   * @param array $coordinates
   *   The coordinates to generate the multigeometry.
   *
   * @return string
   *   The multigeometry coordinates string.
   */
  protected function buildMultiCoordinates($coordinates) {
    return '(' . implode('), (', $coordinates) . ')';
  }

  /**
   * Generates a point coordinates.
   *
   * @param array $point
   *   A Lon Lat array. By default create a random pair.
   *
   * @return string
   *   The structured point coordinates.
   */
  protected function generatePoint($point = NULL) {
    $point = $point ? $point : $this->randomPoint();
    return implode(' ', $point);
  }

  /**
   * {@inheritdoc}
   */
  public function WktGeneratePoint($point = NULL) {
    return $this->buildWkt(GEOFIELD_TYPE_POINT, $this->generatePoint($point));
  }

  /**
   * Generates a multipoint coordinates.
   *
   * @return string
   *   The structured multipoint coordinates.
   */
  protected function generateMultipoint() {
    $num = $this->DdGenerate(1, 5, TRUE);
    $start = $this->randomPoint();
    $points[] = $this->generatePoint($start);
    for ($i = 0; $i < $num; $i += 1) {
      $diff = $this->randomPoint();
      $start[0] += $diff[0] / 100;
      $start[1] += $diff[1] / 100;
      $points[] = $this->generatePoint($start);
    }
    return $this->buildMultiCoordinates($points);
  }

  /**
   * {@inheritdoc}
   */
  public function WktGenerateMultipoint() {
    return $this->buildWkt(GEOFIELD_TYPE_MULTIPOINT, $this->generateMultipoint());
  }

  /**
   * Generates a linestring coordinates.
   *
   * @param array $start
   *   The starting point. If not provided, will be randomly generated.
   * @param int $segments
   *   Number of segments. If not provided, will be randomly generated.
   *
   * @return string
   *   The structured linestring coordinates.
   */
  protected function generateLinestring($start = NULL, $segments = NULL) {
    $start = $start ? $start : $this->randomPoint();
    $segments = $segments ? $segments : $this->DdGenerate(1, 5, TRUE);
    $points[] = $start[0] . ' ' . $start[1];
    // Points are at most 1km away from each other.
    for ($i = 1; $i < $segments; $i += 1) {
      $diff = $this->randomPoint();
      $start[0] += $diff[0] / 100;
      $start[1] += $diff[1] / 100;
      $points[] = $start[0] . ' ' . $start[1];
    }
    return implode(", ", $points);
  }

  /**
   * {@inheritdoc}
   */
  public function WktGenerateLinestring($start = NULL, $segments = NULL) {
    return $this->buildWkt(GEOFIELD_TYPE_LINESTRING, $this->generateLinestring($start, $segments));
  }

  /**
   * Generates a multilinestring coordinates.
   *
   * @return string
   *   The structured multilinestring coordinates.
   */
  protected function generateMultilinestring() {
    $start = $this->randomPoint();
    $num = $this->DdGenerate(1, 3, TRUE);
    $lines[] = $this->generateLinestring($start);
    for ($i = 0; $i < $num; $i += 1) {
      $diff = $this->randomPoint();
      $start[0] += $diff[0] / 100;
      $start[1] += $diff[1] / 100;
      $lines[] = $this->generateLinestring($start);
    }
    return $this->buildMultiCoordinates($lines);
  }

  /**
   * {@inheritdoc}
   */
  public function WktGenerateMultilinestring() {
    return $this->buildWkt(GEOFIELD_TYPE_MULTILINESTRING, $this->generateMultilinestring());
  }

  /**
   * Generates a polygon coordinates.
   *
   * @param array $start
   *   The starting point. If not provided, will be randomly generated.
   * @param int $segments
   *   Number of segments. If not provided, will be randomly generated.
   *
   * @return string
   *   The structured polygon coordinates.
   */
  protected function generatePolygon($start = NULL, $segments = NULL) {
    $start = $start ? $start : $this->randomPoint();
    $segments = $segments ? $segments : $this->DdGenerate(2, 4, TRUE);
    $poly = $this->generateLinestring($start, $segments);
    // Close the polygon.
    return '(' . $poly . ', ' . $start[0] . ' ' . $start[1] . ')';
  }

  /**
   * {@inheritdoc}
   */
  public function WktGeneratePolygon($start = NULL, $segments = NULL) {
    return $this->buildWkt(GEOFIELD_TYPE_POLYGON, $this->generatePolygon($start, $segments));
  }

  /**
   * Generates a multipolygon coordinates.
   *
   * @return string
   *   The structured multipolygon coordinates.
   */
  protected function generateMultipolygon() {
    $start = $this->randomPoint();
    $num = $this->DdGenerate(1, 5, TRUE);
    $segments = $this->DdGenerate(2, 3, TRUE);
    $poly[] = $this->generatePolygon($start, $segments);
    for ($i = 0; $i < $num; $i += 1) {
      $diff = $this->randomPoint();
      $start[0] += $diff[0] / 100;
      $start[1] += $diff[1] / 100;
      $poly[] = $this->generatePolygon($start, $segments);
    }
    return $this->buildMultiCoordinates($poly);
  }

  /**
   * {@inheritdoc}
   */
  public function WktGenerateMultipolygon() {
    return $this->buildWkt(GEOFIELD_TYPE_MULTIPOLYGON, $this->generateMultipolygon());
  }
}
