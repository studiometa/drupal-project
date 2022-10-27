<?php

namespace Drupal\studiometa\Manager;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\studiometa\Formatter\AbstractFormatter;
use Drupal\studiometa\Traits\EntityTranslationAwareTrait;
use Drupal\studiometa\Traits\FieldableEntityAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractEntityManager.
 */
abstract class AbstractEntityManager {

  use EntityTranslationAwareTrait;
  use FieldableEntityAwareTrait;
  use ContainerAwareTrait;

  /**
   * AbstractEntityManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    ContainerInterface $container,
    LanguageManagerInterface $languageManager
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->container = $container;
    $this->languageManager = $languageManager;
  }

  /**
   * Find an entity by its nid.
   *
   * @param int $id
   *   The entity ID.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Return the entity object.
   */
  public function find(int $id): ?EntityInterface {
    $entity = $this->getStorage()->load($id);

    if (!($entity instanceof EntityInterface)) {
      return NULL;
    }

    return $entity;
  }

  /**
   * Find multiple entities by nids.
   *
   * @param int[] $ids
   *   The entity ID.
   *
   * @return array|EntityInterface[]
   *   Return entities object array.
   */
  public function findMultiple(array $ids): array {
    $entities = $this->getStorage()->loadMultiple($ids);

    return !empty($entities) ? $entities : [];
  }

  /**
   * Get the entity storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The entity storage.
   */
  abstract protected function getStorage(): EntityStorageInterface;

  /**
   * Fetch content entity with a pager.
   *
   * @param array $filters
   *   Filter the content with :
   *   [field_key => [value => field_value, (operator => 'LIKE')]].
   * @param int $pager
   *   Limit of the pager.
   *
   * @return array
   *   The content.
   */
  public function paginate(array $filters = [], int $pager = 12): array {
    return $this->fetch($filters, $pager);
  }

  /**
   * Fetch content entity with a limit.
   *
   * @param array $filters
   *   Filter the content with :
   *   [field_key => [value => field_value, (operator => 'LIKE')]].
   * @param int $limit
   *   Limit of nodes to fetch.
   *
   * @return array
   *   The content.
   */
  public function limit(array $filters = [], int $limit = 12): array {
    return $this->fetch($filters, NULL, $limit);
  }

  /**
   * Fetch IDs entities.
   *
   * @param array $filters
   *   Filter condition.
   *   [field_key => [value => field_value, (operator => 'LIKE')]].
   *
   * @return array|integer[]
   *   Entities IDs.
   */
  public function getIds(array $filters): array {
    $query = $this->buildFilters($this->query(), $filters);
    $ids = $query->execute();

    return is_array($ids) ? array_values(array_map('intval', $ids)) : [];
  }

  /**
   * Fetch content entity.
   *
   * @param array $filters
   *   Filter condition.
   *   [field_key => [value => field_value, (operator => 'LIKE')]].
   * @param int|null $pager
   *   Limit of the pager if needed.
   * @param int|null $limit
   *   Limit to return to avoid the pager.
   *
   * @return array|EntityInterface[]
   *   The content.
   */
  protected function fetch(array $filters, ?int $pager = NULL, ?int $limit = NULL): array {
    $query = $this->buildFilters($this->query(), $filters);

    if ($pager) {
      $query->pager($pager);
    }
    if ($limit) {
      $query->range(0, $limit);
    }
    $ids = $query->execute();

    if (!is_array($ids)) {
      return [];
    }

    return $this->getStorage()->loadMultiple($ids);
  }

  /**
   * Build query condition filters.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   The query instance.
   * @param array $filters
   *   The filters.
   *   [field_key => [value => field_value, (operator => 'LIKE')]].
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   The query instance.
   */
  protected function buildFilters(QueryInterface $query, array $filters): QueryInterface {
    foreach ($filters as $key => $value) {
      if (!isset($value['value'])) {
        continue;
      }

      $query->condition(
        $key,
        $value['value'],
        isset($value['operator']) && !empty($value['operator']) ? $value['operator'] : NULL
      );
    }

    return $query;
  }

  /**
   * Get an entity query instance.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   The query instance.
   */
  abstract protected function query(): QueryInterface;

  /**
   * Get the number of.
   *
   * @return int
   *   Number of entity.
   */
  public function countAll(): int {
    return (int) $this->query()->condition('status', 1)->count()->accessCheck(TRUE)->execute();
  }

  /**
   * Get the number of current by filters.
   *
   * @return int
   *   Number of entities.
   */
  public function count(array $filters = []): int {
    $query = $this->buildFilters($this->query(), $filters);
    return (int) $query->condition('status', 1)->count()->accessCheck(TRUE)->execute();
  }

  /**
   * Fetch content entity.
   *
   * @param array $filters
   *   Filter the content with :
   *   [field_key => [value => field_value, (operator => 'LIKE')]].
   *
   * @return array
   *   The content.
   */
  public function where(array $filters = []): array {
    return $this->fetch($filters);
  }

  /**
   * Get all entities.
   *
   * @return array
   *   The content.
   */
  public function all(): array {
    $query_results = $this->query()

      ->condition('status', 1)
      ->execute();

    $query_results = is_int($query_results) ? [$query_results] : $query_results;

    return $this->getStorage()->loadMultiple($query_results);
  }

  /**
   * Get first entity.
   *
   * @return int|null
   *   The object.
   */
  public function firstId() {
    $ids = $this->query()

      ->condition('status', 1)
      ->sort('weight', 'ASC')
      ->range(0, 1)
      ->execute();

    return !empty($ids) && is_array($ids) ? (int) array_shift($ids) : NULL;
  }

  /**
   * Get the entity formatter.
   *
   * @param \Drupal\Core\Entity\EntityInterface|string $entity
   *   Entity object or entity bundle.
   * @param string $module
   *   Entity module used to declare service.
   * @param string $suffix
   *   Formatter name can use a suffix before '_formatter'.
   *
   * @return \Drupal\studiometa\Formatter\AbstractFormatter|null
   *   The formatter if exists or NULL otherwise.
   */
  public function getFormatter($entity, string $module = 'studiometa', string $suffix = ''): ?AbstractFormatter {
    $bundle = $entity instanceof EntityInterface ? $entity->bundle() : $entity;
    if (empty($bundle)) {
      return NULL;
    }

    $formatter_name = $module . '.' . $bundle . $suffix . '_formatter';

    if (!$this->container->has($formatter_name)) {
      return NULL;
    }

    $formatter = $this->container->get($formatter_name);

    if (!$formatter instanceof AbstractFormatter) {
      return NULL;
    }

    return $formatter;
  }

  /**
   * Get the entity manager.
   *
   * @param \Drupal\Core\Entity\EntityInterface|string $entity
   *   Entity object or entity bundle.
   *
   * @return \Drupal\studiometa\Manager\AbstractEntityManager|null
   *   The manager if exists or NULL otherwise.
   */
  public function getManager($entity): ?AbstractEntityManager {
    $bundle = $entity instanceof EntityInterface ? $entity->bundle() : $entity;
    if (empty($bundle)) {
      return NULL;
    }

    $manager_name = 'studiometa.' . $bundle . '_manager';

    if (!$this->container->has($manager_name)) {
      return NULL;
    }

    $manager = $this->container->get($manager_name);

    if (!$manager instanceof AbstractEntityManager) {
      return NULL;
    }

    return $manager;
  }

}
