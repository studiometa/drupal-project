<?php

namespace Drupal\studiometa\Formatter\Node;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\node\NodeInterface;
use Drupal\studiometa\Formatter\AbstractEntityFormatter;
use Drupal\studiometa\Formatter\LinkFormatter;
use Drupal\studiometa\Formatter\MediaFormatter;
use Drupal\studiometa\Helper\FieldableEntityHelper;
use Drupal\studiometa\Helper\StringHelper;
use Drupal\studiometa\Manager\MenuManager;

/**
 * NodeFormatter class.
 */
class NodeFormatter extends AbstractEntityFormatter {

  /**
   * Listing format to load in NodeListingBlock.
   */
  public const LISTING_FORMAT = '';

  // phpcs:disable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * NodeFormatter constructor.
   *
   * {@inheritdoc}
   *
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $urlGenerator
   *   The url generator.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   *   The date formatter.
   * @param \Drupal\studiometa\Formatter\MediaFormatter $mediaFormatter
   *   The media formatter.
   * @param \Drupal\studiometa\Manager\MenuManager $menuManager
   *   The menu manager.
   */
  public function __construct(
    CacheBackendInterface $cacheBackend,
    StringHelper $stringHelper,
    LanguageManagerInterface $languageManager,
    FieldableEntityHelper $fieldableEntityHelper,
    LinkFormatter $linkFormatter,
    protected UrlGeneratorInterface $urlGenerator,
    protected DateFormatterInterface $dateFormatter,
    protected MediaFormatter $mediaFormatter,
    protected MenuManager $menuManager,
  ) {
    parent::__construct($cacheBackend, $stringHelper, $languageManager, $fieldableEntityHelper, $linkFormatter);

    $this->urlGenerator = $urlGenerator;
    $this->dateFormatter = $dateFormatter;
    $this->mediaFormatter = $mediaFormatter;
    $this->menuManager = $menuManager;
  }

  // phpcs:enable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    if (!$item instanceof NodeInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the NodeInterface', 1);
    }

    return [
      'nid' => $item->id(),
      'created' => (int) $item->getCreatedTime(),
      'title' => $item->getTitle(),
      'url' => $this->urlGenerator->generateFromRoute('entity.node.canonical', ['node' => $item->id()], ['absolute' => TRUE]),
    ];
  }

  /**
   * Format an object item into an array with `complete` format.
   *
   * @param mixed $item
   *   Item to format.
   * @param array $options
   *   Format options.
   *
   * @return array
   *   Formatted item.
   */
  protected function formatItemComplete($item, array $options = []): array {
    if (!$item instanceof NodeInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the NodeInterface', 1);
    }

    return $this->formatItem($item, $options);
  }

}
