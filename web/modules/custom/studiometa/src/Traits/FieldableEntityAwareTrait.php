<?php

namespace Drupal\studiometa\Traits;

use Drupal\studiometa\Helper\FieldableEntityHelper;

/**
 * FieldableEntityAwareTrait class.
 */
trait FieldableEntityAwareTrait {

  /**
   * Fieldable entity helper.
   *
   * @var \Drupal\studiometa\Helper\FieldableEntityHelper
   */
  public $fieldableEntityHelper;

  /**
   * Set fieldableEntityHelper.
   *
   * @param \Drupal\studiometa\Helper\FieldableEntityHelper $fieldableEntityHelper
   *   The fieldable entity helper.
   */
  public function setFieldableEntityHelper(FieldableEntityHelper $fieldableEntityHelper): void {
    $this->fieldableEntityHelper = $fieldableEntityHelper;
  }

}
