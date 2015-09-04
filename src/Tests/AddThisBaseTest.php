<?php
/**
 * @file AddThisBaseTest.php
 * Contains the class for AddThisBaseTest.
 */

namespace Drupal\addthis\Tests;


use Drupal\simpletest\WebTestBase;

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

  public function testAddThisToolboxMarkup($services){

  }

  public function testAddThisButtonMarkup(){

  }

}