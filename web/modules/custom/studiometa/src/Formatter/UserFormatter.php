<?php

namespace Drupal\studiometa\Formatter;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\studiometa\Helper\FieldableEntityHelper;
use Drupal\studiometa\Helper\StringHelper;
use Drupal\user\UserInterface;

/**
 * UserFormatter class.
 */
final class UserFormatter extends AbstractEntityFormatter {

  // phpcs:disable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * UserFormatter constructor.
   *
   * {@inheritdoc}
   *
   * @param \Drupal\studiometa\Formatter\ImageFormatter $imageFormatter
   *   The image formatter.
   */
  public function __construct(
    CacheBackendInterface $cacheBackend,
    StringHelper $stringHelper,
    LanguageManagerInterface $languageManager,
    FieldableEntityHelper $fieldableEntityHelper,
    LinkFormatter $linkFormatter,
    protected ImageFormatter $imageFormatter
  ) {
    parent::__construct($cacheBackend, $stringHelper, $languageManager, $fieldableEntityHelper, $linkFormatter);

    $this->setFieldableEntityHelper($fieldableEntityHelper);
    $this->imageFormatter = $imageFormatter;
  }

  // phpcs:enable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    if (!$item instanceof UserInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the UserInterface', 1);
    }

    $image = $this->fieldableEntityHelper->getFieldValue($item, 'user_picture', NULL, 'target_id');

    return [
      'id' => $item->id(),
      'name' => $item->getDisplayName(),
      'image' => !empty($image) ? $this->imageFormatter->format($image, '', TRUE, '', $options) : [],
    ];
  }

}
