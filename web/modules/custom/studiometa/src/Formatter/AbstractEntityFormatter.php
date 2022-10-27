<?php

namespace Drupal\studiometa\Formatter;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\studiometa\Helper\FieldableEntityHelper;
use Drupal\studiometa\Helper\StringHelper;
use Drupal\studiometa\Traits\EntityTranslationAwareTrait;
use Drupal\studiometa\Traits\FieldableEntityAwareTrait;

/**
 * AbstractEntityFormatter class.
 */
abstract class AbstractEntityFormatter extends AbstractFormatter {

  use EntityTranslationAwareTrait;
  use FieldableEntityAwareTrait;

  /**
   * AbstractEntityFormatter constructor.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache Backend.
   * @param \Drupal\studiometa\Helper\StringHelper $stringHelper
   *   The string helper.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   * @param \Drupal\studiometa\Helper\FieldableEntityHelper $fieldableEntityHelper
   *   The fieldable entity helper.
   * @param \Drupal\studiometa\Formatter\LinkFormatter $linkFormatter
   *   The link formatter.
   */
  public function __construct(
    CacheBackendInterface $cacheBackend,
    StringHelper $stringHelper,
    LanguageManagerInterface $languageManager,
    FieldableEntityHelper $fieldableEntityHelper,
    protected LinkFormatter $linkFormatter,
  ) {
    parent::__construct($cacheBackend, $stringHelper);
    $this->setLanguageManager($languageManager);
    $this->setFieldableEntityHelper($fieldableEntityHelper);
    $this->linkFormatter = $linkFormatter;
  }

  /**
   * {@inheritdoc}
   */
  public function format($data, string $format = '', bool $with_cache = TRUE, string $cache_prefix = '', array $method_options = []): array {
    /** @var \Drupal\Core\Entity\ContentEntityInterface */
    $data = $this->getTranslation($data);

    return parent::format($data, $format, $with_cache, $cache_prefix, $method_options);
  }

  /**
   * {@inheritdoc}
   */
  protected function getCacheKey($data, string $format = '', string $cache_prefix = '', array $options = []): string {
    return sprintf(
      '%s##%s',
      parent::getCacheKey($data, $format, $cache_prefix, $options),
      $this->languageManager->getCurrentLanguage()->getId()
    );
  }

}
