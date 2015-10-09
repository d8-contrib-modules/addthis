<?php
/**
 * @file AddThisBaseTest.php
 * Contains the class for AddThisBaseTest.
 */

namespace Drupal\addthis\Tests;


use Drupal\simpletest\WebTestBase;

/**
 * Tests the add this functionality.
 */
class AddThisBaseTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('addthis');

  /**
   * {@inheritdoc}
   */
  protected function setUp(){
    parent::setUp();
  }

  /**
   * Helper function to ensure DrupalSettings are being set properly when
   * an AddThis widget is on the page.
   * - Checks drupalSettings variables on a page that has addThis.
   * - Expects to be called from a test that has already added an AddThis element
   * to the page.
   */
  public function testDrupalSettings(){

  }

  /**
   * Helper function to test the toolbox markup on a page.
   * - Ensure the container element has the correct AddThis Class + Custom Classes
   * - Loop through the services enabled
   * - Ensure anchor tag is present with the correct class.
   *
   * @param $services
   * @param $button_size
   * @param $orientation
   * @param $extra_css
   */
  public function testAddThisToolboxMarkup($services, $button_size, $orientation, $extra_css){

  }

  /**
   * Helper function to test the basic button markup on a page.
   * - Ensure the container element has the correct AddThis Class + Custom Classes
   * - Ensure the correct button size is present.
   *
   * @param $services
   * @param $button_size
   * @param $orientation
   * @param $extra_css
   */
  public function testAddThisButtonMarkup($button_size, $extra_css){

  }

}