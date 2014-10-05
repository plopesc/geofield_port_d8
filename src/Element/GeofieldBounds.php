<?php

/**
 * @file
 * Contains \Drupal\geofield\Element\GeofieldBounds.
 */

namespace Drupal\geofield\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElement;

/**
 * Provides a Geofield bounds form element.
 *
 * @FormElement("geofield_bounds")
 */
class GeofieldBounds extends FormElement {

  /**
   * {@inheritdoc}
   */

  public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#process' => array(
        array($class, 'boundsProcess'),
      ),
      '#element_validate' => array(
        array($class, 'boundsValidate')
      ),
      '#theme' => 'geofield_bounds',
      '#theme_wrappers' => array('fieldset'),
    );
  }

  /**
   * Generates the Geofield bounds form element..
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
  public static function boundsProcess(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['#tree'] = TRUE;
    $element['top'] = array(
      '#type' => 'textfield',
      '#title' => t('Top'),
      '#required' => (!empty($element['#required'])) ? $element['#required'] : FALSE,
      '#default_value' => (!empty($element['#default_value']['top'])) ? $element['#default_value']['top'] : '',
      '#attributes' => array(
        'class' => array('geofield-top'),
      ),
    );

    $element['right'] = array(
      '#type' => 'textfield',
      '#title' => t('Right'),
      '#required' => (!empty($element['#required'])) ? $element['#required'] : FALSE,
      '#default_value' => (!empty($element['#default_value']['right'])) ? $element['#default_value']['right'] : '',
      '#attributes' => array(
        'class' => array('geofield-right'),
      ),
    );

    $element['bottom'] = array(
      '#type' => 'textfield',
      '#title' => t('Bottom'),
      '#required' => (!empty($element['#required'])) ? $element['#required'] : FALSE,
      '#default_value' => (!empty($element['#default_value']['bottom'])) ? $element['#default_value']['bottom'] : '',
      '#attributes' => array(
        'class' => array('geofield-bottom'),
      ),
    );

    $element['left'] = array(
      '#type' => 'textfield',
      '#title' => t('Left'),
      '#required' => (!empty($element['#required'])) ? $element['#required'] : FALSE,
      '#default_value' => (!empty($element['#default_value']['left'])) ? $element['#default_value']['left'] : '',
      '#attributes' => array(
        'class' => array('geofield-left'),
      ),
    );

    unset($element['#value']);
    // Set this to false always to prevent notices.
    $element['#required'] = FALSE;

    return $element;
  }

  /**
   * Validation callback for a Geofield bounds element.
   *
   * @param array $element
   *   The element being processed.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   */
  function boundsValidate(&$element, FormStateInterface $form_state, &$complete_form) {
    $components = array(
      'top' => array(
        'title' => 'Top',
        'range' => 90,
      ),
      'right' => array(
        'title' => 'Right',
        'range' => 180,
      ),
      'bottom' => array(
        'title' => 'Bottom',
        'range' => 90,
      ),
      'left' => array(
        'title' => 'Left',
        'range' => 180,
      ),
    );

    $allFilled = TRUE;
    $anyFilled = FALSE;
    foreach ($components as $key => $component) {
      if (!empty($element[$key]['#value'])) {
        if (!is_numeric($element[$key]['#value'])) {
          $form_state->setError($element[$key], t('@title: @component_title is not numeric.', array('@title' => $element['#title'], '@component_title' => $component['title'])));
        }
        elseif (abs($element[$key]['#value']) > $component['range']) {
          $form_state->setError($element[$key], t('@title: @component_title is out of bounds.', array('@title' => $element['#title'], '@component_title' => $component['title'])));
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
          $form_state->setError($element[$key], t('@title: @component_title must be filled too.', array('@title' => $element['#title'], '@component_title' => $component['title'])));
        }
      }
    }
  }

}
