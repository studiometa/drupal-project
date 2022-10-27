<?php

namespace Drupal\studiometa\Formatter;

use Drupal\Core\Cache\Cache;

/**
 * AbstractRenderArrayFormatter class.
 */
abstract class AbstractRenderArrayFormatter extends AbstractFormatter {

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    return [
      '#theme' => $this->getRenderArrayTheme(),
      '#cache' => [
        'contexts' => [
          'url',
          'languages',
        ],
        'max-age' => Cache::PERMANENT,
      ],
    ];
  }

  /**
   * Get the render array theme.
   *
   * @return string
   *   The render array theme.
   */
  abstract protected function getRenderArrayTheme(): string;

}
