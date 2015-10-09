<?php
/**
 * @file
 * Contains \Drupal\addthis\Form\AddThisSettingsAdvancedForm.
 */

namespace Drupal\addthis\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Component\Utility\Xss;

/**
 * Defines a form to configure maintenance settings for this site.
 */
class AddThisSettingsAdvancedForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'addthis_settings_advanced_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['addthis.settings.advanced'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('addthis.settings.advanced');

    //Add our library to the settings form to add in custom CSS.
    $form['#attached']['library'][] = 'addthis/addthis.admin';

    // Service URL's settings.
    $form['service_urls_details'] = array(
      '#type' => 'details',
      '#title' => t('Service URLs'),
      '#open' => TRUE,
    );
    $form['service_urls_details']['addthis_bookmark_url'] = array(
      '#type' => 'textfield',
      '#title' => t('AddThis bookmark URL'),
      '#default_value' => $config->get('addthis_bookmark_url'),
      '#required' => TRUE,
    );
    $form['service_urls_details']['addthis_services_css_url'] = array(
      '#type' => 'textfield',
      '#title' => t('AddThis services stylesheet URL'),
      '#default_value' => $config->get('addthis_services_css_url'),
      '#required' => TRUE,
    );
    $form['service_urls_details']['addthis_services_json_url'] = array(
      '#type' => 'textfield',
      '#title' => t('AddThis services json URL'),
      '#default_value' => $config->get('addthis_services_json_url'),
      '#required' => TRUE,
    );
    $form['service_urls_details']['addthis_widget_js_url'] = array(
      '#type' => 'textfield',
      '#title' => t('AddThis javascript widget URL'),
      '#default_value' => $config->get('addthis_widget_js_url'),
      '#required' => TRUE,
    );

    // Advanced settings.
    $form['advanced_settings_details'] = array(
      '#type' => 'details',
      '#title' => t('Advanced settings'),
      '#access' => \Drupal::currentUser()
        ->hasPermission('administer advanced addthis'),
      '#open' => TRUE,
    );
    $form['advanced_settings_details']['addthis_custom_configuration_code_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Use custom AddThis configuration code'),
      '#default_value' => $config->get('addthis_custom_configuration_code_enabled'),
      '#required' => FALSE,
      '#description' => t('Use custom AddThis configuration code. If checked, custom configuration will be used instead of other configuration settings provided in AddThis administration user interface.'),
    );
    $form['advanced_settings_details']['addthis_custom_configuration_code'] = array(
      '#type' => 'textarea',
      '#title' => t('AddThis custom configuration code'),
      '#default_value' => $config->get('addthis_custom_configuration_code'),
      '#required' => FALSE,
      '#description' => t('AddThis custom configuration code. See format at <a href="http://addthis.com/" target="_blank">AddThis.com</a>'),
    );
    $form['advanced_settings_details']['addthis_widget_load_async'] = array(
      '#type' => 'checkbox',
      '#title' => t('Initialize asynchronously through addthis.init().'),
      '#description' => t('Use this when you have your own Ajax functionality or create things after the DOM is ready trough Javascript. Initialize the addthis functionality through addthis.init().'),
      '#default_value' => $config->get('addthis_widget_load_async'),
    );
    $form['advanced_settings_details']['addthis_widget_include'] = array(
      '#type' => 'select',
      '#title' => t('Load widget js.'),
      '#options' => array(
        '0' => t('Don\'t include at all.'),
        '1' => t('Include on all (non admin) pages'),
        '2' => t('(Default) Include on widget rendering by Drupal.'),
      ),
      '#default_value' => $config->get('addthis_widget_include'),
    );


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('addthis.settings.advanced')
      ->set('addthis_bookmark_url', $form_state->getValue('addthis_bookmark_url'))
      ->set('addthis_services_css_url_key', $form_state->getValue('addthis_services_css_url_key'))
      ->set('addthis_services_json_url_key', $form_state->getValue('addthis_services_json_url_key'))
      ->set('addthis_widget_js_url', $form_state->getValue('addthis_widget_js_url'))
      ->set('addthis_custom_configuration_code_enabled', $form_state->getValue('addthis_custom_configuration_code_enabled'))
      ->set('addthis_custom_configuration_code', $form_state->getValue('addthis_custom_configuration_code'))
      ->set('addthis_widget_load_async', $form_state->getValue('addthis_widget_load_async'))
      ->set('addthis_widget_include', $form_state->getValue('addthis_widget_include'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}

