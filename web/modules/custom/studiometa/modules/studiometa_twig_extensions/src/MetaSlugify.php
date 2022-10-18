<?php

namespace Drupal\studiometa_twig_extensions;

use Twig\Extension\AbstractExtension;
use Cocur\Slugify\Slugify;
use Twig\TwigFilter;

/**
 * Class MetaSlugify.
 *
 * @package Drupal\MetaSlugify
 */
class MetaSlugify extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getName():string {
    return 'meta_slugify';
  }

  /**
   * Get Filter.
   */
  public function getFilters():array {
    return [
      new TwigFilter('meta_slugify', [$this, 'slugify']),
    ];
  }

  /**
   * Slugify string to prevent XSS attack.
   *
   * @param string $string
   *   String to be slugify.
   * @param string $divider
   *   Characters to divide string.
   *
   * @return string
   *   Safe string to be printed.
   */
  public function slugify($string = '', string $divider = '-'): string {
    if (empty($string)) {
      return '';
    }

    $slugify = new Slugify();
    return $slugify->slugify($string, $divider);
  }

}
