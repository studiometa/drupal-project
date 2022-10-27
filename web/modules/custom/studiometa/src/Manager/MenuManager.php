<?php

namespace Drupal\studiometa\Manager;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Menu\MenuLinkManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * MenuManager class.
 */
class MenuManager extends AbstractEntityManager {

  // phpcs:disable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * MenuManager constructor.
   *
   * {@inheritdoc}
   *
   * @param \Drupal\Core\Menu\MenuLinkManagerInterface $menuLinkManager
   *   The menu link manager.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    ContainerInterface $container,
    LanguageManagerInterface $languageManager,
    protected MenuLinkManagerInterface $menuLinkManager,
  ) {
    parent::__construct($entityTypeManager, $container, $languageManager);

    $this->menuLinkManager = $menuLinkManager;
  }

  // phpcs:enable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * Get the entity storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The entity storage.
   */
  protected function getStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('menu');
  }

  /**
   * {@inheritdoc}
   */
  protected function query(): QueryInterface {
    return $this->getStorage()->getQuery()
      ->accessCheck(TRUE);
  }

  /**
   * Find an entity by its nid.
   *
   * @param mixed $id
   *   The entity ID.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Return the entity object.
   */
  public function find(mixed $id): ?EntityInterface {
    $entity = $this->getStorage()->load($id);

    if (!($entity instanceof EntityInterface)) {
      return NULL;
    }

    return $entity;
  }

  /**
   * Formatted search results by category, will return only page title.
   *
   * @param int|string $node_id
   *   Nodes ID given.
   *
   * @return mixed
   *   Menu label.
   */
  public function getParentMenuTitleByNodeId($node_id) {
    $parent = '';
    $menu_link = $this->menuLinkManager->loadLinksByRoute('entity.node.canonical', ['node' => $node_id], 'main');

    if (empty($menu_link)) {
      return NULL;
    }

    $link = array_shift($menu_link);

    if ($link !== NULL) {
      $parent = $link->getParent();
    }

    if (empty($parent)) {
      return NULL;
    }

    // Get all node parents ID.
    $parents = $this->menuLinkManager->getParentIds($parent);

    // Get first menu level.
    $parent = end($parents);

    /* @phpstan-ignore-next-line */
    return $this->menuLinkManager->createInstance($parent)->getTitle();
  }

}
