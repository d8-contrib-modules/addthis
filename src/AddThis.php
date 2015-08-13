<?php
/**
 * @file
 * An AddThis-class.
 */

namespace Drupal\addthis;

use Drupal\addthis\Util\AddThisJson;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\addthis\Services\AddThisScriptManager;

class AddThis {
  const BLOCK_NAME = 'addthis_block';
  const DEFAULT_CUSTOM_CONFIGURATION_CODE = 'var addthis_config = {}';
  const DEFAULT_FORMATTER = 'addthis_default_formatter';
  const DEFAULT_NUMBER_OF_PREFERRED_SERVICES = 4;
  const FIELD_TYPE = 'addthis';
  const MODULE_NAME = 'addthis';
  const PERMISSION_ADMINISTER_ADDTHIS = 'administer addthis';
  const PERMISSION_ADMINISTER_ADVANCED_ADDTHIS = 'administer advanced addthis';
  const STYLE_KEY = 'addthis_style';
  const WIDGET_TYPE = 'addthis_button_widget';

  // AddThis attribute and parameter names (as defined in AddThis APIs).
  const PROFILE_ID_QUERY_PARAMETER = 'pubid';
  const TITLE_ATTRIBUTE = 'addthis:title';
  const URL_ATTRIBUTE = 'addthis:url';


  // Persistent variable keys.
  const BLOCK_WIDGET_TYPE_KEY = 'addthis_block_widget_type';
  const BLOCK_WIDGET_SETTINGS_KEY = 'addthis_block_widget_settings';
  const BOOKMARK_URL_KEY = 'addthis_bookmark_url';
  const CUSTOM_CONFIGURATION_CODE_ENABLED_KEY = 'addthis_custom_configuration_code_enabled';
  const CUSTOM_CONFIGURATION_CODE_KEY = 'addthis_custom_configuration_code';
  const SERVICES_CSS_URL_KEY = 'addthis_services_css_url';
  const SERVICES_JSON_URL_KEY = 'addthis_services_json_url';

  const WIDGET_JS_URL_KEY = 'addthis_widget_js_url';
  const WIDGET_JS_LOAD_DOMREADY = 'addthis_widget_load_domready';
  const WIDGET_JS_LOAD_ASYNC = 'addthis_widget_load_async';
  const WIDGET_JS_INCLUDE = 'addthis_widget_include';

  // External resources.
  const DEFAULT_BOOKMARK_URL = 'http://www.addthis.com/bookmark.php?v=300';
  const DEFAULT_SERVICES_CSS_URL = 'http://cache.addthiscdn.com/icons/v1/sprites/services.css';
  const DEFAULT_SERVICES_JSON_URL = 'http://cache.addthiscdn.com/services/v1/sharing.en.json';
  const DEFAULT_WIDGET_JS_URL = 'http://s7.addthis.com/js/300/addthis_widget.js';
  const DEFAULT_WIDGET_JS_LOAD_DOMREADY = TRUE;
  const DEFAULT_WIDGET_JS_LOAD_ASYNC = FALSE;

  // Type of inclusion.
  // 0 = don't include, 1 = pages no admin, 2 = on usages only.
  const DEFAULT_WIDGET_JS_INCLUDE = 2;
  const WIDGET_JS_INCLUDE_NONE = 0;
  const WIDGET_JS_INCLUDE_PAGE = 1;
  const WIDGET_JS_INCLUDE_USAGE = 2;


  // Internal resources.
  const ADMIN_CSS_FILE = 'addthis.admin.css';
  const ADMIN_INCLUDE_FILE = 'includes/addthis.admin.inc';

  // Widget types.
  const WIDGET_TYPE_DISABLED = 'addthis_disabled';

  // Styles.
  const CSS_32x32 = 'addthis_32x32_style';
  const CSS_16x16 = 'addthis_16x16_style';
  private static $instance;

  /* @var AddThisJson */
  private $json;

  private $config;

  /**
   * Get the singleton instance of the AddThis class.
   *
   * @return AddThis
   *   Instance of AddThis.
   */
  public static function getInstance() {

    if (!isset(self::$instance)) {
      $add_this = new AddThis();
      $add_this->setJson(new AddThisJson());
      $add_this->setConfig();
      self::$instance = $add_this;
    }

    return self::$instance;
  }

  /**
   * Set the json object.
   */
  public function setJson(AddThisJson $json) {
    $this->json = $json;
  }

  public function setConfig() {
    $this->config = \Drupal::config('addthis.settings');
  }


  public function getServices() {
    $rows = array();
    $services = $this->json->decode($this->getServicesJsonUrl());
    if (empty($services)) {
      drupal_set_message(t('AddThis services could not be loaded from @service_url', array(
        '@service_url',
        $this->getServicesJsonUrl()
      )), 'warning');
    }
    else {
      foreach ($services['data'] as $service) {
        $serviceCode = SafeMarkup::checkPlain($service['code']);
        $serviceName = SafeMarkup::checkPlain($service['name']);
        $service = array(
          '#type' => 'inline_template',
          '#template' => '<span class="addthis_service_icon icon_' . $serviceCode . '"></span> ' . $serviceName,
        );
        //$rows[$serviceCode] = '<span class="addthis_service_icon icon_' . $serviceCode . '"></span> ' . $serviceName;
        $rows[$serviceCode] = $service;
      }
    }
    return $rows;
  }


