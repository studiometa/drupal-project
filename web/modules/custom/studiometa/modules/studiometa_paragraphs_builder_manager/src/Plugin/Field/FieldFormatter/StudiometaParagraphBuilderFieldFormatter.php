<?php

namespace Drupal\studiometa_blocks_manager\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\studiometa\Manager\Paragraph\ParagraphManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the studiometa_paragraph_builder_field_formatter.
 *
 * @FieldFormatter(
 *   id = "studiometa_paragraph_builder_field_formatter",
 *   label = @Translation("Studiometa Paragraph Builder Formatter"),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
final class StudiometaParagraphBuilderFieldFormatter extends FormatterBase implements ContainerFactoryPluginInterface {
  // phpcs:disable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * Constructs a StringFormatter instance.
   *
   * {@inheritdoc}
   *
   * @param \Drupal\studiometa\Manager\Paragraph\ParagraphManager $paragraphManager
   *   The paragraph manager.
   */
  public function __construct(
      $plugin_id,
      $plugin_definition,
      FieldDefinitionInterface $field_definition,
      array $settings,
      $label,
      $view_mode,
      array $third_party_settings,
      protected ParagraphManager $paragraphManager,
    ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->paragraphManager = $paragraphManager;
  }

  // phpcs:enable Drupal.Commenting.FunctionComment.ParamMissingDefinition

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('studiometa.paragraph_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] =
      $this->t('Récupère le formatter associé au type de paragraph du champ et génère le template associé.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      $entity = $item->entity;
      if (empty($entity)) {
        continue;
      }

      $formatter = $this->paragraphManager->getFormatter($entity, 'studiometa', '_builder');

      if (is_null($formatter)) {
        continue;
      }

      $element[$delta] = $formatter->format($entity);
    }

    return $element;
  }

}
