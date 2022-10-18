<?php

namespace Drupal\studiometa\Formatter\Taxonomy;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\studiometa\Formatter\AbstractEntityFormatter;
use Drupal\studiometa\Formatter\LinkFormatter;
use Drupal\studiometa\Helper\FieldableEntityHelper;
use Drupal\studiometa\Helper\StringHelper;
use Drupal\taxonomy\TermInterface;

/**
 * TermFormatter class.
 */
class TermFormatter extends AbstractEntityFormatter {

  // phpcs:disable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * NodeFormatter constructor.
   *
   * {@inheritdoc}
   *
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $urlGenerator
   *   The url generator.
   */
  public function __construct(
    CacheBackendInterface $cacheBackend,
    StringHelper $stringHelper,
    LanguageManagerInterface $languageManager,
    FieldableEntityHelper $fieldableEntityHelper,
    LinkFormatter $linkFormatter,
    protected UrlGeneratorInterface $urlGenerator,
  ) {
    parent::__construct($cacheBackend, $stringHelper, $languageManager, $fieldableEntityHelper, $linkFormatter);

    $this->urlGenerator = $urlGenerator;
  }

  // phpcs:enable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    if (!$item instanceof TermInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the TermInterface', 1);
    }

    return [
      'tid' => $item->id(),
      'title' => $item->getName(),
    ];
  }

  /**
   * Format an complete object item into an array.
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
    if (!$item instanceof TermInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the TermInterface', 1);
    }

    return self::formatItem($item, $options) + [
      'url' => $this->urlGenerator->generateFromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $item->id()], ['absolute' => TRUE]),
    ];
  }

  /**
   * Format an tree object item into an array.
   *
   * @param mixed $item
   *   Item to format.
   * @param array $options
   *   Format options.
   *
   * @return array
   *   Formatted item.
   */
  protected function formatItemTree($item, array $options = []): array {
    if (!$item instanceof TermInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the TermInterface', 1);
    }

    $parent = $this->fieldableEntityHelper->getFieldValue($item, 'parent');

    return self::formatItem($item, $options) + [
      'parent' => !empty($parent) && $parent instanceof TermInterface ? $parent->id() : NULL,
    ];
  }

}
