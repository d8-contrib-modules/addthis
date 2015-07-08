<?php
/**
 * @file
 * An AddThis-class.
 */

namespace Drupal\addthis;

use Drupal\addthis\Util\AddThisJson;
use Drupal\Component\Utility\SafeMarkup;

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

  public function setConfig(){
    $this->config = \Drupal::config('addthis.settings');
  }



  public function getServices() {
    $rows = array();
    $services = $this->json->decode($this->getServicesJsonUrl());
    if (empty($services)) {
      drupal_set_message(t('AddThis services could not be loaded from @service_url', array('@service_url', $this->getServicesJsonUrl())), 'warning');
    }
    else {
      foreach ($services['data'] as $service) {
        $serviceCode = SafeMarkup::checkPlain($service['code']);
        $serviceName = SafeMarkup::checkPlain($service['name']);
        $rows[$serviceCode] = '<span class="addthis_service_icon icon_' . $serviceCode . '"></span> ' . $serviceName;
      }
    }
    return $rows;
  }

  /**
   * Get the type used for the block.
   */
  public function getBlockDisplayType() {
    $block_widget_type = $this->config->get(self::BLOCK_WIDGET_TYPE_KEY);
    $block_widget_type = isset($block_widget_type) ? $block_widget_type : self::WIDGET_TYPE_DISABLED;
    return $block_widget_type;
  }

  /**
   * Get the settings used by the block display.
   */
  public function getBlockDisplaySettings() {
    $block_widget_settings = $this->config->get(self::BLOCK_WIDGET_SETTINGS_KEY);
    $block_widget_settings = isset($block_widget_settings) ? $block_widget_settings : NULL;
    $settings = $block_widget_settings;

    if ($settings == NULL && $this->getBlockDisplayType() != self::WIDGET_TYPE_DISABLED) {
      $settings =  \Drupal::service('plugin.manager.field.formatter')->getDefinition($this->getBlockDisplayType());
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


  public function addStylesheets() {
    drupal_add_css($this->getServicesCssUrl(), 'external');
    drupal_add_css($this->getAdminCssFilePath(), 'file');
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


}