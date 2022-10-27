<?php

namespace Drupal\studiometa_twig_extensions;

use Drupal\Component\Utility\Xss;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class MetaTagConverter.
 *
 * @package Drupal\MetaTagConverter
 */
class MetaTagConverter extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getName():string {
    return 'meta_tag_converter';
  }

  /**
   * Get Filter.
   */
  public function getFilters():array {
    return [
      new TwigFilter('meta_tag_converter', [$this, 'convert'], ['is_safe' => ['html']]),
    ];
  }

  /**
   * Convert predefined tags to HTML.
   *
   * Ex.
   *   <c>string</c> => <span class="class">string</span>
   *   <b>string</b> => <strong>string</strong>
   *
   * @param string $text
   *   The text with tags to be converted.
   * @param string $css_classes
   *   A css class.
   *
   * @return string
   *   The string with HTML tags.
   */
  public function convert($text = '', string $css_classes = ''): string {
    if (empty($text)) {
      return '';
    }

    $css_classes = strpos($css_classes, 'text-') === 0
      ? $css_classes
      : 'text-' . $css_classes;

    $class_element = $css_classes ? "<span class=\"$css_classes\">" : '<span>';

    $text = str_replace(
      ['<c>', '</c>', '<b>', '</b>'],
      [$class_element, '</span>', '<strong>', '</strong>'],
      $text
    );

    return Xss::filter(
      $text,
      [
        'p',
        'br',
        'span',
        'a',
        'em',
        'strong',
        'u',
        'cite',
        'blockquote',
        'code',
        'ul',
        'ol',
        'li',
        'dl',
        'dt',
        'dd',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'hr',
        'table',
        'td',
        'tr',
        'th',
        'thead',
        'tbody',
        'img',
      ]
    );
  }

}
