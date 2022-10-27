<?php

namespace Drupal\studiometa\Formatter\Taxonomy;

/**
 * IconsFormatter class.
 */
class IconsFormatter extends TermFormatter {

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    return array_merge(
      parent::formatItem($item, $options),
      [
        'slug' => $this->fieldableEntityHelper->getFieldValue($item, 'field_slug'),
      ]
    );
  }

}
