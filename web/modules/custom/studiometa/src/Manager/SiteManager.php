<?php

namespace Drupal\studiometa\Manager;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\studiometa\Manager\Node\NodeManager;

/**
 * SiteManager class.
 */
class SiteManager {
  use StringTranslationTrait;

  /**
   * Drupal\Core\Config\ImmutableConfig definition.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $siteConfig;

  /**
   * Drupal\Core\Config\ImmutableConfig definition.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $siteCustomConfig;

  /**
   * SiteManager constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config factory.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $urlGenerator
   *   The URL Generator.
   * @param \Drupal\studiometa\Manager\Node\NodeManager $nodeManager
   *   The node manager.
   */
  public function __construct(
    ConfigFactoryInterface $config,
    protected UrlGeneratorInterface $urlGenerator,
    protected NodeManager $nodeManager
  ) {
    $this->siteConfig = $config->get('system.site');
    $this->siteCustomConfig = $config->get('studiometa_pages_manager.site_configuration');
    $this->urlGenerator = $urlGenerator;
    $this->nodeManager = $nodeManager;
  }

  /**
   * Get site name.
   *
   * @return string
   *   Site name.
   */
  public function getName(): string {
    return $this->siteConfig->get('name') ?? '';
  }

  /**
   * Get site slogan.
   *
   * @return string
   *   Site slogan.
   */
  public function getSlogan(): string {
    return $this->siteConfig->get('slogan') ?? '';
  }

  /**
   * Get custom config by key.
   *
   * @param string $key
   *   Key of the config to retrieve.
   *
   * @return string
   *   Value.
   */
  public function getConfig($key): string {
    if (empty($key)) {
      return '';
    }

    return $this->siteCustomConfig->get($key) ?? '';
  }

  /**
   * Get logo url.
   *
   * @return string
   *   Site logo url.
   */
  public function getLogoUrl(): string {
    return theme_get_setting('logo.url') ?? '';
  }

  /**
   * Get social networks informations.
   *
   * @return array
   *   Social networks informations.
   */
  public function getSocialNetworks(): array {
    $social_network = [
      'facebook' => $this->siteCustomConfig->get('facebook'),
      'instagram' => $this->siteCustomConfig->get('instagram'),
      'twitter' => $this->siteCustomConfig->get('twitter'),
      'youtube' => $this->siteCustomConfig->get('youtube'),
    ];

    return array_filter($social_network);
  }

  /**
   * Get listing URL.
   *
   * @param string $name
   *   Listing name.
   * @param array $options
   *   Route options.
   *
   * @return string
   *   Listing URL.
   */
  public function getListingUrl($name, array $options = []): string {
    $node_id = $this->getListingNodeId($name);

    if (empty($node_id)) {
      return '';
    }

    $node = $this->nodeManager->find($node_id);

    /** @var string */
    return $node instanceof NodeInterface
      ? $this->urlGenerator->generateFromRoute('entity.node.canonical', array_merge($options, ['node' => $node->id()]), ['absolute' => TRUE])
      : '';
  }

  /**
   * Get listing node ID.
   *
   * @param string $name
   *   Listing name.
   *
   * @return int|null
   *   Listing ID.
   */
  public function getListingNodeId($name) {
    if (empty($name)) {
      return NULL;
    }

    $node_id = $this->siteCustomConfig->get($name);

    return !empty($node_id) ? (int) $node_id : NULL;
  }

}