  /**
   * Get the settings used by the block display.
   */
  public function getBlockDisplaySettings($widget_type) {
    $block_widget_settings = $this->config->get(self::BLOCK_WIDGET_SETTINGS_KEY);
    $block_widget_settings = isset($block_widget_settings) ? $block_widget_settings : NULL;
    $settings = $block_widget_settings;

    if ($settings == NULL && $widget_type != self::WIDGET_TYPE_DISABLED) {
      $settings = \Drupal::service('plugin.manager.field.formatter')
        ->getDefaultSettings($widget_type);

    }

    return $settings;
  }


  public function getServicesCssUrl() {
    $services_css_url_key = $this->config->get(self::SERVICES_CSS_URL_KEY);
    $services_css_url_key = isset($services_css_url_key) ? $services_css_url_key : self::DEFAULT_SERVICES_CSS_URL;
    return check_url($services_css_url_key);
  }

  public function getServicesJsonUrl() {
    $service_json_url_key = $this->config->get(self::SERVICES_JSON_URL_KEY);
    $service_json_url_key = isset($service_json_url_key) ? $service_json_url_key : self::DEFAULT_SERVICES_JSON_URL;
    return check_url($service_json_url_key);
  }


  /**
   * Return the type of inclusion.
   *
   * @return string
   *   Retuns domready or async.
   */
  public function getWidgetJsInclude() {
    $widget_js_include = $this->config->get(self::WIDGET_JS_INCLUDE);
    $widget_js_include = isset($widget_js_include) ? $widget_js_include : self::DEFAULT_WIDGET_JS_INCLUDE;
    return $widget_js_include;
  }

  /**
   * Return if domready loading should be active.
   *
   * @return bool
   *   Returns TRUE if domready is enabled.
   */
  public function getWidgetJsDomReady() {
    $widget_js_load_domready = $this->config->get(self::WIDGET_JS_LOAD_DOMREADY);
    $widget_js_load_domready = isset($widget_js_load_domready) ? $widget_js_load_domready : self::DEFAULT_WIDGET_JS_LOAD_DOMREADY;
    return $widget_js_load_domready;
  }

  /**
   * Return if async initialization should be active.
   *
   * @return bool
   *   Returns TRUE if async is enabled.
   */
  public function getWidgetJsAsync() {
    $widget_js_load_async = $this->config->get(self::WIDGET_JS_LOAD_ASYNC);
    $widget_js_load_async = isset($widget_js_load_async) ? $widget_js_load_async : self::DEFAULT_WIDGET_JS_LOAD_ASYNC;
    return $widget_js_load_async;
  }


  public function getCustomConfigurationCode() {
    $custom_configuration_code = $this->config->get(self::CUSTOM_CONFIGURATION_CODE_KEY);
    $custom_configuration_code = isset($custom_configuration_code) ? $custom_configuration_code : self::DEFAULT_CUSTOM_CONFIGURATION_CODE;
    return $custom_configuration_code;
  }

  public function isCustomConfigurationCodeEnabled() {
    $custom_configuration_code_enabled = $this->config->get(self::CUSTOM_CONFIGURATION_CODE_ENABLED_KEY);
    $custom_configuration_code_enabled = isset($custom_configuration_code_enabled) ? $custom_configuration_code_enabled : FALSE;

    return (boolean) $custom_configuration_code_enabled;
  }

  public function getBaseWidgetJsUrl() {
    $widget_js_url = $this->config->get(self::WIDGET_JS_URL_KEY);
    $widget_js_url = isset($widget_js_url) ? $widget_js_url : self::DEFAULT_WIDGET_JS_URL;
    return check_url($widget_js_url);
  }

  public function getBaseBookmarkUrl() {
    $bookmark_url = $this->config->get(self::BOOKMARK_URL_KEY);
    $bookmark_url = isset($bookmark_url) ? $bookmark_url : self::DEFAULT_BOOKMARK_URL;
    return check_url($bookmark_url);
  }


  public function getFullBookmarkUrl() {
    return $this->getBaseBookmarkUrl() . $this->getProfileIdQueryParameterPrefixedWithAmp();
  }

  private function getProfileIdQueryParameter($prefix) {
    $profileId = $this->getProfileId();
    return !empty($profileId) ? $prefix . self::PROFILE_ID_QUERY_PARAMETER . '=' . $profileId : '';
  }

  private function getProfileIdQueryParameterPrefixedWithAmp() {
    return $this->getProfileIdQueryParameter('&');
  }

  private function getProfileIdQueryParameterPrefixedWithHash() {
    return $this->getProfileIdQueryParameter('#');
  }


  public function getDisplayTypes() {
    $displays = array();
    foreach ($display_impl = _addthis_field_info_formatter_field_type() as $key => $display) {
      $displays[$key] = t(SafeMarkup::checkPlain($display['label']));
    }
    return $displays;
  }

