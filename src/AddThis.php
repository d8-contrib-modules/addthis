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

  /* @var AddThisJson */
  private $json;
  private $config;

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
        //#options expects a string, not an array. Render the element so it becomes a string.
        $rows[$serviceCode] = render($service);
      }
    }
    return $rows;
  }

  public function getServicesJsonUrl() {
    $service_json_url_key = $this->config->get('addthis_services_json_url');
    $service_json_url_key = isset($service_json_url_key) ? $service_json_url_key : 'http://cache.addthiscdn.com/services/v1/sharing.en.json';
    return check_url($service_json_url_key);
  }


}
