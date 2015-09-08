<?php
/**
 * @file
 * Definition of Drupal\addthis\Tests\AddThisBlockTest.
 */
namespace Drupal\addthis\Tests;


/**
 * Tests the basic functionality provided by AddThis.
 *
 * @group addthis
 */
class AddThisBlockTest extends AddThisBaseTest {


  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('block');

  /**
   * User account with all available permissions
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(){
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser([
      'administer blocks',
    ]);
    $this->drupalLogin($this->adminUser);
  }
  /**
   * Tests ability to add a block to the page.
   * - Adds AddThis block to content region
   * - Verifies the block displays
   */
  public function testAddThisAddBlockToPage(){

    $block = $this->drupalPlaceBlock('addthis', [
      'label' => 'AddThis Block',
      'region' => 'content',
    ]);

    $this->drupalGet('<front>');
    $this->assertBlockAppears($block);
  }

  /**
   * Tests ability to create a block with "addthis_basic_toolbox" and verifies the form fields.
   * - Creates an AddThis block
   * - Sets Display type to addthis_basic_toolbox
   * - Confirms  fields are available
   * - Saves block settings
   * - Confirms configuration was saved successfully.
   *
   * @throws \Exception
   */
  public function testAddThisBlockToolboxConfig(){
    $block = array();
    $block['id'] = 'addthis_block';
    $block['settings[label]'] = $this->randomMachineName(8);
    $block['theme'] = $this->config('system.theme')->get('default');
    $block['region'] = 'content';
    $edit = array(
      'settings[label]' => $block['settings[label]'],
      'id' => $block['id'],
      'region' => $block['region'],
      'settings[addthis_settings][display_type]' => 'addthis_basic_toolbox',
      );

    //Create Block with type=addthis_basic_toolbox
    $this->drupalPostForm('admin/structure/block/add/' . $block['id'] . '/' . $block['theme'], $edit, t('Save block'));
    $this->assertText(t('The block configuration has been saved.'), 'AddThis block has been created.');

    //Load up new block.
    $this->drupalGet('admin/structure/block/manage/' . $block['id']);
    //Verify form fields for toolbox are here.
    $this->assertFieldByName('settings[addthis_settings][type_settings][basic_toolbox][share_services]');
    $this->assertFieldByName('settings[addthis_settings][type_settings][basic_toolbox][buttons_size]');
    $this->assertFieldByName('settings[addthis_settings][type_settings][basic_toolbox][counter_orientation]');
    $this->assertFieldByName('settings[addthis_settings][type_settings][basic_toolbox][extra_css]');

    //Test saving and loading config works.
    $edit = [
      'settings[addthis_settings][display_type]' => 'addthis_basic_toolbox',
      'settings[addthis_settings][type_settings][basic_toolbox][share_services]' => 'twitter,facebook,compact',
      'settings[addthis_settings][type_settings][basic_toolbox][buttons_size]' => 'addthis_32x32_style',
      'settings[addthis_settings][type_settings][basic_toolbox][counter_orientation]' => 'vertical',
      'settings[addthis_settings][type_settings][basic_toolbox][extra_css]' => $this->randomMachineName(),
    ];
    $this->drupalPostForm('admin/structure/block/manage/' . $block['id'], $edit, t('Save block'));
    $this->drupalGet('admin/structure/block/manage/' . $block['id']);
    $this->assertRaw($edit['settings[addthis_settings][display_type]'], 'Content of configurable type successfully verified.');
    $this->assertRaw($edit['settings[addthis_settings][type_settings][basic_toolbox][share_services]'], 'Content of configurable share services block successfully verified.');
    $this->assertRaw($edit['settings[addthis_settings][type_settings][basic_toolbox][buttons_size]'], 'Content of configurable button size block successfully verified.');
    $this->assertRaw($edit['settings[addthis_settings][type_settings][basic_toolbox][counter_orientation]'], 'Content of configurable counter orientation block successfully verified.');
    $this->assertRaw($edit['settings[addthis_settings][type_settings][basic_toolbox][extra_css]'], 'Content of configurable extra css block successfully verified.');



  }

