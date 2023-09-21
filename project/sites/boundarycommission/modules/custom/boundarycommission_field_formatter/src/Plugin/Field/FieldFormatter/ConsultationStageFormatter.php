<?php

namespace Drupal\boundarycommission_field_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'Consultation Stage' formatter.
 *
 * @FieldFormatter(
 *   id = "consultation_stage_formatter",
 *   label = @Translation("Unity consultation stage field formatter"),
 *   field_types = {
 *     "entity_reference"
 *   },
 * )
 */
class ConsultationStageFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'taxonomy_url_path' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    // TODO: There is no form validator call for plugin settings forms,
    // possibly look at using an ajax callback to validate CSS classes.
    $elements['taxonomy_url_path'] = [
      '#title' => $this->t('Taxonomy path'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('taxonomy_url_path'),
      '#description' => $this->t('Add the url to replace for the taxonomy path with. Taxonomy path URL without
                                        the site URL and taxonomy ID e.g. /representations-received/type/'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Taxonomy URL path: @url_path', ['@url_path' => $this->getSetting('taxonomy_url_path')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $settings = $this->getSettings();

    $element['taxonomy_url_path'] = $settings['taxonomy_url_path'];

    foreach ($items as $delta => $item) {
      // Get the taxonomy tid for this field.
      $tid = $item->target_id;
      if (!empty($tid)) {
        // Load up the taxonomy term so that we can get the name.
        $term = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->load($tid);
        // Build the link to return to the consultations page with this term selected.
        $element[$delta] = ['#markup' => '<a href="' . $element['taxonomy_url_path'] . $tid . '">' . $term->label() . '</a>'];
      }
    }

    return $element;
  }

}
