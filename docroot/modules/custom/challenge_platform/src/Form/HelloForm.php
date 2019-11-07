<?php

namespace Drupal\challenge_platform\Form;

use Drupal\node\NodeInterface;
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

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Please enter the title.'),
    ];

    $form['submit_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Submission Name'),
      '#description' => $this->t('Enter the submission´s name'),
      '#required' => TRUE,
    ];

    $form['lead_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Team Lead Name'),
      '#description' => $this->t('Enter the team lead name'),
      '#required' => TRUE,
    ];

    $validators = [
      'file_validate_extensions' => ['pdf'],
    ];

    $form['pdf_file'] = [
      '#type' => 'managed_file',
      '#name' => 'submission_document',
      '#title' => t('Submission PDF'),
      '#size' => 80,
      '#description' => t('PDF format only'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://docroot/sites/default/files/pdf',
    ];

    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Body'),
      '#description' => $this->t('Enter your proposal for this campaing'),
      '#required' => TRUE,
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
    $name_form = $form_state->getValue('submit_name');
    $lead_name_form = $form_state->getValue('lead_name');
    $title_form = $campaing_form . ' ' . $name_form;
    $doc_form = $form_state->getValue('pdf_file');
    $body_form = $form_state->getValue('body');

    Node::create([
      'type' => 'submission',
      'title' => $title_form,
      'field_campaing' => $value_campaing,
      'field_lead_name' => $lead_name_form,
      'field_submission_name' => $name_form,
      'field_submission_document' => $doc_form,
      'body' => $body_form,
    ])->save();

  }

}

/**
 * Form campaings provider returns value_campaing values.
 */
function get_campaing_values() {
  $node = \Drupal::routeMatch()->getParameter('node');
  if ($node instanceof NodeInterface) {
    $nid = $node->id();
  }

  return $nid;
}
