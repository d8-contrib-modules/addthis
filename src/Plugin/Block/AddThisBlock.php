<?php
/**
 * @file
 * Contains \Drupal\addthis\Plugin\Block\AddThisBlock.
 */

namespace Drupal\addthis\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\addthis\AddThis;
use Drupal\addthis\Services\AddThisScriptManager;

/**
 * Provides my custom block.
 *
 * @Block(
 *   id = "addthis_block",
 *   admin_label = @Translation("AddThis"),
 *   category = @Translation("Blocks")
 * )
 */
class AddThisBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'type' => 'addthis_disabled',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $element = array(
      '#type' => 'addthis_wrapper',
      '#tag' => 'a',
      '#attributes' => array(
        'class' => array(
          'addthis_button',
        ),
      ),
    );


    // Add the widget script.
    $script_manager = AddThisScriptManager::getInstance();
    $script_manager->attachJsToElement($element);


    $widget_type = $this->configuration['type'];
    $widget_settings = AddThis::getInstance()->getBlockDisplaySettings($widget_type);

    $services = trim($widget_settings['share_services']);
    $services = str_replace(' ', '', $services);
    $services = explode(',', $services);
    $items = array();

    // All service elements
    $items = array();
    foreach ($services as $service) {
      $items[$service] = array(
        '#type' => 'addthis_element',
        '#tag' => 'a',
        '#value' => '',
        '#attributes' => array(
          'href' => AddThis::getInstance()->getBaseBookmarkUrl(),
          'class' => array(
            'addthis_button_' . $service,
          ),
        ),
        '#addthis_service' => $service,
      );

      // Add individual counters.
      if (strpos($service, 'counter_') === 0) {
        $items[$service]['#attributes']['class'] = array("addthis_$service");
      }

      // Basic implementations of bubble counter orientation.
      // @todo Figure all the bubbles out and add them.
      //   Still missing: tweetme, hyves and stubleupon, google_plusone_badge.
      //
      $orientation = ($widget_settings['counter_orientation'] == 'horizontal' ? TRUE : FALSE);
      switch ($service) {
        case 'linkedin_counter':
          $items[$service]['#attributes'] += array(
            'li:counter' => ($orientation ? '' : 'top'),
          );
          break;
        case 'facebook_like':
          $items[$service]['#attributes'] += array(
            'fb:like:layout' => ($orientation ? 'button_count' : 'box_count')
          );
          break;
        case 'facebook_share':
          $items[$service]['#attributes'] += array(
            'fb:share:layout' => ($orientation ? 'button_count' : 'box_count')
          );
          break;
        case 'google_plusone':
          $items[$service]['#attributes'] += array(
            'g:plusone:size' => ($orientation ? 'standard' : 'tall')
          );
          break;
        case 'tweet':
          $items[$service]['#attributes'] += array(
            'tw:count' => ($orientation ? 'horizontal' : 'vertical'),
            'tw:via' => AddThis::getInstance()->getTwitterVia(),
          );
          break;
        case 'bubble_style':
          $items[$service]['#attributes']['class'] = array(
            'addthis_counter', 'addthis_bubble_style'
          );
          break;
        case 'pill_style':
          $items[$service]['#attributes']['class'] = array(
            'addthis_counter', 'addthis_pill_style'
          );
          break;
      }
    }

    $element += $items;

    $markup = render($element);
    return array(
      '#markup' => $markup
    );

  }

  function blockForm($form, FormStateInterface $form_state) {
    //@TODO: Implement block form.
    $form['settings']['addthis_settings'] = array(
      '#type' => 'fieldset',
      '#title' => 'Display settings',
    );

    // Retrieve settings.
    $widget_type = $this->configuration['type'];
    //$addthis_settings = AddThis::getInstance()->getBlockDisplaySettings();


    // The list of formatters.
    $formatter_options = AddThis::getInstance()->getDisplayTypes();

    $form['settings']['addthis_settings']['type'] = array(
      '#type' => 'select',
      '#title' => t('Formatter for @title', array('@title' => 'AddThis block')),
      '#title_display' => 'invisible',
      '#options' => $formatter_options,
      '#default_value' => $this->configuration['type'],
      '#attributes' => array('class' => array('addthis-display-type')),
    );
    return $form;
  }
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['type'] = $form_state->getValue(['settings', 'addthis_settings', 'type']);
  }



}

?>