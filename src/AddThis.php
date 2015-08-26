<?php
/**
 * @file
 *
 * Contains \Drupal\addthis\AddThis.
 *
 * An AddThis-class.
 */

namespace Drupal\addthis;

use Drupal\addthis\Util\AddThisJson;
use Drupal\Component\Utility\SafeMarkup;

class AddThis {

  // Persistent variable keys.
  /*
  const SERVICES_CSS_URL_KEY = 'addthis_services_css_url';
    const SERVICES_JSON_URL_KEY = 'addthis_services_json_url';
  */

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
   * @var \Drupal\addthis\AddThisScriptManager.
   */
  protected $add_this_script_manager;

  /**
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config_factory;

  /**
   * @param \Drupal\addthis\AddThisScriptManager $addThisScriptManager
   */
  public function __construct(\Drupal\addthis\AddThisScriptManager $addThisScriptManager, \Drupal\Core\Config\ConfigFactory $configFactory) {
    $this->add_this_script_manager = $addThisScriptManager;
    $this->config_factory = $configFactory;
    $this->json = new AddThisJson();
    $this->setConfig();
  }

  /**
   * Set the json object.
   */
  public function setJson(AddThisJson $json) {
    $this->json = $json;
  }

  public function setConfig() {
    $this->config = $this->config_factory->get('addthis.settings');
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


  public function getServicesJsonUrl() {
    $service_json_url_key = $this->config->get(self::SERVICES_JSON_URL_KEY);
    $service_json_url_key = isset($service_json_url_key) ? $service_json_url_key : self::DEFAULT_SERVICES_JSON_URL;
    return check_url($service_json_url_key);
  }


}