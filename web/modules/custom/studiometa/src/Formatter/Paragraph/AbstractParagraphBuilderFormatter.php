<?php

namespace Drupal\studiometa\Formatter\Paragraph;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\studiometa\Formatter\AbstractRenderArrayFormatter;
use Drupal\studiometa\Formatter\LinkFormatter;
use Drupal\studiometa\Helper\FieldableEntityHelper;
use Drupal\studiometa\Helper\StringHelper;
use Drupal\studiometa\Traits\EntityTranslationAwareTrait;
use Drupal\studiometa\Traits\FieldableEntityAwareTrait;

/**
 * AbstractParagraphBuilderFormatter class.
 */
abstract class AbstractParagraphBuilderFormatter extends AbstractRenderArrayFormatter {

  use EntityTranslationAwareTrait;
  use FieldableEntityAwareTrait;

  /**
   * Link formatter.
   *
   * @var \Drupal\studiometa\Formatter\LinkFormatter
   */
  protected $linkFormatter;

  /**
   * AbstractFormatter constructor.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache Backend.
   * @param \Drupal\studiometa\Helper\StringHelper $stringHelper
   *   The string helper.
   * @param \Drupal\studiometa\Helper\FieldableEntityHelper $fieldableEntityHelper
   *   The fieldable entity helper.
   * @param \Drupal\studiometa\Formatter\LinkFormatter $linkFormatter
   *   The link formatter.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   */
  public function __construct(
    CacheBackendInterface $cacheBackend,
    StringHelper $stringHelper,
    FieldableEntityHelper $fieldableEntityHelper,
    LinkFormatter $linkFormatter,
    LanguageManagerInterface $languageManager
  ) {
    parent::__construct($cacheBackend, $stringHelper);

    $this->setFieldableEntityHelper($fieldableEntityHelper);
    $this->linkFormatter = $linkFormatter;
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    $render_array = parent::formatItem($item, $options);

    /** @var \Drupal\paragraphs\ParagraphInterface */
    $item = $this->getTranslation($item);

    $parent_entity = $item->getParentEntity();
    $entity_id = !empty($parent_entity) ? (int) $parent_entity->id() : NULL;

    if (empty($entity_id)) {
      return $render_array;
    }

    $render_array['#cache']['tags'] = ['node:' . $entity_id];
    $render_array['#cache']['tags'] = ['paragraph:' . $item->id()];

    foreach ($this->formatParagraphData($item) as $key => $value) {
      $render_array['#' . $key] = $value;
    }

    return $render_array;
  }

  /**
   * Format Paragraph data.
   *
   * @return array
   *   Formatted data.
   */
  abstract public function formatParagraphData(ParagraphInterface $data): array;

}
