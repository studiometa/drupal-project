<?php

namespace Drupal\studiometa_twig_extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * MetaByteConversion class.
 */
class MetaByteConversion extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'format_bytes';
  }

  /**
   * Get filter.
   */
  public function getFilters(): array {
    return [
      new TwigFilter('meta_format_bytes', 'format_size'),
    ];
  }

}