  /**
   * Tests the validation for the AddThisBasicToolbox service settings.
   * - Creates AddThis block
   * - Sets Display type to addthis_basic_toolbox
   * - Tests for failed validation for services field
   * - Tests for successful validation for services field
   *
   * @throws \Exception
   */
  public function testAddThisBlockToolboxServicesValidation(){
    $block = array();
    $block['id'] = 'addthis_block';
    $block['settings[label]'] = $this->randomMachineName(8);
    $block['theme'] = $this->config('system.theme')->get('default');
    $block['region'] = 'content';
    $edit = array(
      'settings[label]' => $block['settings[label]'],
      'id' => $block['id'],
      'region' => $block['region'],
      'settings[addthis_settings][display_type]' => 'addthis_basic_toolbox',
    );

    $this->drupalGet('admin/structure/block/list/' . $this->config('system.theme')->get('default'));

    $this->drupalPostForm('admin/structure/block/add/' . $block['id'] . '/' . $block['theme'], $edit, t('Save block'));
    $this->assertText(t('The block configuration has been saved.'), 'AddThis block has been created.');

    //Test - validation should fail.
    $edit = [
      'settings[addthis_settings][display_type]' => 'addthis_basic_toolbox',
      'settings[addthis_settings][type_settings][basic_toolbox][share_services]' => '',
    ];
    $this->drupalPostForm('admin/structure/block/manage/' . $block['id'], $edit, t('Save block'));
    $this->assertText(t('Services field is required.'), 'AddThis block validation failed because services was empty.');

    //Test - validation should fail.
    $edit = [
      'settings[addthis_settings][display_type]' => 'addthis_basic_toolbox',
      'settings[addthis_settings][type_settings][basic_toolbox][share_services]' => 'facebook,twitter,!@#@!$%!@$@!$',
    ];
    $this->drupalPostForm('admin/structure/block/manage/' . $block['id'], $edit, t('Save block'));
    $this->assertText(t('The declared services are incorrect or nonexistent.'), 'AddThis block validation failed because services has special characters.');


    //Test - validation should pass.
    $edit = [
      'settings[addthis_settings][display_type]' => 'addthis_basic_toolbox',
      'settings[addthis_settings][type_settings][basic_toolbox][share_services]' => 'twitter,facebook,compact',
    ];
    $this->drupalPostForm('admin/structure/block/manage/' . $block['id'], $edit, t('Save block'));
    $this->assertText(t('The block configuration has been saved.'), 'AddThis block saved.');


  }


  /**
   * Tests ability to create a block with "addthis_basic_button" and verifies the form fields.
   * - Creates an AddThis block
   * - Sets Display type to addthis_basic_button
   * - Confirms  fields are available
   * - Saves block settings
   * - Confirms configuration was saved successfully.
   * @throws \Exception
   */
  public function testAddThisBlockButtonConfig(){
    $block = array();
    $block['id'] = 'addthis_block';
    $block['settings[label]'] = $this->randomMachineName(8);
    $block['theme'] = $this->config('system.theme')->get('default');
    $block['region'] = 'content';
    $edit = array(
      'settings[label]' => $block['settings[label]'],
      'id' => $block['id'],
      'region' => $block['region'],
      'settings[addthis_settings][display_type]' => 'addthis_basic_button',
    );

    //Create Block with type=addthis_basic_toolbox
    $this->drupalPostForm('admin/structure/block/add/' . $block['id'] . '/' . $block['theme'], $edit, t('Save block'));
    $this->assertText(t('The block configuration has been saved.'), 'AddThis block has been created.');

    //Load up new block.
    $this->drupalGet('admin/structure/block/manage/' . $block['id']);
    //Verify form fields for toolbox are here.
    $this->assertFieldByName('settings[addthis_settings][type_settings][basic_button][button_size]');
    $this->assertFieldByName('settings[addthis_settings][type_settings][basic_button][extra_css]');

    //Verify block config saves and loads properly
    $edit = array(
      'settings[addthis_settings][display_type]' => 'addthis_basic_button',
      'settings[addthis_settings][type_settings][basic_button][button_size]' => 'big',
      'settings[addthis_settings][type_settings][basic_button][extra_css]' => $this->randomMachineName(),
    );
    $this->drupalPostForm('admin/structure/block/manage/' . $block['id'], $edit, t('Save block'));
    //Load up the new block configs.
    $this->drupalGet('admin/structure/block/manage/' . $block['id']);
    $this->assertRaw($edit['settings[addthis_settings][display_type]'], 'Content of configurable type successfully verified.');
    $this->assertRaw($edit['settings[addthis_settings][type_settings][basic_button][button_size]'], 'Content of configurable button size block successfully verified.');
    $this->assertRaw($edit['settings[addthis_settings][type_settings][basic_button][extra_css]'], 'Content of configurable extra css block successfully verified.');


  }



}