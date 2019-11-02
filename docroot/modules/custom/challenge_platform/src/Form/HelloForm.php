<?php

namespace Drupal\challenge_platform\Form;

use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * HelloForm controller.
 */
class HelloForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'ChallengePlatform_hello_form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $value_campaing = get_campaing_values();

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Please enter the title and accept the terms of use of the site.'),
    ];

    $form['campaing_options'] = [
      '#type' => 'value',
      '#value' => $value_campaing,
    ];

    $form['campaing'] = [
      '#type' => 'select',
      '#title' => 'Campaing',
      '#options' => $form['campaing_options']['#value'],
      '#description' => $this->t('Select what campaing you want to apply'),
      '#required' => TRUE,
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#description' => $this->t('Enter your name'),
      '#required' => TRUE,
    ];

    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Body'),
      '#description' => $this->t('Enter your proposal for this campaing'),
      '#required' => TRUE,
    ];

    $form['accept'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I accept the terms of use of the site'),
      '#description' => $this->t('Please read and accept the terms of use'),
    ];

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;

  }

  /**
   * Validate the title and the checkbox of the form.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $name = $form_state->getValue('name');
    $accept = $form_state->getValue('accept');

    if (strlen($name) < 10) {
      // Set an error for the form element with a key of "title".
      $form_state->setErrorByName('name', $this->t('The title must be at least 10 characters long.'));
    }

    if (empty($accept)) {
      // Set an error for the form element with a key of "accept".
      $form_state->setErrorByName('accept', $this->t('You must accept the terms of use to continue'));
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $value_campaing = get_campaing_values();

    // Redirect to home.
    $form_state->setRedirect('<front>');

    $campaing_form = $form_state->getValue('campaing');
    $campaing_form = intval($campaing_form);
    $campaing_form = $value_campaing[$campaing_form];
    $name_form = $form_state->getValue('name');
    $title_form = $campaing_form . ' ' . $name_form;
    $body_form = $form_state->getValue('body');

    Node::create([
      'type' => 'submission',
      'title' => $title_form,
      'field_campaing' => $campaing_form,
      'field_name' => $name_form,
      'body' => $body_form,
    ])->save();

  }

}

/**
 * Form campaings provider returns value_campaing values.
 */
function get_campaing_values() {
  $query = \Drupal::entityQuery('taxonomy_term');
  $query->condition('vid', 'campaing_1');
  $tids = $query->execute();
  $terms = Term::loadMultiple($tids);

  foreach ($terms as $term) {
    $tid = $term->id();
    $value_campaing[$tid] = $term->getName();
  }
  return $value_campaing;
}
