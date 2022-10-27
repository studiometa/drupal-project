<?php

namespace Drupal\studiometa\Manager;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Query\QueryInterface;

/**
 * MediaManager class.
 */
class MediaManager extends AbstractEntityManager {

  /**
   * Get the entity storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The entity storage.
   */
  protected function getStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('media');
  }

  /**
   * {@inheritdoc}
   */
  protected function query(): QueryInterface {
    return $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->sort('created', 'DESC');
  }

}
