<?php

namespace Drupal\Tests\field_permissions\Functional;

use Drupal\Core\Url;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;
use Drupal\user\RoleInterface;

/**
 * Test the field permissions report page.
 *
 * @group field_permissions
 */
class FieldReportTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'field_permissions',
    'entity_test',
    'field_ui',
    'text',
  ];

  /**
   * Field storage.
   *
   * @var \Drupal\field\FieldStorageConfigInterface
   */
  protected $fieldStorage;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $admin = $this->drupalCreateUser([
      'administer field permissions',
      'access site reports',
    ]);
    $this->drupalLogin($admin);

    // Add a field.
    // Set up the field_test field.
    $this->fieldStorage = FieldStorageConfig::create([
      'field_name' => 'field_test',
      'type' => 'integer',
      'entity_type' => 'entity_test',
    ]);
    $this->fieldStorage->save();

    $field = FieldConfig::create([
      'field_name' => 'field_test',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
    ]);
    $field->save();
  }

  /**
   * Test the report page.
   */
  public function testReportPage() {
    $this->drupalGet(Url::fromRoute('field_permissions.reports'));
    $this->assertSession()->statusCodeEquals(200);

    // Initially, no fields should be private or custom.
    $this->assertSession()->pageTextContains('Public field (author and administrators can edit, everyone can view)');
    $this->assertSession()->pageTextNotContains('Private field (only author and administrators can edit and view)');
    $this->assertSession()->pageTextNotContains('Not all users have this permission');
    $this->assertSession()->pageTextNotContains('All users have this permission');

    // Set to private.
    $this->fieldStorage->setThirdPartySetting('field_permissions', 'permission_type', FIELD_PERMISSIONS_PRIVATE);
    $this->fieldStorage->save();
    $this->drupalGet(Url::fromRoute('field_permissions.reports'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Private field (only author and administrators can edit and view)');

    // Set custom, and grant no permissions initially.
    $this->fieldStorage->setThirdPartySetting('field_permissions', 'permission_type', FIELD_PERMISSIONS_CUSTOM);
    $this->fieldStorage->save();
    $this->drupalGet(Url::fromRoute('field_permissions.reports'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Not all users have this permission');

    // Grant anonymous and authenticated view permission.
    foreach ([RoleInterface::ANONYMOUS_ID, RoleInterface::AUTHENTICATED_ID] as $role_id) {
      /** @var RoleInterface $role */
      $role = $this->container->get('entity_type.manager')
        ->getStorage('user_role')
        ->load($role_id);
      $role->grantPermission('view_field_test')->save();
    }
    $this->drupalGet(Url::fromRoute('field_permissions.reports'));
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('All users have this permission');
  }

}
