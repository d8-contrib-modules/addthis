<?php
/**
 * @file
 * Contains \Drupal\addthis_block\Plugin\Block\AddThisBlock.
 */

namespace Drupal\addthis_block\Plugin\Block;

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
      'share_services' => 'facebook,twitter',
      'buttons_size' => 'addthis_16x16_style',
      'counter_orientation' => 'horizontal',
      'extra_css' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build()
  {

    $config = $this->configuration;
    $element = array(
      '#type' => 'addthis_wrapper',
      '#tag' => 'div',
      '#attributes' => array(
        'class' => array(
          'addthis_toolbox',
          'addthis_default_style',
          ($config['buttons_size'] == AddThis::CSS_32x32 ? AddThis::CSS_32x32 : NULL),
          $config['extra_css'],
        ),
      ),
    );

    // Add the widget script.
    $script_manager = AddThisScriptManager::getInstance();
    $script_manager->attachJsToElement($element);


    $services = trim($config['share_services']);
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
      $orientation = ($config['counter_orientation'] == 'horizontal' ? TRUE : FALSE);
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

    $settings = $this->getConfiguration();
    $elements = AddThis::getInstance()->getBasicToolboxForm($settings);

    $form['settings']['addthis_settings'] += $elements;

    return $form;
  }
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['share_services'] = $form_state->getValue(['settings', 'addthis_settings', 'share_services']);
    $this->configuration['buttons_size'] = $form_state->getValue(['settings', 'addthis_settings', 'buttons_size']);
    $this->configuration['counter_orientation'] = $form_state->getValue(['settings', 'addthis_settings', 'counter_orientation']);
    $this->configuration['extra_css'] = $form_state->getValue(['settings', 'addthis_settings', 'extra_css']);

  }



}

?>