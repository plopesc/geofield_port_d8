<?php

/**
 * @file
 * Contains \Drupal\geofield\Tests\WKTGeneratorTest.
 */

namespace Drupal\geofield\Tests;

use Drupal\geofield\WKTGenerator;
use Drupal\Tests\UnitTestCase;

// @TODO: Remove once Core's PHPUnit's autoloader gets it act together. :-/
require_once(__DIR__ . '/../../../../lib/Drupal/geofield/WKTGenerator.php');

/**
 * Tests WKTGenerator.
 *
 * @group Geofield
 */
class WKTGeneratorTest extends UnitTestCase {

  public static function getInfo() {
    return array(
      'name' => 'WKTGenerator Tests',
      'description' => 'Unit tests for WKTGenerator class.',
      'group' => 'Geofield'
    );
  }

  /**
   * Tests the method for checking if the access check applies to a route.
   */
  public function testBasic() {
    $generator = new WKTGenerator();
    $this->assertEquals(TRUE, TRUE);
  }
}
