<?php

namespace Drupal\studiometa\Helper;

/**
 * Class StringHelper.
 *
 * @package Drupal\studiometa\Helper
 */
class StringHelper {

  /**
   * Remove describing brackets contained in the given string.
   *
   * Ex. [Default] My string or [DE][EN] My string.
   *
   * @param string $string
   *   Given string to be cleaned.
   *
   * @return string
   *   String without brackets.
   */
  public function removeBrackets(string $string): string {
    $pattern = '#^\[.+](.+)?$#';
    preg_match($pattern, $string, $matches);

    if (isset($matches[1])) {
      return trim($matches[1]);
    }

    return str_replace(['[', ']'], ['', ''], $string);
  }

  /**
   * Determine if a given string starts with a given substring.
   *
   * @param string $haystack
   *   The string.
   * @param string|string[] $needles
   *   The character that should be at the beginning of the string.
   *
   * @return bool
   *   Return whether the string begins by the given character.
   */
  public function startsWith(string $haystack, $needles): bool {
    foreach ((array) $needles as $needle) {
      if ((string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Pad the left side of a string with another.
   *
   * @param string $value
   *   The string.
   * @param int $length
   *   Number of extra character to add on the left side of the string.
   * @param string $pad
   *   Character to add.
   *
   * @return string
   *   The string with the extra characters added at its left side.
   */
  public function padLeft(string $value, int $length, string $pad = ' '): string {
    return str_pad($value, $length, $pad, STR_PAD_LEFT);
  }

  /**
   * Get the portion of a string before the last occurrence of a given value.
   *
   * @param string $subject
   *   The string.
   * @param string $search
   *   The last occurrence to search.
   *
   * @return string
   *   Return the string without the part after the occurrence of a given value.
   */
  public function beforeLast(string $subject, string $search): string {
    if ($search === '') {
      return $subject;
    }

    $pos = mb_strrpos($subject, $search);

    if ($pos === FALSE) {
      return $subject;
    }

    return $this->substr($subject, 0, $pos);
  }

  /**
   * Returns the portion of string specified by the start and length parameters.
   *
   * @param string $string
   *   The string.
   * @param int $start
   *   Start of the portion to be returned.
   * @param int|null $length
   *   Length of the portion.
   *
   * @return string
   *   Return the portion of the string specified by start and length.
   */
  public function substr(string $string, int $start, ?int $length = NULL): string {
    return mb_substr($string, $start, $length, 'UTF-8');
  }

  /**
   * Convert snake_case to PascalCase.
   *
   * @param string $entry
   *   Entry string.
   *
   * @return string
   *   Output.
   */
  public function snakeToPascalCase(string $entry): string {
    return str_replace('_', '', ucwords($entry, '_'));
  }

  /**
   * Convert camel-case to PascalCase.
   *
   * @param string $entry
   *   Entry string.
   *
   * @return string
   *   Output.
   */
  public function camelToPascalCase(string $entry): string {
    return str_replace('-', '', ucwords($entry, '-'));
  }

}
