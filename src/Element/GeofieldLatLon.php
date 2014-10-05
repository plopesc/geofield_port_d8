<?php

/**
 * @file
 * Contains \Drupal\geofield\Element\GeofieldLatLon.
 */

namespace Drupal\geofield\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a Geofield Lat Lon form element.
 *
 * @FormElement("geofield_latlon")
 */
class GeofieldLatLon extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#process' => array(
        array($class, 'latlonProcess'),
      ),
      '#element_validate' => array(
        array($class, 'latlonValidate'),
      ),
      '#theme_wrappers' => array('fieldset', 'form_element'),
    );
  }

  /**
   * Generates the Geofield Lat Lon form element..
   *
   * @param array $element
   *   An associative array containing the properties and children of the
   *   element. Note that $element must be taken by reference here, so processed
   *   child elements are taken over into $form_state.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The processed element.
   */
  public static function latlonProcess(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['#tree'] = TRUE;
    $element['#input'] = TRUE;
    $element['lat'] = array(
      '#type' => 'textfield',
      '#title' => t('Latitude'),
      '#required' => (!empty($element['#required'])) ? $element['#required'] : FALSE,
      '#default_value' => (!empty($element['#default_value']['lat'])) ? $element['#default_value']['lat'] : '',
      '#attributes' => array(
        'class' => array('geofield-lat'),
      ),
    );

    $element['lon'] = array(
      '#type' => 'textfield',
      '#title' => t('Longitude'),
      '#required' => (!empty($element['#required'])) ? $element['#required'] : FALSE,
      '#default_value' => (!empty($element['#default_value']['lon'])) ? $element['#default_value']['lon'] : '',
      '#attributes' => array(
        'class' => array('geofield-lon'),
      ),
    );

    unset($element['#value']);
    // Set this to false always to prevent notices.
    $element['#required'] = FALSE;

    if (!empty($element['#geolocation']) && $element['#geolocation'] == TRUE) {
      $element['#attached']['js'][] = drupal_get_path('module', 'geofield') . '/js/geolocation.js';
      $element['geocode'] = array(
        '#type' => 'button',
        '#value' => t('Find my location'),
        '#name' => 'geofield-html5-geocode-button',
      );
      $element['#attributes']['class'] = array('auto-geocode');
    }

    return $element;
  }

  /**
   * Validation callback for a Geofield Lat Lon element.
   *
   * @param array $element
   *   The element being processed.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   */
  function latlonValidate(&$element, FormStateInterface $form_state, &$complete_form) {
    $components = array(
      'lat' => array(
        'title' => 'Latitude',
        'range' => 90,
      ),
      'lon' => array(
        'title' => 'Longitude',
        'range' => 180,
      ),
    );

    $allFilled = TRUE;
    $anyFilled = FALSE;
    $error_label = isset($element['#error_label']) ? $element['#error_label'] : $element['#title'];
    foreach ($components as $key => $component) {
      if (!empty($element[$key]['#value'])) {
        if (!is_numeric($element[$key]['#value'])) {
          $form_state->setError($element[$key], t('@title: @component_title is not numeric.', array('@title' => $error_label, '@component_title' => $component['title'])));
        }
        elseif (abs($element[$key]['#value']) > $component['range']) {
          $form_state->setError($element[$key], t('@title: @component_title is out of bounds.', array('@title' => $error_label, '@component_title' => $component['title'])));
        }
      }
      if ($element[$key]['#value'] == '') {
        $allFilled = FALSE;
      }
      else {
        $anyFilled = TRUE;
      }
    }
    if ($anyFilled && !$allFilled) {
      foreach ($components as $key => $component) {
        if ($element[$key]['#value'] == '') {
          $form_state->setError($element[$key], t('@title: @component_title must be filled too.', array('@title' => $error_label, '@component_title' => $component['title'])));
        }
      }
    }
  }

}
