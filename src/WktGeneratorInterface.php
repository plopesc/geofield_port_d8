<?php

/**
 * @file
 * Contains \Drupal\geofield\WktGeneratorInterface.
 */

namespace Drupal\geofield;

interface WktGeneratorInterface {

  /**
   * Returns a WKT format multipoint feature.
   *
   * @return string
   *   The WKT multipoint feature.
   */
  public function WktGenerateMultipoint();

  /**
   * Returns a WKT format multilinestring feature.
   *
   * @return string
   *   The WKT multilinestring feature.
   */
  public function WktGenerateMultilinestring();

  /**
   * Returns a WKT format polygon feature.
   *
   * @param array $start
   *   The starting point. If not provided, will be randomly generated.
   * @param int $segments
   *   Number of segments. If not provided, will be randomly generated.
   *
   * @return string
   *   The WKT polygon feature.
   */
  public function WktGeneratePolygon($start = NULL, $segments = NULL);

  /**
   * Returns a WKT format point feature.
   *
   * @param array $point
   *   A Lon Lat array. By default create a random pair.
   *
   * @return string
   *   The WKT point feature.
   */
  public function WktGeneratePoint($point = NULL);

  /**
   * Returns a WKT format linestring feature.
   *
   * @param array $start
   *   The starting point. If not provided, will be randomly generated.
   * @param int $segments
   *   Number of segments. If not provided, will be randomly generated.
   *
   * @return string
   *   The WKT linestring feature.
   */
  public function WktGenerateLinestring($start = NULL, $segments = NULL);

  /**
   * Helper to generate a random WKT string
   *
   * Try to keeps values sane, no shape is more than 100km across
   *
   * @return string
   *   The random WKT value.
   */
  public function WktGenerateGeometry();

  /**
   * Returns a WKT format multipolygon feature.
   *
   * @return string
   *   The WKT multipolygon feature.
   */
  public function WktGenerateMultipolygon();
}