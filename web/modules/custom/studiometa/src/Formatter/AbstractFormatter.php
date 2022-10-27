<?php

namespace Drupal\studiometa\Formatter;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\studiometa\Helper\StringHelper;

/**
 * AbstractFormatter class.
 */
abstract class AbstractFormatter {

  public const CACHE_KEY_PREFIX = 'formatter';

  /**
   * AbstractFormatter constructor.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache Backend.
   * @param \Drupal\studiometa\Helper\StringHelper $stringHelper
   *   The string helper.
   */
  public function __construct(
    protected CacheBackendInterface $cacheBackend,
    protected StringHelper $stringHelper
  ) {
    $this->cacheBackend = $cacheBackend;
    $this->stringHelper = $stringHelper;
  }

  /**
   * Get formatted item from cache by cache ID given.
   *
   * @param string $cache_id
   *   Cache ID given.
   *
   * @return array|null
   *   Formatted item or nulL.
   */
  final protected function getItemFromCache(string $cache_id): ?array {
    $cache = $this->cacheBackend->get($cache_id);

    if (is_object($cache) && property_exists($cache, 'data')) {
      return $cache->data;
    }

    return NULL;
  }

  /**
   * Set formatted item to cache.
   *
   * @param string $cache_id
   *   Cache ID given.
   * @param array $item
   *   Formatted item.
   *
   * @return array
   *   Cached item.
   */
  final protected function setItemToCache(string $cache_id, array $item): array {
    $this->cacheBackend->set($cache_id, $item, (int) date('U') + (00));

    return $item;
  }

  /**
   * Get cache key.
   *
   * @param mixed $data
   *   Data.
   * @param string $format
   *   Format.
   * @param string $cache_prefix
   *   Cache prefix.
   * @param array $options
   *   Request options.
   *
   * @return string
   *   Cache key.
   */
  protected function getCacheKey($data, string $format = '', string $cache_prefix = '', array $options = []): string {
    return sprintf(
      '%s_%s%s--%s-%s',
      self::CACHE_KEY_PREFIX,
      !empty($cache_prefix) ? $cache_prefix . '_' : '',
      md5(serialize($data)),
      md5(serialize($options)),
      $format
    );
  }

  /**
   * Format an object item into an array.
   *
   * @param mixed $item
   *   Item to format.
   * @param array $options
   *   Format options.
   *
   * @return array
   *   Formatted item.
   */
  abstract protected function formatItem($item, array $options = []): array;

  /**
   * Format data.
   *
   * @param mixed $data
   *   Data.
   * @param string $format
   *   Data format (in snake_case).
   * @param bool $with_cache
   *   Format from cache.
   * @param string $cache_prefix
   *   Cache prefix.
   * @param array $method_options
   *   Targeted format method options.
   *
   * @return array
   *   Data formatted.
   */
  public function format($data, string $format = '', bool $with_cache = TRUE, string $cache_prefix = '', array $method_options = []): array {
    if ($with_cache) {
      $cache_key = $this->getCacheKey($data, $format, $cache_prefix, $method_options);
      $cached_data = $this->getItemFromCache($cache_key);

      if (NULL !== $cached_data) {
        return $cached_data;
      }
    }

    $callback_method_name = 'formatItem' . $this->stringHelper->snakeToPascalCase($format);

    if (!method_exists($this, $callback_method_name)) {
      throw new \InvalidArgumentException('Specified format method doesn\'t exists or is not callable.', 1);
    }

    $result = call_user_func(
      function ($callback_data) use ($callback_method_name, $method_options) {
        return $this->$callback_method_name($callback_data, $method_options);
      },
      $data
    );

    if (!$with_cache) {
      return $result;
    }

    return $this->setItemToCache(
      $cache_key,
      $result
    );
  }

  /**
   * Format multiple items.
   *
   * @param array $items
   *   Array of item to format.
   * @param string $format
   *   Data format (in snake_case).
   * @param bool $with_cache
   *   Format from cache.
   * @param string $cache_prefix
   *   Cache prefix.
   * @param array $method_options
   *   Targeted format method options.
   *
   * @return array
   *   Data formatted.
   */
  public function formatCollection(array $items, string $format = '', bool $with_cache = TRUE, string $cache_prefix = '', array $method_options = []): array {
    return array_map(
      function ($item) use ($format, $with_cache, $cache_prefix, $method_options) {
        return $this->format($item, $format, $with_cache, $cache_prefix, $method_options);
      },
      $items
    );
  }

  /**
   * Format collection grouped by given key.
   *
   * @param array $items
   *   Items list.
   * @param string $key1
   *   Key to group (first level).
   * @param string $key2
   *   Key to group (second level).
   * @param string $format
   *   Data format (in snake_case).
   * @param int $limit
   *   Limit of children.
   * @param bool $with_cache
   *   Format from cache.
   * @param string $cache_prefix
   *   Cache prefix.
   * @param array $method_options
   *   Targeted format method options.
   *
   * @return array
   *   Formatted items.
   */
  public function formatCollectionGroupBy(array $items, string $key1, string $key2 = '', string $format = '', $limit = NULL, bool $with_cache = TRUE, string $cache_prefix = '', array $method_options = []): array {
    $items = $this->formatCollection($items, $format, $with_cache, $cache_prefix, $method_options);

    $ordered_items = [];

    foreach ($items as $item) {
      if (!empty($key2) && array_key_exists($key1, $item) && array_key_exists($key2, $item)) {
        $ordered_items[$item[$key1]][$item[$key2]][] = $item;
        continue;
      }

      if (array_key_exists($key1, $item)) {
        $key = is_array($item[$key1]) ? current($item[$key1]) : $item[$key1];
        if (!empty($limit) && isset($ordered_items[$key]) && isset($ordered_items[$key]['items']) && count($ordered_items[$key]['items']) >= $limit) {
          continue;
        }

        $ordered_items[$key]['self'] = $item[$key1];
        unset($item[$key1]);

        $ordered_items[$key]['items'][] = $item;
        continue;
      }

      $ordered_items['-'][] = $item;
    }

    return $ordered_items;
  }

}
