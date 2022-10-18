<?php

namespace Drupal\studiometa\Formatter\Paragraph;

use Drupal\paragraphs\ParagraphInterface;
use Drupal\studiometa\Formatter\AbstractEntityFormatter;

/**
 * ParagraphFormatter class.
 */
class ParagraphFormatter extends AbstractEntityFormatter {

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    if (!$item instanceof ParagraphInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the ParagraphInterface', 1);
    }

    return [
      'id' => $item->id(),
    ];
  }

}
