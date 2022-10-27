<?php

namespace Drupal\studiometa_paragraphs_builder_manager\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\Entity\Node;
use Drupal\studiometa\Traits\FieldableEntityAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'PageBuilderBlock' block.
 *
 * @Block(
 *  id = "page_builder_block",
 *  admin_label = @Translation("Page builder block"),
 *  context_definitions = {
 *    "node" = @ContextDefinition("entity:node", required = TRUE)
 *  }
 * )
 */
final class PageBuilderBlock extends BlockBase implements ContainerFactoryPluginInterface {

  use FieldableEntityAwareTrait;

  /**
   * The paragraph manager.
   *
   * @var \Drupal\studiometa\Manager\Paragraph\ParagraphManager
   */
  private $paragraphManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->fieldableEntityHelper = $container->get('studiometa.fieldable_entity_helper');
    $instance->paragraphManager = $container->get('studiometa.paragraph_manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    /** @var \Drupal\node\Entity\Node */
    $node = $this->getContextValue('node');
    if (!$node instanceof Node || !$node->hasField('field_page_builder')) {
      return [];
    }

    /** @var \Drupal\paragraphs\ParagraphInterface[] $entities */
    $entities = $this->fieldableEntityHelper->getFieldValue($node, 'field_page_builder', [], 'value', TRUE);
    foreach ($entities as $entity) {
      if (!$entity->isPublished() && !$node->in_preview) {
        continue;
      }

      $formatter = $this->paragraphManager->getFormatter($entity);

      if (is_null($formatter)) {
        continue;
      }

      $build[] = $formatter->format($entity);
    }

    return $build;
  }

}
