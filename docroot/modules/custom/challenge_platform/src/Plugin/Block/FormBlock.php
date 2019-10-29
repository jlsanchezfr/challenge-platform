<?php

namespace Drupal\challenge_platform\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Form' block.
 *
 * @Block(
 *   id = "challenge_form_block",
 *   admin_label = @Translation("Project form"),
 *   category = @Translation("Custom challenge block")
 * )
 */
class FormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\challenge_platform\Form\HelloForm');
    return $form;
  }

}
