<?php

use Drupal\field\FieldStorageConfigInterface;

/**
 * @file
 * Hooks for Views integration.
 *
 * In addition to the usual views hooks, Geofield defines a set of classes that allows
 * us to break out the code needed to handle the wide variety of proximity searches 
 * the module supports. We define api functions here to allow for very basic autoloading.
 * 
 * Our mini-plugin system we're using here is not designed for external (non-geofield) use,
 * but as a programming convenience for the maintainers.
 */

/**
 * Implements hook_field_views_data().
 *
 * Views integration for geofields.
 *
 */
function geofield_field_views_data(FieldStorageConfigInterface $field_storage) {
  $data = field_views_field_default_views_data($field_storage);
  $field_name = $field_storage->field_name;

  foreach ($data as $table_name => $table_data) {
    if (isset($table_data[$field_name])) {
      $group_name = $table_data[$field_name]['group'];
      $data[$table_name]['field_geofield_distance'] = array(
        'group' => $group_name,
        'title'       => $table_data[$field_name]['title'] . ' - proximity',
        'title short' => $table_data[$field_name]['title'] . ' - proximity',
        'help' => $table_data[$field_name]['help'],
        'sort' => array(
          'field' => 'field_geofield_distance',
          'table' => $table_name,
          'handler' => 'geofield_handler_sort',
          'field_name' => $field_name,
          'real_field' => $table_name,
          'id' => 'geofield_proximity',
        ),
        'field' => array(
          'field' => 'field_geofield_distance',
          'table' => $table_name,
          'handler' => 'geofield_handler_field',
          'field_name' => $field_name,
          'real_field' => $table_name,
          'float' => TRUE,
          'click sortable' => TRUE,
          'id' => 'geofield_proximity',
        ),
        'filter' => array(
          'field' => 'field_geofield_distance',
          'table' => $table_name,
          'handler' => 'geofield_handler_filter',
          'field_name' => $field_name,
          'real_field' => $table_name,
          'id' => 'geofield_proximity',
        ),
      );
    }
  }
  return $data;
}
