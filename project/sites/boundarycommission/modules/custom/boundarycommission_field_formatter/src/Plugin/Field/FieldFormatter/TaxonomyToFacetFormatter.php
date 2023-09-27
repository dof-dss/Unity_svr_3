<?php

namespace Drupal\boundarycommission_field_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\facets\Entity\Facet;

/**
 * Plugin implementation of the 'Consultation Stage' formatter.
 *
 * @FieldFormatter(
 *   id = "Map taxonomy term to facet",
 *   label = @Translation("Unity taxonomy term to facet formatter"),
 *   field_types = {
 *     "entity_reference"
 *   },
 * )
 */
class TaxonomyToFacetFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'search_page_url' => '',
        'search_index' => ''
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    // TODO: There is no form validator call for plugin settings forms,
    // possibly look at using an ajax callback to validate CSS classes.

    /** @var \Drupal\facets\FacetManager\DefaultFacetManager $facet_manager */
    $facet_manager = \Drupal::service('facets.manager');

    $facets = $facet_manager->getEnabledFacets();
    $active_facets = [];
    foreach ($facets as $facet => $info) {
      $active_facets[$facet] .= $info->id();
    }
    $elements['search_page_url'] = [
      '#title' => $this->t('Search page URL ending'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('search_page_url'),
      '#description' => $this->t('Add the url ending of the search page. e.g. If the the search page is http://www.test.co.uk/search-page then simply enter search-page.'),
      '#required' => TRUE,
    ];
    $elements['search_index'] = [
      '#type' => 'select',
      '#title' => t('Related facet'),
      '#description' => t('The facet you want to link this term to.'),
      '#options' => $active_facets,
      "#default_value" => $this->getSetting('search_index'),
      '#required' => TRUE,
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Facet: @search_page_url', ['@search_page_url' => $this->getSetting('search_page_url')]);
    $summary[] = $this->t('Search page URL: @search_index', ['@search_index' => $this->getSetting('search_index')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $settings = $this->getSettings();

    $facet = Facet::load($settings['search_index']);
    $facet_pretty_path_url = $facet->get('url_alias');

    $element['search_page_url'] = $settings['search_page_url'];

    foreach ($items as $delta => $item) {
      // Get the taxonomy tid for this field.
      $tid = $item->target_id;
      if (!empty($tid)) {
        // Load up the taxonomy term so that we can get the name.
        $term = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->load($tid);
        // Build the link to return to the consultations page with this term selected.
        $element[$delta] = ['#markup' => '<a href="/' . $element['search_page_url'] . '/' . $facet_pretty_path_url . '/' . $tid . '">' . $term->label() . '</a>'];
      }
    }

    return $element;
  }

}
