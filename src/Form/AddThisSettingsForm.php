<?php
/**
 * @file
 * Contains \Drupal\addthis\Form\AddThisSettingsForm.
 */

namespace Drupal\addthis\Form;

use Drupal\addthis\AddThisScriptManager;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Xss;

/**
 * Defines a form to configure maintenance settings for this site.
 */
class AddThisSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'addthis_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['addthis.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('addthis.settings');
    $add_this_script_manager = \Drupal::service('addthis.script_manager');


    //Add our library to the settings form to add in custom CSS.
    $form['#attached']['library'][] = 'addthis/addthis.admin';

    // Visual settings.
    $form['compact_menu'] = array(
      '#type' => 'details',
      '#title' => t('Compact menu'),
      '#open' => TRUE,
      '#description' => '<p>' . t('Configure the global behavior and style of the compact menu and some additional settings related to the interface.') . '</p>',
      '#description_display' => 'before'
    );
    $form['compact_menu']['menu_style'] = array(
      '#type' => 'details',
      '#title' => t('Style'),
      '#open' => FALSE,
    );
    $form['compact_menu']['menu_style']['addthis_co_brand'] = array(
      '#type' => 'textfield',
      '#title' => t('Branding text'),
      '#description' => t('Additional branding message to be rendered in the upper-right-hand corner of the compact menus.<br />Should be less than 15 characters in most cases to render properly.'),
      '#default_value' => $config->get('compact_menu.menu_style.addthis_co_brand'),
      '#required' => FALSE,
      '#maxlength' => 15,
    );
    $form['compact_menu']['menu_style']['addthis_ui_header_color'] = array(
      '#type' => 'textfield',
      '#title' => t('Header text color'),
      '#default_value' => $config->get('compact_menu.menu_style.addthis_ui_header_color'),
      '#description' => t('Something like \'#FFFFFF\'.'),
      '#size' => 8,
      '#maxlength' => 7,
      '#required' => FALSE,
    );
    $form['compact_menu']['menu_style']['addthis_ui_header_background_color'] = array(
      '#type' => 'textfield',
      '#title' => t('Header background color'),
      '#default_value' => $config->get('compact_menu.menu_style.addthis_ui_header_background_color'),
      '#description' => t('Something like \'#000000\'.'),
      '#size' => 8,
      '#maxlength' => 7,
      '#required' => FALSE,
    );
    $form['compact_menu']['menu_style']['addthis_click_to_open_compact_menu_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Open compact menu on click'),
      '#description' => t('Default behavior is to open compact menu on hover.'),
      '#default_value' => $config->get('compact_menu.menu_style.addthis_click_to_open_compact_menu_enabled'),
      '#required' => FALSE,
    );
    $form['compact_menu']['menu_style']['addthis_open_windows_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use pop-up windows'),
      '#description' => t('If checked, all shares will open in a new pop-up window instead of a new tab or regular browser window.'),
      '#default_value' => $config->get('compact_menu.menu_style.addthis_open_windows_enabled'),
      '#required' => FALSE,
    );
    $form['compact_menu']['menu_style']['addthis_ui_delay'] = array(
      '#type' => 'textfield',
      '#title' => t('Menu open delay'),
      '#description' => t('Delay, in milliseconds, before compact menu appears when mousing over a regular button. Capped at 500 ms.'),
      '#default_value' => $config->get('compact_menu.menu_style.addthis_ui_delay'),
      '#required' => FALSE,
      '#size' => 3,
      '#maxlength' => 3,
    );

    // Enabled services settings.
    $form['compact_menu']['enabled_services'] = array(
      '#type' => 'details',
      '#title' => t('Compact menu enabled services'),
      '#description' => t('The sharing services you select here will be displayed in the compact menu. If you select no services, AddThis will provide a list of frequently used services. This list is updated regularly. <b>Notice that this setting does not define what services should be display in a toolbox.</b>'),
      '#description_display' => 'before',
      '#open' => FALSE,
    );

    $form['compact_menu']['enabled_services']['addthis_enabled_services'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Enabled services'),
      '#options' => $add_this_script_manager->getServices(),
      '#default_value' => $config->get('compact_menu.enabled_services.addthis_enabled_services'),
      '#required' => FALSE,
      '#columns' => 3,
    );

    // Additional visual settings.
    $form['compact_menu']['additionals'] = array(
      '#type' => 'details',
      '#title' => t('Additional configuration'),
      '#open' => FALSE,
    );
    $form['compact_menu']['additionals']['addthis_standard_css_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use standard AddThis stylesheet'),
      '#description' => t('If not checked, AddThis will not load standard CSS file, allowing you to style everything yourself without incurring the cost of an additional load.'),
      '#default_value' => $config->get('compact_menu.additionals.addthis_standard_css_enabled'),
      '#required' => FALSE,
    );
    $form['compact_menu']['additionals']['addthis_508_compliant'] = array(
      '#type' => 'checkbox',
      '#title' => t('508 compliant'),
      '#description' => 'If checked, clicking the AddThis button will open a new window to a page that is keyboard navigable.',
      '#default_value' => $config->get('compact_menu.additionals.addthis_508_compliant'),
      '#required' => FALSE,
    );
    $form['compact_menu']['additionals']['addthis_addressbook_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use addressbook'),
      '#description' => 'If checked, the user will be able import their contacts from popular webmail services when using AddThis\'s email sharing.',
      '#default_value' => $config->get('compact_menu.additionals.addthis_addressbook_enabled'),
      '#required' => FALSE,
    );

    // Excluded Services.
    $form['excluded_services'] = array(
      '#type' => 'details',
      '#title' => t('Excluded services'),
      '#description' => t('The sharing services you select here will be excluded from all AddThis menus. This applies globally.'),
      '#description_display' => 'before',
      '#open' => FALSE,
    );
    $form['excluded_services']['addthis_excluded_services'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Excluded services'),
      '#options' => $add_this_script_manager->getServices(),
      '#default_value' => $config->get('excluded_services.addthis_excluded_services'),
      '#required' => FALSE,
      '#columns' => 3,
    );

    //Analytics settings.
    $profile_id = $config->get('analytics.addthis_profile_id');
    $can_track_clicks = empty($profile_id) ? FALSE : TRUE;
    $form['analytics'] = array(
      '#type' => 'details',
      '#title' => t('Analytics and Tracking'),
      '#open' => $can_track_clicks ? TRUE : FALSE,
    );

    if (!$can_track_clicks) {
      $form['analytics']['can_track_notice'] = array(
        '#theme' => 'html_tag',
        '#tag' => 'div',
        '#value' => t('For click analytics and statistics you have to provide a ProfileID from <a href="http://www.addthis.com">AddThis.com</a>. Register <a href="https://www.addthis.com/register" targt="_blank">here</a>.'),
        '#attributes' => array('class' => array('messages', 'warning')),
      );
    }

    $form['analytics']['addthis_profile_id'] = array(
      '#type' => 'textfield',
      '#title' => t('AddThis ProfileID'),
      '#default_value' => $config->get('analytics.addthis_profile_id'),
      '#required' => FALSE,
      '#size' => 25,
      '#description' => t('ProfileID at <a href="http://addthis.com/" target="_blank">AddThis.com</a>. Required for statistics.'),
    );
    $form['analytics']['addthis_clickback_tracking_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Track clickback'),
      '#description' => 'Check to allow AddThis to append a variable to your URLs upon sharing. AddThis will use this to track how many people come back to your content via links shared with AddThis. Highly recommended. Always global.',
      '#default_value' => $config->get('analytics.addthis_clickback_tracking_enabled'),
      '#required' => FALSE,
    );

    // Facebook Like tracking requires a namespace to be added.
    $rdf_enabled = \Drupal::moduleHandler()->moduleExists('rdf');
    if (!$rdf_enabled) {
      $rdf_description = '<span class="admin-disabled">' . t('The RDF module needs to be enabled to support Facebook Like tracking support.<br />Enable the module on <a href="!modules">modules</a> page.',
          array('!modules' => base_path() . 'admin/modules')
        ) . '</span>';
    }
    else {
      $rdf_description = t('Check to enable Facebook Like tracking support. Always global.');
    }
    $form['analytics']['title_facebook'] = array(
      '#theme' => 'html_tag',
      '#tag' => 'div',
      '#value' => '<b>' . t('Facebook') . '</b>',
    );
    $form['analytics']['facebook_notice'] = array(
      '#theme' => 'html_tag',
      '#tag' => 'p',
      '#value' => $rdf_description,
      '#access' => !$rdf_enabled,
    );

    $form['analytics']['addthis_facebook_like_count_support_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable Facebook Like tracking'),
      '#description' => Xss::filter($rdf_description, array('span')),
      '#default_value' => $config->get('analytics.addthis_facebook_like_count_support_enabled'),
      '#required' => FALSE,
      '#disabled' => !$rdf_enabled,
    );

    // Google Analytics and Google Social Tracking support.
    $can_do_google_social_tracking = \Drupal::moduleHandler()
      ->moduleExists('google_analytics');
    //@TODO Get back to this.
    $google_analytics_config = $this->configFactory()->get('google_analytics.settings');
    $google_analytics_account = $google_analytics_config->get('google_analytics_account');
    $is_google_analytics_setup = $can_do_google_social_tracking && isset($google_analytics_account);
    $form['analytics']['google_analytics'] = array(
      '#type' => 'markup',
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#markup' => '<b>' . t('Google Analytics') . '</b>',
    );
    if (!$can_do_google_social_tracking) {
      $form['analytics']['can_do_google_analytics'] = array(
        '#type' => 'markup',
        '#prefix' => '<p>',
        '#suffix' => '</p>',
        '#markup' => '<span class="admin-disabled">' . t('Install/enable the <a href="http://drupal.org/project/google_analytics" target="_blank">Google Analytics</a> module for Social Tracking support.') . '</span>',
      );
    }
    elseif ($can_do_google_social_tracking && !$is_google_analytics_setup) {
      $form['analytics']['can_do_google_analytics'] = array(
        '#type' => 'markup',
        '#prefix' => '<p>',
        '#suffix' => '</p>',
        '#value' => '<span class="admin-disabled">' . t('Configure the Google Analytics module correctly with the account code to use this feature.') . '</span>',
      );
    }
    $form['analytics']['addthis_google_analytics_tracking_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Track with Google Analytics'),
      '#description' => t('Check to track shares in your Google Analytics account reports (<a href="http://www.addthis.com/help/google-analytics-integration">more info</a>). Always global.'),
      '#default_value' => $config->get('analytics.addthis_google_analytics_tracking_enabled'),
      '#required' => FALSE,
      '#disabled' => !$is_google_analytics_setup,
    );
    $form['analytics']['addthis_google_analytics_social_tracking_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Track with Google Analytics social'),
      '#description' => t('Check to track shares in the new Google Analytics social interaction reports (<a href="http://www.addthis.com/help/google-analytics-integration#social">more info</a>). Always global.'),
      '#default_value' => $config->get('analytics.addthis_google_analytics_social_tracking_enabled'),
      '#required' => FALSE,
      '#disabled' => !$is_google_analytics_setup,
    );

    // Third party settings.
    $form['third_party'] = array(
      '#type' => 'details',
      '#title' => t('Third party settings'),
      '#open' => FALSE,
    );
    $form['third_party']['twitter_service'] = array(
      '#type' => 'fieldset',
      '#title' => t('Twitter'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['third_party']['twitter_service']['addthis_twitter_via'] = array(
      '#type' => 'textfield',
      '#title' => t('Send via'),
      '#description' => t('When sending a tweet this is the screen name of the user to attribute the Tweet to. (Relates to Tweet Button)'),
      '#default_value' => $config->get('third_party.addthis_twitter_via'),
      '#size' => 15,
    );
    $form['third_party']['twitter_service']['addthis_twitter_template'] = array(
      '#type' => 'textfield',
      '#title' => t('Template text'),
      '#description' => t('The {{title}} and {{url}} are replaced from the Twitter Button.'),
      '#default_value' => $config->get('third_party.addthis_twitter_template'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('addthis.settings')
      ->set('compact_menu.menu_style.addthis_co_brand', $form_state->getValue('addthis_co_brand'))
      ->set('compact_menu.menu_style.addthis_ui_header_color', $form_state->getValue('addthis_ui_header_color'))
      ->set('compact_menu.menu_style.addthis_ui_header_background_color', $form_state->getValue('addthis_ui_header_background_color'))
      ->set('compact_menu.menu_style.addthis_click_to_open_compact_menu_enabled', $form_state->getValue('addthis_click_to_open_compact_menu_enabled'))
      ->set('compact_menu.menu_style.addthis_open_windows_enabled', $form_state->getValue('addthis_open_windows_enabled'))
      ->set('compact_menu.menu_style.addthis_ui_delay', $form_state->getValue('addthis_ui_delay'))
      ->set('compact_menu.enabled_services.addthis_enabled_services', $form_state->getValue('addthis_enabled_services'))
      ->set('compact_menu.additionals.addthis_standard_css_enabled', $form_state->getValue('addthis_standard_css_enabled'))
      ->set('compact_menu.additionals.addthis_508_compliant', $form_state->getValue('addthis_508_compliant'))
      ->set('compact_menu.additionals.addthis_addressbook_enabled', $form_state->getValue('addthis_addressbook_enabled'))
      ->set('excluded_services.addthis_excluded_services', $form_state->getValue('addthis_excluded_services'))
      ->set('analytics.addthis_profile_id', $form_state->getValue('addthis_profile_id'))
      ->set('analytics.addthis_clickback_tracking_enabled', $form_state->getValue('addthis_clickback_tracking_enabled'))
      ->set('analytics.addthis_facebook_like_count_support_enabled', $form_state->getValue('addthis_facebook_like_count_support_enabled'))
      ->set('analytics.addthis_google_analytics_tracking_enabled', $form_state->getValue('addthis_google_analytics_tracking_enabled'))
      ->set('analytics.addthis_google_analytics_social_tracking_enabled', $form_state->getValue('addthis_google_analytics_social_tracking_enabled'))
      ->set('third_party.addthis_twitter_via', $form_state->getValue('addthis_twitter_via'))
      ->set('third_party.addthis_twitter_template', $form_state->getValue('addthis_twitter_template'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}

