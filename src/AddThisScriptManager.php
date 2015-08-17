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
   * Get a array with all addthis_config values.
   *
   * Allow alter through 'addthis_configuration'.
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
      'ui_language' => $this->language_manager->getCurrentLanguage()->getId(),
    ];
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
  public function getAddThisShareConfig() {

    $configuration = $this->getAddThisConfig();

    if (isset($configuration['templates'])) {
      $addthis_share = [
        'templates' => $configuration['templates'],
      ];
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

}
