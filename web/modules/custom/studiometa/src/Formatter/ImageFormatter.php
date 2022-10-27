<?php

namespace Drupal\studiometa\Formatter;

use Drupal\file\FileInterface;

/**
 * ImageFormatter class.
 */
final class ImageFormatter extends AbstractEntityFormatter {

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    if (!$item instanceof FileInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the FileInterface', 1);
    }

    return [
      'width' => $options['width'] ?? 1,
      'height' => $options['height'] ?? 1,
      'alt' => $options['alt'] ?? $item->getFilename(),
      'src' => $item->createFileUrl(),
    ];

  }

}
