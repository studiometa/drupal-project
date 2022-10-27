<?php

namespace Drupal\studiometa\Helper;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;

/**
 * FieldableEntityHelper helper.
 */
class FieldableEntityHelper {

  /**
   * Get field value.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   Entity.
   * @param string $field_name
   *   Field name.
   * @param mixed $default
   *   Default value.
   * @param string|array $key
   *   Value key.
   * @param bool $is_multiple
   *   Had to return multiples values.
   *
   * @return mixed
   *   Field value.
   *
   * @throws \InvalidArgumentException
   *   Invalid field name specified.
   */
  public function getFieldValue(FieldableEntityInterface $entity, string $field_name, $default = NULL, $key = 'value', bool $is_multiple = FALSE) {
    if (!$entity->hasField($field_name)) {
      return $default;
    }

    $field = $entity->get($field_name);

    if ($field->isEmpty()) {
      return $default;
    }

    if ($field instanceof EntityReferenceFieldItemList) {
      $entities = $field->referencedEntities();

      if (!$is_multiple) {
        if (empty($entities)) {
          return $default;
        }

        return current($entities);
      }

      return $entities;
    }

    $value = $field->getValue();

    if (is_array($value)) {
      if (empty($value)) {
        return $value;
      }

      $first = reset($value);

      if (is_array($key)) {
        $return_values = [];

        foreach ($key as $key_element) {
          if (!array_key_exists($key_element, $first)) {
            continue;
          }

          $return_values[$key_element] = $first[$key_element];
        }

        return $return_values;
      }

      // Get value specified key if exists.
      if (array_key_exists($key, $first)) {
        if ($is_multiple) {
          return array_column($value, $key);
        }

        return $first[$key];
      }

      // Get value instead.
      if ($is_multiple) {
        return $value;
      }

      return $first;
    }

    return $value;
  }

}
