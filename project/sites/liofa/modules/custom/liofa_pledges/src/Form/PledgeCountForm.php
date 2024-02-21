<?php

namespace Drupal\liofa_pledges\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements admin form to allow setting of audit text.
 */
class pledgeCountForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'liofa_pledges.countsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'liofa_pledges';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('liofa_pledges.countsettings');

    $message_online = "For data protection reasons, pledges submitted via the website are deleted after 3 days. The website maintains";
    $message_online .= "<br/>a count of all submitted pledges, deleted and not deleted. On very rare occasions, the count might get out of sync. ";
    $message_online .= "<br/>If that happens, you can reset the submission count to the correct value by entering the value in the field below.";

    $message_offset = "The Líofa website shows a count of all pledges received. The count is the sum of: ";
    $message_offset .= "<br/><ul><li>Pledges submitted via the website</li>";
    $message_offset .= "<br/><li>Pledges that were collected elsewhere and uploaded via the <a href='/admin/content/bulk-pledges'>bulk pledges upload page.</a></li></ul>";
    $message_offset .= '<br/>You can manually increase or decrease the total shown on the website by adding an offset value here.';

    $form['pledge_count_submissions'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Pledges submitted via the website'),
      '#size' => 10,
      '#required' => TRUE,
      '#description' => $this->t($message_online),
      '#default_value' => $config->get('pledge_count_submissions'),
    ];

    $form['pledge_count_offset'] = [
      '#type' => 'textfield',
      '#title' => $this->t('A manual offset to be applied to the pledge count on the website.'),
      '#size' => 10,
      '#required' => TRUE,
      '#description' => $this->t($message_offset),
      '#default_value' => $config->get('pledge_count_offset'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!is_numeric($form_state->getValue('pledge_count_submissions'))) {
      $form_state->setErrorByName('pledge_count_submissions', $this->t("The value must be numeric."));
    }
    elseif ($form_state->getValue('pledge_count_submissions') < 0) {
      $form_state->setErrorByName('pledge_count_submissions', $this->t("The number of submissions must be greater than or equal to zero."));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('liofa_pledges.countsettings')
      ->set('pledge_count_submissions', $form_state->getValue('pledge_count_submissions'))
      ->set('pledge_count_offset', $form_state->getValue('pledge_count_offset'))
      ->save();
  }

}
