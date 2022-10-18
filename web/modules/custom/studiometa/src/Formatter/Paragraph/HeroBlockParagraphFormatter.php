<?php

namespace Drupal\studiometa\Formatter\Paragraph;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\studiometa\Formatter\LinkFormatter;
use Drupal\studiometa\Formatter\MediaFormatter;
use Drupal\studiometa\Helper\FieldableEntityHelper;
use Drupal\studiometa\Helper\StringHelper;

/**
 * HeroBlockParagraphFormatter class.
 */
class HeroBlockParagraphFormatter extends ParagraphFormatter {

  // phpcs:disable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * HeroBlockParagraphFormatter constructor.
   *
   * {@inheritdoc}
   *
   * @param \Drupal\studiometa\Formatter\MediaFormatter $mediaFormatter
   *   The media formatter.
   */
  public function __construct(
    CacheBackendInterface $cacheBackend,
    StringHelper $stringHelper,
    LanguageManagerInterface $languageManager,
    FieldableEntityHelper $fieldableEntityHelper,
    LinkFormatter $linkFormatter,
    protected MediaFormatter $mediaFormatter
  ) {
    parent::__construct($cacheBackend, $stringHelper, $languageManager, $fieldableEntityHelper, $linkFormatter);

    $this->mediaFormatter = $mediaFormatter;
  }

  // phpcs:enable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    if (!$item instanceof ParagraphInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the ParagraphInterface', 1);
    }

    $image = $this->fieldableEntityHelper->getFieldValue($item, 'field_image');

    return [
      'title' => $this->fieldableEntityHelper->getFieldValue($item, 'field_title'),
      'media' => $image ? current($this->mediaFormatter->format($image)) : [],
      'content' => $item->hasField('field_content') && !$item->get('field_content')->isEmpty()
        ? $item->get('field_content')->view(['label' => 'hidden'])
        : '',
    ];
  }

}
