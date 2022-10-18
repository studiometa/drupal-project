<?php

namespace Drupal\studiometa\Formatter;

use Drupal\file\FileInterface;

/**
 * VideoFormatter class.
 */
final class VideoFormatter extends AbstractEntityFormatter {

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    if (!$item instanceof FileInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the FileInterface', 1);
    }

    return [
      'src' => $item->createFileUrl(),
      'width' => 0,
      'height' => 0,
    ];
  }

}
