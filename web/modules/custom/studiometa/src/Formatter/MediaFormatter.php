<?php

namespace Drupal\studiometa\Formatter;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\media\MediaInterface;
use Drupal\studiometa\Helper\FieldableEntityHelper;
use Drupal\studiometa\Helper\StringHelper;
use Drupal\studiometa\Traits\FieldableEntityAwareTrait;

/**
 * MediaFormatter class.
 */
final class MediaFormatter extends AbstractEntityFormatter {

  use FieldableEntityAwareTrait;

  /**
   * MediaFormatter constructor.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cache Backend.
   * @param \Drupal\studiometa\Helper\StringHelper $stringHelper
   *   The string helper.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   * @param \Drupal\studiometa\Helper\FieldableEntityHelper $fieldableEntityHelper
   *   The fieldable entity helper.
   * @param \Drupal\studiometa\Formatter\LinkFormatter $linkFormatter
   *   The link formatter.
   * @param \Drupal\studiometa\Formatter\FileFormatter $fileFormatter
   *   The file formatter.
   * @param \Drupal\studiometa\Formatter\ImageFormatter $imageFormatter
   *   The image formatter.
   * @param \Drupal\studiometa\Formatter\VideoFormatter $videoFormatter
   *   The video formatter.
   * @param \Drupal\studiometa\Formatter\AudioFormatter $audioFormatter
   *   The audio formatter.
   */
  public function __construct(
    CacheBackendInterface $cacheBackend,
    StringHelper $stringHelper,
    LanguageManagerInterface $languageManager,
    FieldableEntityHelper $fieldableEntityHelper,
    LinkFormatter $linkFormatter,
    protected FileFormatter $fileFormatter,
    protected ImageFormatter $imageFormatter,
    protected VideoFormatter $videoFormatter,
    protected AudioFormatter $audioFormatter
  ) {
    parent::__construct($cacheBackend, $stringHelper, $languageManager, $fieldableEntityHelper, $linkFormatter);

    $this->fileFormatter = $fileFormatter;
    $this->imageFormatter = $imageFormatter;
    $this->videoFormatter = $videoFormatter;
    $this->audioFormatter = $audioFormatter;
  }

  /**
   * {@inheritdoc}
   */
  protected function formatItem($item, array $options = []): array {
    if (!$item instanceof MediaInterface) {
      throw new \InvalidArgumentException('Specified `item` must implement the NodeInterface', 1);
    }

    if ($item->hasField('field_media_image')) {
      $image = $this->fieldableEntityHelper->getFieldValue($item, 'field_media_image', NULL, 'target_id');

      // Get all values from media_image
      // to get default width, default height and alt.
      $image_values = current($item->get('field_media_image')->getValue());
      $options = array_merge($options, $image_values);

      return [
        'image' => NULL !== $image
        ? $this->imageFormatter->format($image, '', TRUE, '', $options)
        : NULL,
      ];
    }

    if ($item->hasField('field_media_document')) {
      $file = $this->fieldableEntityHelper->getFieldValue($item, 'field_media_document', NULL, 'target_id');
      return [
        'document' => !empty($file) ? $this->fileFormatter->format($file, '', TRUE, '', $options) : NULL,
      ];
    }

    if ($item->hasField('field_media_oembed_video')) {
      $remote_video = $this->fieldableEntityHelper->getFieldValue($item, 'field_media_oembed_video', NULL, 'target_id');
      return [
        'oembed_video' => !empty($remote_video) && !empty($remote_video['value']) ? ['url' => $remote_video['value']] : NULL,
      ];
    }

    if ($item->hasField('field_media_video_file')) {
      $video = $this->fieldableEntityHelper->getFieldValue($item, 'field_media_video_file', NULL, 'target_id');
      return [
        'video' => NULL !== $video ? $this->videoFormatter->format($video, '', TRUE, '', $options) : NULL,
      ];
    }

    if ($item->hasField('field_media_audio_file')) {
      $audio = $this->fieldableEntityHelper->getFieldValue($item, 'field_media_audio_file', NULL, 'target_id');
      return [
        'audio' => !empty($audio) ? $this->audioFormatter->format($audio, '', TRUE, '', $options) : NULL,
      ];
    }

    return [];
  }

}
