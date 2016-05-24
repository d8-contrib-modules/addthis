<?php
/**
 * @file
 * Definition of Drupal\addthis\Tests\AddThisFieldTest.
 */
namespace Drupal\addthis\Tests;

/**
 * Tests the basic functionality provided by AddThis.
 *
 * @group addthis
 */
class AddThisFieldTest extends AddThisFieldWebTestBase {


  /**
   * Tests ability to add the addthis field to a node.
   * - Creates content type
   * - Adds AddThis field
   */
  public function testAddThisAddFieldToNode() {
    // Add a single field as administrator user.
    $this->drupalLogin($this->administratorAccount);
    $this->fieldName = $this->createField('addthis', 'addthis_button_widget', '1');
  }

  /**
   * Tests the Manage Form settings for the AddThis Toolbox
   *  - Creates content type
   * - Adds AddThis field
   * - Visits Manage Form Page
   * - Sets display type to "AddThis Basic Toolbox"
   * - Verifies form fields.
   * - @TODO Confirm saving settings & confrim
   */
  public function testAddThisFieldToolboxWidgetForm(){
    // Add a single field as administrator user.
    $this->drupalLogin($this->administratorAccount);
    $this->fieldName = $this->createField('addthis', 'addthis_button_widget', '1');
    $this->drupalGet('admin/structure/types/manage/'. $this->contentTypeName . '/display');
    $this->assertText($this->fieldName, 'Field is configurable');

    //Fieldname is set to the name, without field_ so we need to add it back.
    $field_key = 'field_' . $this->fieldName;

    //Set the display type to AddThis Toolbox.
    $format = 'addthis_basic_toolbox';
    $edit = array(
      'fields[' . $field_key. '][type]' => $format,
      'refresh_rows' => $field_key
    );
    $this->drupalPostAjaxForm(NULL, $edit, array('op' => t('Refresh')));
    $this->assertFieldByName('fields[' . $field_key. '][type]', $format, 'The expected formatter is selected.');

    // Click on the formatter settings button to open the formatter settings
    // form.
    $this->drupalPostAjaxForm(NULL, array(), $field_key . '_settings_edit');

    $this->assertFieldByName('fields[' . $field_key . '][settings_edit_form][settings][share_services]');
    $this->assertFieldByName('fields[' . $field_key . '][settings_edit_form][settings][buttons_size]');
    $this->assertFieldByName('fields[' . $field_key . '][settings_edit_form][settings][counter_orientation]');
    $this->assertFieldByName('fields[' . $field_key . '][settings_edit_form][settings][extra_css]');

  }

  /**
   * Tests the Manage Form settings for the AddThis Button
   * - Creates content type
   * - Adds AddThis field
   * - Visits Manage Form Page
   * - Sets display type to "AddThis Basic Button"
   * - Verifies form fields.
   * - @TODO Confirm saving settings & confrim
   */
  public function testAddThisFieldButtonWidgetForm(){
    // Add a single field as administrator user.
    $this->drupalLogin($this->administratorAccount);
    $this->fieldName = $this->createField('addthis', 'addthis_button_widget', '1');
    $this->drupalGet('admin/structure/types/manage/'. $this->contentTypeName . '/display');
    $this->assertText($this->fieldName, 'Field is configurable');

    //Fieldname is set to the name, without field_ so we need to add it back.
    $field_key = 'field_' . $this->fieldName;

    //Set the display type to AddThis Toolbox.
    $format = 'addthis_basic_button';
    $edit = array(
      'fields[' . $field_key. '][type]' => $format,
      'refresh_rows' => $field_key
    );
    $this->drupalPostAjaxForm(NULL, $edit, array('op' => t('Refresh')));
    $this->assertFieldByName('fields[' . $field_key. '][type]', $format, 'The expected formatter is selected.');

    // Click on the formatter settings button to open the formatter settings
    // form.
    $this->drupalPostAjaxForm(NULL, array(), $field_key . '_settings_edit');

    $this->assertFieldByName('fields[' . $field_key . '][settings_edit_form][settings][button_size]');
    $this->assertFieldByName('fields[' . $field_key . '][settings_edit_form][settings][extra_css]');

  }





}