<?php

namespace Drupal\studiometa\Formatter;

use Drupal\file\FileInterface;

/**
 * FileFormatter class.
 */
final class FileFormatter extends AbstractEntityFormatter {

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    if (!$item instanceof FileInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the FileInterface', 1);
    }

    $filename = $item->getFilename();

    return [
      'src' => $item->createFileUrl(),
      'filename' => $filename,
      'name' => \pathinfo($filename, PATHINFO_FILENAME),
      'extension' => \pathinfo($filename, PATHINFO_EXTENSION),
      'mime' => $item->getMimeType(),
      'size' => $item->getSize(),
    ];
  }

}
