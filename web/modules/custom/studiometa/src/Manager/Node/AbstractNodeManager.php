<?php

namespace Drupal\studiometa\Manager\Node;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\node\NodeInterface;
use Drupal\studiometa\Manager\AbstractEntityManager;

/**
 * AbstractNodeManager class.
 */
abstract class AbstractNodeManager extends AbstractEntityManager {

  /**
   * Items limit in one page of pager.
   */
  public const PAGER = 12;

  /**
   * Find a node by its title.
   *
   * @param string $title
   *   The title.
   * @param string|null $operator
   *   The operator.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Return the node object.
   */
  public function findByTitle(string $title, ?string $operator = NULL): ?EntityInterface {
    $query = $this->query()->condition('title', $title, $operator)->range(0, 1);

    $ids = $query->execute();

    if (!is_array($ids) || empty($ids)) {
      return NULL;
    }

    return $this->getStorage()->load(array_pop($ids));
  }

  /**
   * {@inheritdoc}
   */
  protected function query(): QueryInterface {
    $langId = $this->languageManager->getCurrentLanguage()->getId();

    return $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', $this->getNodeType(), NULL, $langId)
      ->condition('langcode', $langId)
      ->condition('status', NodeInterface::PUBLISHED, NULL, $langId)
      ->sort('created', 'DESC', $langId);
  }

  /**
   * Get the entity storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The entity storage.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('node');
  }

  /**
   * Get node type.
   *
   * @return string
   *   The node type.
   */
  abstract public function getNodeType(): string;

}
