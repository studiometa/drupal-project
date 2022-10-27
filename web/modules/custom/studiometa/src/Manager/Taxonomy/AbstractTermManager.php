<?php

namespace Drupal\studiometa\Manager\Taxonomy;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\studiometa\Manager\AbstractEntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * AbstractTermManager class.
 */
abstract class AbstractTermManager extends AbstractEntityManager {

  /**
   * AbstractTermManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   The entity type manager.
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache backend.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entityRepository
   *   The entity repository.
   */
  public function __construct(
    EntityTypeManager $entityTypeManager,
    ContainerInterface $container,
    LanguageManagerInterface $languageManager,
    protected CacheBackendInterface $cacheBackend,
    protected EntityRepositoryInterface $entityRepository
  ) {
    parent::__construct($entityTypeManager, $container, $languageManager);

    $this->cacheBackend = $cacheBackend;
    $this->entityRepository = $entityRepository;
  }

  /**
   * Get terms by type.
   *
   * @param string $vid
   *   Taxonomy type.
   * @param int $parent
   *   Parent term ID.
   *
   * @return array
   *   The taxonomy term corresponding to the type.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */

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
    return $this->entityTypeManager->getStorage('taxonomy_term');
  }

  /**
   * {@inheritdoc}
   */
  protected function query(): QueryInterface {
    return $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->condition('status', 1)
      ->condition(
        'vid',
        $this->getTermType(),
        NULL,
        $this->languageManager->getCurrentLanguage()->getId()
      );
  }

  /**
   * Get term type.
   *
   * @return string
   *   The term type.
   */
  abstract public function getTermType(): string;

  /**
   * Get parent term if exist.
   *
   * @param int $term_id
   *   Term ID.
   *
   * @return \Drupal\taxonomy\TermInterface|false
   *   The parent term if exist.
   */
  public function getParent(int $term_id) {
    /** @var \Drupal\taxonomy\TermStorage */
    $term_storage = $this->getStorage();

    $parents = $term_storage->loadParents($term_id);

    return reset($parents);
  }

}
