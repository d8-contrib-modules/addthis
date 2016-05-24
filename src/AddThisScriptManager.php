<?php
/**
 * @file
 *
 * Contains \Drupal\addthis\AddThisScriptManager
 *
 * Class definition of a script manager.
 *
 * This class will be used on different places. The result of the attachJsToElement()
 * should be the same in every situation within one request and throughout the
 * loading of the site.
 *
 * When manipulating the configuration do this very early in the request. This
 * could be hook_init() for example. Any other method should be before hook_page_build().
 * The implementation of addthis_page_build() is the first known instance where
 * this class might get used based on the configuration.
 */

namespace Drupal\addthis;


use Drupal\addthis\Util\AddThisJson;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;

class AddThisScriptManager {
  /**
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $language_manager;

  /**
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config_factory;

  /**
   * Construct function.
   *
   * @param \Drupal\Core\Language\LanguageManager $languageManager
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   */
  public function __construct(\Drupal\Core\Language\LanguageManager $languageManager, \Drupal\Core\Config\ConfigFactory $configFactory) {
    $this->language_manager = $languageManager;
    $this->config_factory = $configFactory;
  }

  /**
   * Set values for addthis_config based on Sharing API documentation. See
   * http://support.addthis.com/customer/portal/articles/1337994-the-addthis_config-variable
   * for more details.
   *
   * @TODO Allow alter of 'addthis_configuration'.
   *
   * @todo Add static cache.
   *
   * @todo Make the adding of configuration dynamic.
   *   SRP is lost here.
   */
  public function getAddThisConfig() {
    $config = $this->config_factory->get('addthis.settings');

    $enabled_services = $this->getServiceNamesAsCommaSeparatedString($config->get('compact_menu.enabled_services.addthis_enabled_services')) . 'more';
    $excluded_services = $this->getServiceNamesAsCommaSeparatedString($config->get('excluded_services.addthis_excluded_services'));

    $configuration = [
      'services_compact' => $enabled_services,
      'services_exclude' => $excluded_services,
      //'services_expanded' => @todo - add this
      'ui_508_compliant' => $config->get('compact_menu.additionals.addthis_508_compliant'),
      'ui_click' => $config->get('compact_menu.menu_style.addthis_click_to_open_compact_menu_enabled'),
      'ui_cobrand' => $config->get('compact_menu.menu_style.addthis_co_brand'),
      'ui_delay' => $config->get('compact_menu.menu_style.addthis_ui_delay'),
      'ui_header_background' => $config->get('compact_menu.menu_style.addthis_ui_header_background_color'),
      'ui_header_color' => $config->get('compact_menu.menu_style.addthis_ui_header_color'),
      'ui_open_windows' => $config->get('compact_menu.menu_style.addthis_open_windows_enabled'),
      'ui_use_css' => $config->get('compact_menu.additionals.addthis_standard_css_enabled'),
      'ui_use_addressbook' => $config->get('compact_menu.additionals.addthis_addressbook_enabled'),
      'ui_language' => $this->language_manager->getCurrentLanguage()->getId(),
      'pubid' => $config->get('analytics.addthis_profile_id'),
      'data_track_clickback' => $config->get('analytics.addthis_clickback_tracking_enabled'),

    ];

    //Ensure that the Google Analytics module is enabled for tracking.
    if (\Drupal::moduleHandler()->moduleExists('google_analytics')) {
      if ($config->get('analytics.addthis_google_analytics_tracking_enabled')) {
        $configuration['data_ga_property'] = $this->config_factory->get('google_analytics.settings')
          ->get('account');
        $configuration['data_ga_social'] = $config->get('analytics.addthis_google_analytics_social_tracking_enabled');
      }
    }

    return $configuration;
  }

  /**
   * Get a array with all addthis_share values that we set. More documentation can
   * be found here: http://support.addthis.com/customer/portal/articles/1337996-the-addthis_share-variable
   *
   * @TODO Allow alter of 'addthis_configuration_share'.
   *
   * @todo Add static cache.
   *
   * @todo Make the adding of configuration dynamic.
   *   SRP is lost here.
   */
  public function getAddThisShareConfig() {
    $config = $this->config_factory->get('addthis.settings');

    $addthis_share['templates']['twitter'] = $config->get('third_party.addthis_twitter_template');

    return $addthis_share;
  }

  /**
   * Returns a comma separated list of service values.
   *
   * @param $services
   * @return string
   */
  protected function getServiceNamesAsCommaSeparatedString($services) {
    $serviceNames = array_values($services);
    $servicesAsCommaSeparatedString = '';
    foreach ($serviceNames as $serviceName) {
      if ($serviceName != '0') {
        $servicesAsCommaSeparatedString .= $serviceName . ',';
      }
    }
    return $servicesAsCommaSeparatedString;
  }

  /**
   * Get an array containing the rendered AddThis services.
   *
   * @return array
   *   An array containing the rendered AddThis services.
   */
  public function getServices() {
    $rows = array();
    $json = new AddThisJson();
    $services = $json->decode($this->getServicesJsonUrl());
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

  /**
   * Gets the AddThis services url.
   *
   * @return string
   */
  public function getServicesJsonUrl() {
    $config = $this->config_factory->get('addthis.settings.advanced');
    $service_json_url_key = $config->get('addthis_services_json_url');
    $service_json_url_key = isset($service_json_url_key) ? $service_json_url_key : 'http://cache.addthiscdn.com/services/v1/sharing.en.json';
    return Html::escape(UrlHelper::stripDangerousProtocols($service_json_url_key));
  }

  /**
   * Attach the widget js to the element.
   *
   * @todo Change the scope of the addthis.js.
   *   See if we can get the scope of the addthis.js into the header
   *   just below the settings so that the settings can be used in the loaded
   *   addthis.js of our module.
   *
   * @param array $element
   *   The element to attach the JavaScript to.
   */
  public function attachJsToElement(&$element) {
    $config = $this->config_factory->get('addthis.settings');
    $adv_config = $this->config_factory->get('addthis.settings.advanced');

    //Generate AddThisWidgetURL
    $fragment = [];

    $pubid = $config->get('analytics.addthis_profile_id');
    if (isset($pubid) && !empty($pubid) && is_string($pubid)) {
      $fragment[] = 'pubid=' . $pubid;
    }

    if ($adv_config->get('addthis_widget_load_async')) {
      $fragment[] = 'async=1';
    }

    //Always load the script with domready flag.
    $fragment[] = 'domready=1';


    $element['#attached']['library'][] = 'addthis/addthis.widget';
    $addThisConfig = $this->getAddThisConfig();
    $addThisShareConfig = $this->getAddThisShareConfig();



    $options = [
      'fragment' => implode('&', $fragment),
      'external' => TRUE,
    ];


    $widget_url = $adv_config->get('addthis_widget_js_url');
    $widgetURL = URL::fromUri($widget_url, $options)->toString();


    $element['#attached']['drupalSettings']['addThisWidget'] = [
      'widgetScript' => $widgetURL,
      'config' => $addThisConfig,
      'share' => $addThisShareConfig,
    ];


  }


}



