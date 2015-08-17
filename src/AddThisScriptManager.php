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

use Drupal\addthis\Util\AddThisWidgetJsUrl;


class AddThisScriptManager {

  private $async = NULL;
  private $domready = NULL;

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

    // TODO: Remove these from here. They can be referenced when we use them.
    $config = $this->config_factory->get('addthis.settings.advanced');
    $this->async = $config->get('addthis_widget_load_async');
    $this->domready = $config->get('addthis_widget_load_domready');
  }

  /**
   * Get the current widget js url.
   *
   * @return string
   *   A url reference to the widget js.
   */
  public function getWidgetJsUrl() {
    // TODO: This uses a global function, why do we need to check the URL at this point?
    return check_url($this->config_factory->get('addthis.settings.advanced')->get('addthis_widget_js_url'));
  }

  /**
   * Return if we are on https connection.
   *
   * TODO: Why are we doing all of this URL processing?
   * @return bool
   *   TRUE if the current request is on https.
   */
  public function isHttps() {
    global $is_https;

    return $is_https;
  }

  /**
   * Change the schema from http to https if we are on https.
   *
   * TODO: Why are we doing all of this URL processing?
   * @param  string $url
   *   A full url.
   *
   * @return string
   *   The changed url.
   */
  public function correctSchemaIfHttps($url) {
    if (is_string($url) && $this->isHttps()) {
      return str_replace('http://', 'https://', $url);
    }
    else {
      return $url;
    }
    throw new InvalidArgumentException('The argument was not a string value');
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
    if ($adv_config->get('addthis_widget_js_include') !== 0) {
      $widget_js = new AddThisWidgetJsUrl($adv_config->get('addthis_widget_js_url'));


      $pubid = $config->get('analytics.addthis_profile_id');
      if (isset($pubid) && !empty($pubid) && is_string($pubid)) {
        $widget_js->addAttribute('pubid', $pubid);
      }

      $async = $this->async;
      if ($async) {
        $widget_js->addAttribute('async', 1);
      }

      if ($this->domready) {
        $widget_js->addAttribute('domready', 1);
      }
      else {
        // Only when the script is not loaded after the DOM is ready we include
        // the script with #attached.
        $element['#attached']['library'][] = 'addthis/addthis.widget';
      }

      // Every setting value passed here overrides previously set values but
      // leaves the values that are already set somewhere else and that are not
      // passed here.
      $element['#attached']['drupalSettings']['addthis'] = array(
        'async' => $async,
        'domready' => $this->domready,
        'widget_url' => $this->getWidgetJsUrl(),
        'addthis_config' => $this->getJsAddThisConfig(),
        'addthis_share' => $this->getJsAddThisShare(),
      );
    }
  }

  /**
   * Enable / disable domready loading.
   *
   * @param bool $enabled
   *   TRUE to enabled domready loading.
   */
  function setDomReady($enabled) {
    $this->domready = $enabled;
  }

  /**
   * Enable / disable async loading.
   *
   * @param bool $enabled
   *   TRUE to enabled async loading.
   */
  function setAsync($enabled) {
    $this->async = $enabled;
  }

  /**
   * Get a array with all addthis_config values.
   *
   * Allow alter through 'addthis_configuration'.
   *
   * @todo Add static cache.
   *
   * @todo Make the adding of configuration dynamic.
   *   SRP is lost here.
   */
  private function getJsAddThisConfig() {
    $config = $this->config_factory->get('addthis.settings');

    $enabled_services = $this->getServiceNamesAsCommaSeparatedString($config->get('compact_menu.enabled_services.addthis_enabled_services')) . 'more';
    $excluded_services = $this->getServiceNamesAsCommaSeparatedString($config->get('excluded_services.addthis_excluded_services'));

    $configuration = array(
      'pubid' => $config->get('analytics.addthis_profile_id'),
      'services_compact' => $enabled_services,
      'services_exclude' => $excluded_services,
      'data_track_clickback' => $config->get('analytics.addthis_clickback_tracking_enabled'),
      'ui_508_compliant' => $config->get('compact_menu.additionals.addthis_508_compliant'),
      'ui_click' => $config->get('compact_menu.menu_style.addthis_click_to_open_compact_menu_enabled'),
      'ui_cobrand' => $config->get('compact_menu.menu_style.addthis_co_brand'),
      'ui_delay' => $config->get('compact_menu.menu_style.addthis_ui_delay'),
      'ui_header_background' => $config->get('compact_menu.menu_style.addthis_ui_header_background_color'),
      'ui_header_color' => $config->get('compact_menu.menu_style.addthis_ui_header_color'),
      'ui_open_windows' => $config->get('compact_menu.menu_style.addthis_open_windows_enabled'),
      'ui_use_css' => $config->get('compact_menu.additionals.addthis_standard_css_enabled'),
      'ui_use_addressbook' => $config->get('compact_menu.additionals.addthis_addressbook_enabled'),
      // TODO: check that this returns what we want.
      'ui_language' => $this->language_manager->getCurrentLanguage(),
    );
    // TODO: Do we need to check if the module exists or can we just check the setting?
    if (\Drupal::moduleHandler()->moduleExists('googleanalytics')) {
      if ($config->get('analytics.addthis_google_analytics_tracking_enabled')) {
        $configuration['data_ga_property'] = $this->config_factory->get('google_analytics.settings')->get('google_analytics_account');
        $configuration['data_ga_social'] = $config->get('analytics.addthis_google_analytics_social_tracking_enabled');
      }
    }

    // drupal_alter('addthis_configuration', $configuration);
    return $configuration;
  }

  /**
   * Get a array with all addthis_share values.
   *
   * Allow alter through 'addthis_configuration_share'.
   *
   * @todo Add static cache.
   *
   * @todo Make the adding of configuration dynamic.
   *   SRP is lost here.
   */
  private function getJsAddThisShare() {

    $configuration = $this->getJsAddThisConfig();

    if (isset($configuration['templates'])) {
      $addthis_share = array(
        'templates' => $configuration['templates'],
      );
    }
    $addthis_share['templates']['twitter'] = $this->config_factory->get('addthis.settings')
      ->get('third_party.addthis_twitter_template');

    //drupal_alter('addthis_configuration_share', $configuration);
    return $addthis_share;
  }

  /**
   * Returns a comma separated list of service values.
   *
   * @param $services
   * @return string
   */
  public function getServiceNamesAsCommaSeparatedString($services) {
    $serviceNames = array_values($services);
    $servicesAsCommaSeparatedString = '';
    foreach ($serviceNames as $serviceName) {
      if ($serviceName != '0') {
        $servicesAsCommaSeparatedString .= $serviceName . ',';
      }
    }
    return $servicesAsCommaSeparatedString;
  }

}
