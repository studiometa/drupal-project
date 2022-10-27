<?php

namespace Drupal\studiometa\Manager\Taxonomy;

/**
 * IconsManager class.
 */
final class IconsManager extends AbstractTermManager {

  /**
   * {@inheritdoc}
   */
  public function getTermType(): string {
    return 'icons';
  }

}
