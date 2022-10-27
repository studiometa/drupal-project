<?php

namespace Drupal\studiometa\Manager\Paragraph;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\studiometa\Manager\AbstractEntityManager;

/**
 * ParagraphManager class.
 */
class ParagraphManager extends AbstractEntityManager {

  /**
   * {@inheritdoc}
   */
  protected function getStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('paragraph');
  }

  /**
   * {@inheritdoc}
   */
  protected function query(): QueryInterface {
    $langId = $this->languageManager->getCurrentLanguage()->getId();

    return $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->condition('langcode', $langId)
      ->condition('status', 1, NULL, $langId)
      ->sort('created', 'DESC', $langId);
  }

}
