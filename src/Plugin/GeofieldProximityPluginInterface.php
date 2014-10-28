<?php

/**
 * @file
 *   Contains \Drupal\geofield\Plugin\GeofieldProximityPluginInterface.
 */

namespace Drupal\geofield\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\ViewsHandlerInterface;

/**
 * Defines an interface for geofield proximity plugins.
 *
 * Modules implementing this interface may want to extend GeofieldProximityBase
 * class, which provides default implementations of each method.
 *
 * All methods in GeofieldProximityPluginInterface maps directly to a
 * method in a views_handler class, except for 'getSourceValue,' which
 * is primarily called in the 'query' method, but also in other instances.
 *
 * @see \Drupal\geofield\Annotation\GeofieldProximity
 * @see \Drupal\geofield\Plugin\GeofieldProximityBase
 * @see \Drupal\geofield\Plugin\GeofieldProximityManager
 * @see plugin_api
 */
interface GeofieldProximityPluginInterface extends PluginInspectionInterface {

  /**
   * Information about options for all kinds of purposes will be held here.
   * @code
   * 'option_name' => array(
   *  - 'default' => default value,
   *  - 'contains' => (optional) array of items this contains, with its own
   *      defaults, etc. If contains is set, the default will be ignored and
   *      assumed to be array().
   *  ),
   * @endcode
   *
   * @param array $options
   *   Array containing already defined options.
   * @param ViewsHandlerInterface $views_plugin
   *   The Views handler related to this plugin.
   */
  public function defineOptions(array &$options, ViewsHandlerInterface $views_plugin);

  /**
   * Provide a form to edit options for this plugin.
   *
   * @param array $form
   *   The whole form array.
   * @param FormStateInterface $form_state
   *   The current form state.
   * @param ViewsHandlerInterface $views_plugin
   *   The Views handler related to this plugin.
   */
  public function buildOptionsForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin);

  /**
   * Validates the options for this plugin.
   *
   * @param array $form
   *   The whole form array.
   * @param FormStateInterface $form_state
   *   The current form state.
   * @param ViewsHandlerInterface $views_plugin
   *   The Views handler related to this plugin.
   */
  public function validateOptionsForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin);

  /**
   * Options form subform for setting options.
   *
   * @param array $form
   *   The whole form array.
   * @param FormStateInterface $form_state
   *   The current form state.
   * @param ViewsHandlerInterface $views_plugin
   *   The Views handler related to this plugin.
   */
  public function valueForm(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin);

  /**
   * Validates options form subform for setting options.
   *
   *  @param array $form
   *   The whole form array.
   * @param FormStateInterface $form_state
   *   The current form state.
   * @param ViewsHandlerInterface $views_plugin
   *   The Views handler related to this plugin.
   */
  public function valueValidate(array &$form, FormStateInterface &$form_state, ViewsHandlerInterface $views_plugin);

  /**
   * Retrieves source values necessary for the query.
   *
   * @param ViewsHandlerInterface $views_plugin
   * @return mixed
   */
  public function getSourceValue(ViewsHandlerInterface $views_plugin);
}
