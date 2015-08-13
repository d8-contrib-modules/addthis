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


    $form['settings']['addthis_settings']['share_services'] = array(
      '#title' => t('Services'),
      '#type' => 'textfield',
      '#size' => 80,
      '#default_value' => $this->configuration['share_services'],
      '#required' => TRUE,
      '#element_validate' => array($this, 'addThisDisplayElementServicesValidate'),
      '#description' =>
        t('Specify the names of the sharing services and seperate them with a , (comma). <a href="http://www.addthis.com/services/list" target="_blank">The names on this list are valid.</a>') .
        t('Elements that are available but not ont the services list are (!services).',
          array('!services' => 'bubble_style, pill_style, tweet, facebook_send, twitter_follow_native, google_plusone, stumbleupon_badge, counter_* (several supported services), linkedin_counter')
        ),
    );
    $form['settings']['addthis_settings']['buttons_size'] = array(
      '#title' => t('Buttons size'),
      '#type' => 'select',
      '#default_value' => $this->configuration['buttons_size'],
      '#options' => array(
        'addthis_16x16_style' => t('Small (16x16)'),
        'addthis_32x32_style' => t('Big (32x32)'),
      ),
    );
    $form['settings']['addthis_settings']['counter_orientation'] = array(
      '#title' => t('Counter orientation'),
      '#description' => t('Specify the way service counters are oriented.'),
      '#type' => 'select',
      '#default_value' => $this->configuration['counter_orientation'],
      '#options' => array(
        'horizontal' => t('Horizontal'),
        'vertical' => t('Vertical'),
      )
    );
    $form['settings']['addthis_settings']['extra_css'] = array(
      '#title' => t('Extra CSS declaration'),
      '#type' => 'textfield',
      '#size' => 40,
      '#default_value' => $this->configuration['extra_css'],
      '#description' => t('Specify extra CSS classes to apply to the toolbox'),
    );
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