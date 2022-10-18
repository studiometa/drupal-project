<?php

namespace Drupal\studiometa_twig_extensions;

use Drupal\Component\Utility\Xss;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class MetaSanitizeHTML.
 *
 * @package Drupal\MetaSanitizeHTML
 */
class MetaSanitizeHTML extends AbstractExtension {

  /**
   * List of HTML tags allowed by filter().
   *
   * Which are not inside Xss::getHtmlTagList().
   *
   * @var array
   */
  protected static $htmlTags = [
    'p',
    'br',
    'span',
    'h1',
    'h2',
    'h3',
    'h4',
    'h5',
    'h6',
    'img',
    'figure',
    'figcaption',
    'blockquote',
    'ul',
    'ol',
    'li',
    'table',
    'thead',
    'tbody',
    'tr',
    'th',
    'td',
    'tfooter',
    'img',
    'drupal-media',
    'drupalmedia',
    'drupal\-media',
  ];

  /**
   * {@inheritdoc}
   */
  public function getName():string {
    return 'meta_sanitize';
  }

  /**
   * Get Filter.
   */
  public function getFilters():array {
    return [
      new TwigFilter('meta_sanitize', [$this, 'sanitize'], ['is_safe' => ['html']]),
    ];
  }

  /**
   * Sanitize string to prevent XSS attack.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|string $string
   *   String to be sanitize.
   * @param array $html_tags
   *   An array of HTML tags.
   *
   * @return string
   *   Safe string to be printed.
   */
  public function sanitize($string = '', array $html_tags = []): string {
    if (empty($string)) {
      return '';
    }

    return Xss::filter(
      $string,
      array_merge(
        Xss::getHtmlTagList(),
        static::$htmlTags,
        $html_tags
      )
    );
  }

}
