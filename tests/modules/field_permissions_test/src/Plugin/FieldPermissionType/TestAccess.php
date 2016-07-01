<?php

namespace Drupal\field_permissions_test\Plugin\FieldPermissionType;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\field_permissions\Plugin\FieldPermissionType\Base;
use Drupal\field_permissions\Plugin\HasCustomPermissionsInterface;

/**
 * A test field permission type plugin.
 *
 * @FieldPermissionType(
 *   id = "test_access",
 *   title = "Test type",
 *   description = "Denies access if the field name starts with 'test_' and this is an edit operation.",
 *   weight = 42
 * )
 */
class TestAccess extends Base implements HasCustomPermissionsInterface {

  /**
   * {@inheritdoc}
   */
  public function hasFieldAccess($operation, EntityInterface $entity, AccountInterface $account) {
    if ($operation === 'view') {
      return $account->hasPermission('foo access');
    }
    return strpos($this->fieldStorage->getName(), 'test_') === FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getPermissions() {
    return [
      'foo access' => [
        'title' => 'Test access custom permission',
      ],
    ];
  }

}
