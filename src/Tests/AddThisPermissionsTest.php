<?php
/**
 * @file
 * Definition of Drupal\addthis\Tests\AddThisFunctionalityTest.
 */
namespace Drupal\addthis\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the basic functionality provided by AddThis.
 *
 * @group addthis
 */
class AddThisPermissionsTest extends WebTestBase {


  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('addthis', 'block', 'field', 'dblog');

  /**
   * Verifies that users cannot access addthis pages without permissions
   * - Creates a new user with administer site permissions
   * - verifies user cannot access addthis settings.
   */
  function testAddThisConfigPermissionDenied(){
    // Create user with permission to "administer addthis settings".
    $user1 = $this->drupalCreateUser(array('administer site configuration'));
    $this->drupalLogin($user1);

    $this->drupalGet('admin/config/user-interface/addthis');
    $this->assertRaw(t('Access denied'),
      'A user without administer addthis permission should not be able to access AddThis system settings.');

    $this->drupalGet('admin/config/user-interface/addthis/advanced');
    $this->assertRaw(t('Access denied'),
      'A user without administer advanced addthis permission should not be able to access AddThis system settings.');
  }

  /**
   * Verifies that users can access addthis basic settings page with
   * administer addthis settings permission
   * - Creates a new user with administer addthis settings
   * - verifies user can access addthis basic settings form.
   */
  function testAddThisConfigPermissionGranted(){
    // Create user with permission to "administer addthis settings".
    $user1 = $this->drupalCreateUser(array('administer addthis settings'));
    $this->drupalLogin($user1);

    $this->drupalGet('admin/config/user-interface/addthis');
    //$this->assertNoText(t('Access denied'), 'A user with administer addthis should be able to access the basic addthis configuration page');

    $this->drupalGet('admin/config/user-interface/addthis/advanced');
    $this->assertText(t('Access denied'),
      'A user without administer advanced addthis permission should not be able to access AddThis system settings.');
  }

  /**
   * Verifies that users can access addthis advanced settings with
   * administer advanced addthis settings permission.
   * - Creates a new user with dminister advanced addthis settings
   * - verifies user can access addthis advanced settings.
   */
  function testAddThisConfigPermissionAdvancedGranted(){
    // Create user with permission to "administer advanced addthis settings".
    $user1 = $this->drupalCreateUser(array('administer advanced addthis settings'));
    $this->drupalLogin($user1);

    $this->drupalGet('admin/config/user-interface/addthis/advanced');
    $this->assertNoRaw(t('Access denied'),
      'A user with administer advanced addthis permission should be able to access AddThis system settings.');
  }


}