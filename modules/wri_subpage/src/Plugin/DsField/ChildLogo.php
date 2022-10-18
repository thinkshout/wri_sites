<?php

namespace Drupal\wri_subpage\Plugin\DsField;

use Drupal\wri_node\General;
use Drupal\node\NodeInterface;
use Drupal\ds\Plugin\DsField\DsFieldBase;
use Drupal\node\Entity\Node;

/**
 * Plugin that renders the terms from a chosen taxonomy vocabulary.
 *
 * Template path: /web/themes/custom/ts_wrin/templates/fields/field-
 * -childimg.html.twig
 * Template name: field--childimg.html.twig.
 *
 * @DsField(
 *   id = "childlogo",
 *   title = @Translation("Add Logo"),
 *   entity_type = "node",
 *   ui_limit = {"subpage|*"}
 * )
 */
class ChildLogo extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity = $this->entity();
    $build = [];
    $node = $entity->field_parent_page->entity;
    $uri = FALSE;
    if (($node instanceof NodeInterface) && isset($node->field_logo->entity->field_media_image->entity->uri->value)) {
      $uri = $node->field_logo->entity->field_media_image->entity->uri->value;
    }

    if ($uri) {
      $build = [
        '#theme' => 'image_style',
        '#uri' => $uri,
        '#style_name' => '250_wide',
      ];
    }

    return $build;
  }

}
