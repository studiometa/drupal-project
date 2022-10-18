<?php

namespace Drupal\studiometa_blocks_manager\Plugin\Block;

use Drupal\Component\Plugin\Exception\ContextException;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'HeroBlock' block.
 *
 * @Block(
 *  id = "hero_block",
 *  admin_label = @Translation("C3 - Hero"),
 *  context_definitions = {
 *    "node" = @ContextDefinition("entity:node", required = TRUE)
 *   }
 * )
 */
final class HeroBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Hero block paragraph formatter.
   *
   * @var \Drupal\studiometa\Formatter\Paragraph\HeroBlockParagraphFormatter
   */
  protected $heroBlockParagraphFormatter;

  /**
   * Node formatter.
   *
   * @var \Drupal\studiometa\Formatter\Node\NodeFormatter
   */
  protected $nodeFormatter;

  /**
   * Site manager.
   *
   * @var \Drupal\studiometa\Manager\SiteManager
   */
  protected $siteManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->heroBlockParagraphFormatter = $container->get('studiometa.hero_block_paragraph_formatter');
    $instance->nodeFormatter = $container->get('studiometa.node_formatter');
    $instance->siteManager = $container->get('studiometa.site_manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    try {
      /** @var \Drupal\node\NodeInterface */
      $node = $this->getContextValue('node');
    }
    catch (ContextException $e) {
      return [];
    }

    if (!$node instanceof NodeInterface) {
      return [];
    }

    $render_array = [
      '#theme' => 'meta_hero_block',
      '#back_url' => $this->getBackUrl($node),
      '#cache' => [
        'contexts' => [
          'url',
          'languages',
        ],
        'max-age' => Cache::PERMANENT,
      ],
    ];

    $hero_item = $this->heroBlockParagraphFormatter->fieldableEntityHelper->getFieldValue($node, 'field_hero');

    if (empty($hero_item)) {
      $category = $this->nodeFormatter->fieldableEntityHelper->getFieldValue($node, 'field_publication_category', []);

      return array_merge($render_array, [
        '#subtitle' => !empty($category) ? $category->getName() : '',
        '#title' => $node->getTitle(),
      ]);
    }

    $hero = $this->heroBlockParagraphFormatter->format($hero_item);

    return array_merge($render_array, [
      '#subtitle' => $hero['subtitle'] ?? '',
      '#title' => $hero['title'] ?? '',
      '#content' => $hero['content'] ?? '',
      '#media' => $hero['media'] ?? [],
      '#has_black_text' => $hero['has_black_text'] ?? FALSE,
    ]);
  }

  /**
   * Get back url.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Given node entity.
   *
   * @return null|string
   *   Back url.
   */
  private function getBackUrl(NodeInterface $node): ?string {
    $matches = [
      'press_release' => 'mediaroom_press_release',
    ];

    $bundle = $node->bundle();
    if (empty($bundle) || !array_key_exists($bundle, $matches)) {
      return NULL;
    }

    return $this->siteManager->getListingUrl($matches[$bundle]);
  }

}
