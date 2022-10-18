<?php

namespace Drupal\studiometa\Traits;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * EntityTranslationAwareTrait class.
 */
trait EntityTranslationAwareTrait {

  /**
   * Language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Set language manager.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   */
  public function setLanguageManager(LanguageManagerInterface $languageManager): void {
    $this->languageManager = $languageManager;
  }

  /**
   * Get the entity translated.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Entity to translate.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   *   A typed data object for the translated data.
   */
  public function getTranslation(ContentEntityInterface $entity) {
    $current_language = $this->languageManager->getCurrentLanguage()->getId();
    return $entity->hasTranslation($current_language) ? $entity->getTranslation($current_language) : $entity;
  }

}