  /**
   * Provides options for the BasicToolboxForm. This is used in field & block
   * configurations.
   * @param $options
   * @return array
   */
  public function getBasicToolboxForm($parent_class, $options) {
    $element = array();

    $element['share_services'] = array(
      '#title' => t('Services'),
      '#type' => 'textfield',
      '#size' => 80,
      '#default_value' => $options['share_services'],
      '#required' => TRUE,
      //Validate function is defined in addthis.module.
      '#element_validate' => array(
        $parent_class,
        'addThisDisplayElementServicesValidate'
      ),
      '#description' =>
        t('Specify the names of the sharing services and seperate them with a , (comma). <a href="http://www.addthis.com/services/list" target="_blank">The names on this list are valid.</a>') .
        t('Elements that are available but not ont the services list are (!services).',
          array('!services' => 'bubble_style, pill_style, tweet, facebook_send, twitter_follow_native, google_plusone, stumbleupon_badge, counter_* (several supported services), linkedin_counter')
        ),
    );
    $element['buttons_size'] = array(
      '#title' => t('Buttons size'),
      '#type' => 'select',
      '#default_value' => $options['buttons_size'],
      '#options' => array(
        'addthis_16x16_style' => t('Small (16x16)'),
        'addthis_32x32_style' => t('Big (32x32)'),
      ),
    );
    $element['counter_orientation'] = array(
      '#title' => t('Counter orientation'),
      '#description' => t('Specify the way service counters are oriented.'),
      '#type' => 'select',
      '#default_value' => $options['counter_orientation'],
      '#options' => array(
        'horizontal' => t('Horizontal'),
        'vertical' => t('Vertical'),
      )
    );
    $element['extra_css'] = array(
      '#title' => t('Extra CSS declaration'),
      '#type' => 'textfield',
      '#size' => 40,
      '#default_value' => $options['extra_css'],
      '#description' => t('Specify extra CSS classes to apply to the toolbox'),
    );

    return $element;
  }


  /**
   *
   * Returns the basicButtonForm elements to be used in the Field and Block implementation.
   *
   * @param $parent_class
   * @param $options
   * @return array
   */
  public function getBasicButtonForm($parent_class, $options) {
    $element = array();

    $element['button_size'] = array(
      '#title' => t('Image'),
      '#type' => 'select',
      '#default_value' => $options['button_size'],
      '#options' => array(
        'small' => t('Small'),
        'big' => t('Big'),
      ),
    );
    $element['extra_css'] = array(
      '#title' => t('Extra CSS declaration'),
      '#type' => 'textfield',
      '#size' => 40,
      '#default_value' => $options['extra_css'],
      '#description' => t('Specify extra CSS classes to apply to the button'),
    );

    return $element;
  }

  /**
   * Returns rendered markup for the BasicToolbox display. This will be called
   * from both the Field and Block render functions.
   * @param $settings
   * @return null
   */
  function getBasicToolboxMarkup($settings) {
    $element = array(
      '#type' => 'addthis_wrapper',
      '#tag' => 'div',
      '#attributes' => array(
        'class' => array(
          'addthis_toolbox',
          'addthis_default_style',
          ($settings['buttons_size'] == AddThis::CSS_32x32 ? AddThis::CSS_32x32 : NULL),
          $settings['extra_css'],
        ),
      ),
    );

    // Add the widget script.
    $script_manager = AddThisScriptManager::getInstance();
    $script_manager->attachJsToElement($element);


    $services = trim($settings['share_services']);
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
      $orientation = ($settings['counter_orientation'] == 'horizontal' ? TRUE : FALSE);
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
            'addthis_counter',
            'addthis_bubble_style'
          );
          break;
        case 'pill_style':
          $items[$service]['#attributes']['class'] = array(
            'addthis_counter',
            'addthis_pill_style'
          );
          break;
      }
    }

    $element += $items;

    return render($element);
  }


  /**
   * Returns rendered markup for the BasicButton display. This will be called
   * from both the Field and Block render functions.
   * @param $settings
   * @return null
   */
  function getBasicButtonMarkup($settings) {
    $button_img = 'http://s7.addthis.com/static/btn/sm-share-en.gif';
    if (isset($settings['buttons_size']) && $settings['buttons_size'] == 'big') {
      $button_img = 'http://s7.addthis.com/static/btn/v2/lg-share-en.gif';
    }
    //$button_img = $addthis->transformToSecureUrl($button_img);

    //$extra_css = isset($settings['extra_css']) ? $settings['extra_css'] : '';
    $element = array(
      '#type' => 'addthis_wrapper',
      '#tag' => 'a',
      '#attributes' => array(
        'class' => array(
          'addthis_button',
        ),
      ),
    );
    //$element['#attributes'] += $addthis->getAddThisAttributesMarkup($options);

    // Add the widget script.
    $script_manager = AddThisScriptManager::getInstance();
    $script_manager->attachJsToElement($element);

    // Create img button.
    $image = array(
      '#type' => 'addthis_element',
      '#tag' => 'img',
      '#attributes' => array(
        'src' => $button_img,
        'alt' => t('Share page with AddThis'),
      ),
    );
    $element[] = $image;

    return render($element);
  }


}