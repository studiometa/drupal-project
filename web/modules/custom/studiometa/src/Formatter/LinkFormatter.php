<?php

namespace Drupal\studiometa\Formatter;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Path\PathValidator;
use Drupal\Core\Url;
use Drupal\studiometa\Helper\StringHelper;
use Drupal\studiometa\Helper\UrlHelper;

/**
 * LinkFormatter class.
 */
class LinkFormatter extends AbstractFormatter {

  /**
   * LinkFormatter constructor.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache Backend.
   * @param \Drupal\studiometa\Helper\StringHelper $stringHelper
   *   The string helper.
   * @param \Drupal\studiometa\Helper\UrlHelper $urlHelper
   *   The url helper.
   * @param \Drupal\Core\Path\PathValidator $pathValidator
   *   The path validator.
   */
  public function __construct(
    CacheBackendInterface $cacheBackend,
    StringHelper $stringHelper,
    protected UrlHelper $urlHelper,
    protected PathValidator $pathValidator
  ) {
    parent::__construct($cacheBackend, $stringHelper);
    $this->urlHelper = $urlHelper;
    $this->pathValidator = $pathValidator;
  }

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    if (empty($item['uri'])) {
      return [];
    }

    if (is_object($item['uri']) && method_exists($item['uri'], '__toString')) {
      $item['uri'] = $item['uri']->__toString();
    }

    /*
    Check URLS specified with standard format
    (eg: `<front>`, `https://example.com`, `/example`).
     */
    $url = $this->pathValidator->getUrlIfValidWithoutAccessCheck($item['uri']);

    // Check URLS specified with Drupal format (eg: `entity:node/1`).
    if (!$url) {
      $url = Url::fromUri($item['uri']);
    }

    return [
      'url' => $url->toString(),
      'label' => $item['title'] ?? '',
      'target' => $this->urlHelper->isExternal($url) ? '_blank' : '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function format($data, string $format = '', bool $with_cache = TRUE, string $cache_prefix = '', array $method_options = []): array {
    return parent::format($data, $format, FALSE, $cache_prefix);
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
    $formatted_item = self::formatItem($item, $options);

    return empty($formatted_item['label']) ? [] : $formatted_item;
  }

  /**
   * Format data from link widget result.
   *
   * @param mixed $data
   *   Data.
   * @param string $format
   *   Data format (in snake_case).
   * @param bool $with_cache
   *   Format from cache.
   * @param string $cache_prefix
   *   Cache prefix.
   *
   * @return array
   *   Data formatted.
   */
  public function formatFromLinkWidget($data, string $format = '', bool $with_cache = TRUE, string $cache_prefix = '') {
    if (!isset($data['title'])) {
      throw new \InvalidArgumentException('Specified `item` must have a `title` key.', 1);
    }

    if (!isset($data['uri'])) {
      throw new \InvalidArgumentException('Specified `item` must have a `uri` key.', 1);
    }

    return $this->format([
      'label' => $data['title'] ?? '',
      'url' => $data['uri'] ?? '',
    ], $format, $with_cache, $cache_prefix);
  }

  /**
   * Format multiple items from link widget result.
   *
   * @param array $items
   *   Array of item to format.
   * @param string $format
   *   Data format (in snake_case).
   * @param bool $with_cache
   *   Format from cache.
   * @param string $cache_prefix
   *   Cache prefix.
   *
   * @return array
   *   Data formatted.
   */
  public function formatCollectionFromLinkWidget(array $items, string $format = '', bool $with_cache = TRUE, string $cache_prefix = ''): array {
    return array_map(
      function ($item) use ($format, $with_cache, $cache_prefix) {
        return $this->formatFromLinkWidget($item, $format, $with_cache, $cache_prefix);
      },
      $items
    );
  }

}
