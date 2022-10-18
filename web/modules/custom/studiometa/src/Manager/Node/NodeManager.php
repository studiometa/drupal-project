<?php

namespace Drupal\studiometa\Manager\Node;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\node\NodeInterface;

/**
 * NodeManager class.
 */
final class NodeManager extends AbstractNodeManager {

  /**
   * {@inheritdoc}
   */
  public function getNodeType(): string {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  protected function query(): QueryInterface {
    $langId = $this->languageManager->getCurrentLanguage()->getId();

    return $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->condition('langcode', $langId)
      ->condition('status', NodeInterface::PUBLISHED, NULL, $langId)
      ->sort('title', 'ASC', $langId);
  }

}
