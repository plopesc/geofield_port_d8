<?php

/**
 * @file
 * Definition of Drupal\geofield_map\Plugin\views\style\GeofieldMap.
 */

namespace Drupal\geofield_map\Plugin\views\style;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render a View output as a map.
 *
 * @ingroup views_style_plugins
 *
 * Attributes set below end up in the $this->definition[] array.
 *
 * @Plugin(
 *   id = "geofield_map",
 *   title = @Translation("Geofield map"),
 *   help = @Translation("Displays a View containing Geofields as a map."),
 *   type = "normal",
 *   theme = "geofield_map_map",
 *   even_empty = TRUE
 * )
 */
class GeofieldMap extends StylePluginBase {

  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {

    // Set these before calling parent::init() as it uses these.
    $this->definition['even empty'] = TRUE; // cannot have space in annotation, so doing it here
    $this->usesOptions = TRUE;
    $this->usesRowPlugin = FALSE;
    $this->usesRowClass = FALSE;
    $this->usesGrouping = FALSE;
    $this->usesFields = TRUE;

    parent::init($view, $display, $options);
  }

  /**
   * Set default options
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['data_source'] = array('default' => '');
    $options['popup_source'] = array('default' => '');
    $options['alt_text'] = array('default' => '');
    $options['geofield_map_width'] = array('default' => '100%');
    $options['geofield_map_height'] = array('default' => '300px');
    $options['geofield_map_zoom'] = array('default' => '8');
    $options['geofield_map_controltype'] = array('default' => 'default');
    $options['geofield_map_mtc'] = array('default' => 'standard');
    $options['geofield_map_pancontrol'] = array('default' => 1);
    $options['geofield_map_maptype'] = array('default' => 'map');
    $options['geofield_map_baselayers_map'] = array('default' => 1);
    $options['geofield_map_baselayers_satellite'] = array('default' => 1);
    $options['geofield_map_baselayers_hybrid'] = array('default' => 1);
    $options['geofield_map_baselayers_physical'] = array('default' => 0);
    $options['geofield_map_scale'] = array('default' => 0);
    $options['geofield_map_overview'] = array('default' => 0);
    $options['geofield_map_overview_opened'] = array('default' => 0);
    $options['geofield_map_scrollwheel'] = array('default' => 0);
    $options['geofield_map_draggable'] = array('default' => 0);
    $options['geofield_map_streetview_show'] = array('default' => 0);
    $options['icon'] = array('default' => '');
    return $options;
  }

  /**
   * Options form
   */
  public function buildOptionsForm(&$form, &$form_state) {
    parent::buildOptionsForm($form, $form_state);

    $handlers = $this->displayHandler->getHandlers('field');
    $data_source_options = $popup_source_options = array('' => '<none>');

    foreach ($handlers as $handle) {
      $popup_source_options[$handle->options['id']] = (!empty($handle->options['label'])) ? $handle->options['label'] : $handle->options['id'];

      if (!empty($handle->field_info['type']) && $handle->field_info['type'] == 'geofield') {
        $data_source_options[$handle->options['id']] = (!empty($handle->options['label'])) ? $handle->options['label'] : $handle->options['id'];
      }
    }

    if (count($data_source_options) == 1) {
      $form['error'] = array(
        '#markup' => 'Please add at least 1 geofield to the view',
      );
    }
    else {
      // Map Preset
      $form['data_source'] = array(
        '#type' => 'select',
        '#title' => t('Data Source'),
        '#description' => t('Which field contains geodata?'),
        '#options' => $data_source_options,
        '#default_value' => $this->options['data_source'] ? $this->options['data_source'] : '',
      );

      $form['popup_source'] = array(
        '#type' => 'select',
        '#title' => t('Popup Text'),
        '#options' => $popup_source_options,
        '#default_value' => $this->options['popup_source'] ? $this->options['popup_source'] : '',
      );

      $form['alt_text'] = array(
        '#type' => 'textarea',
        '#title' => t('Alternate Text'),
        '#description' => t('This text shows up when a user does not have javascript enabled'),
        '#default_value' => $this->options['alt_text'] ? $this->options['alt_text'] : '',
      );

      $form = geofield_map_settings_form($this->options, $form);
    }
  }

  /**
   * Renders the View as a map.
   */
  public function render() {
    geophp_load();
    if (empty($this->view->style_plugin->options['data_source'])) {
      drupal_set_message(t('A <strong>Data source</strong> must be selected in the Geofield map settings.'), 'error');
      return '';
    }
    $style_options = $this->view->style_plugin->options;

    $geo_data = (!empty($style_options['data_source'])) ? 'field_' . $style_options['data_source']: NULL;
    $popup_data = (!empty($style_options['popup_source'])) ? $style_options['popup_source'] : NULL;
    $geofield_handler = $this->view->field[$style_options['data_source']];

    $map_data = array();
    if ($geo_data) {

      $this->renderFields($this->view->result);

      foreach ($this->view->result as $id => $result) {

        $geofield = $geofield_handler->getValue($result);

        // RdB: @todo: the above call returns empty, as the Geofield values
        // aren't in $this->view->result[$id], for some reason. So using
        // $this->rendered_fields[$id] instead.
        if (empty($geofield)) {
          $geom = $this->rendered_fields[$id][$geofield_handler->field];
          $coords = explode('<br/>', $geom);
          // If we have the Latitude/Longitude format, which is rendered as
          // "Latitude: y<br/>Longitude: x", convert it to WKT.
          // All other formats are handled fine by geoPHP::load(), below.
          if (count($coords) == 2) {
            $lat = str_replace('Latitude: ', '',  $coords[0]);
            $lon = str_replace('Longitude: ', '',  $coords[1]);
            $geom = "POINT ($lon $lat)";
          }
        }
        else {
          $geom = $geofield[0]['geom'];
        }
        $geometry = \geoPHP::load($geom);
        if ($geometry) {
          $datum = json_decode($geometry->out('json'));
          $datum->properties = array(
            'description' => $popup_data ? $this->rendered_fields[$id][$popup_data] : '',
          );
          $map_data[] = $datum;
        }
      }

      if (count($map_data) == 1) {
        $map_data = $map_data[0];
      }
      elseif (count($map_data) > 1) {
        $tmp = $map_data;
        $map_data = array(
          'type' => 'GeometryCollection',
          'geometries' => $tmp,
        );
      }

      $map_settings = geofield_map_settings_do($style_options);
      
      $view_name = $this->view->storage->get('label');
      $container_id = drupal_html_id($view_name . '_' . $this->view->current_display);

      $js_settings = array(
        $container_id => array(
          'map_id' => $container_id,
          'map_settings' => $map_settings,
          'data' => $map_data,
        ),
      );

      drupal_add_js(array('geofieldMap' => $js_settings), 'setting');
    }
    else {
      $container_id = 'no-data';
    }

    drupal_add_js('//maps.googleapis.com/maps/api/js?sensor=false', 'external');
    drupal_add_js(drupal_get_path('module', 'geofield_map') . '/js/GeoJSON.js');
    drupal_add_js(drupal_get_path('module', 'geofield_map') . '/js/geofield_map.js');

    // Defaults
    $width = '100%';
    $height = '300px';
    if ($style_options['geofield_map_width']) {
      $width = $style_options['geofield_map_width'];
    }
    if ($style_options['geofield_map_height']) {
      $height = $style_options['geofield_map_height'];
    }

    return '<div style="width: ' . $width . '; height: ' . $height . '" id="' . $container_id . '" class="geofieldMap">' . $style_options['alt_text'] . '</div>';
  }
}