<?php

namespace Drupal\liofa_pledges\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBaseTrait;
use Drupal\node\NodeInterface;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides route responses for the Example module.
 */
class LiofaPledgesController extends ControllerBase {

  /**
   * The config for pledge counts.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Constructs the LiofaPledgesController object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
    $this->config = $this->configFactory->getEditable('liofa_pledges.countsettings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function pledgesSummary() {
    // Output table.
    $table_html = '<table id="pledges-table">
            <thead><tr><th>Component</th><th class="right">Value</th> </tr></thead>
            <tbody>
             <tr class="odd"><td>Pledges submitted online</td><td class="right">' . $this->config->get('onsite_pledges') . '</td> </tr>
             <tr class="even"><td>Bulk pledges</td><td class="right">' . $this->config->get('bulk_pledge_count') . '</td> </tr>
             <tr class="odd"><td>Pledge count offset</td><td class="right">' . $this->config->get('pledge_count_offset') . '</td> </tr>
             <tr class="even"><td class="total">Total</td><td class="right total">' . $this->config->get('pledge_count_total') . '</td> </tr>
            </tbody>
      </table>';
    return [
      '#markup' => 'The table below shows a breakdown of the pledge count value shown on the home page.' . $table_html,
    ];
  }

  /**
   * Generate pledge count totals and store in config.
   */
  public static function generateTotals() {
    $config = \Drupal::configFactory()->getEditable('liofa_pledges.countsettings');
//    $config->set('bulk_pledge_count', 0)->save();
    // Retrieve pledge count submitted online.
    $onsite_pledges = intval($config->get('pledge_count_submissions'));
    // Now need to get bulk pledges count.
//    $bulk_pledge_count = 0;
//    $result = \Drupal::entityTypeManager()->getStorage('node')->getAggregateQuery('AND')
//      ->accessCheck(FALSE)
//      ->aggregate('field_bulk_number', 'sum')
//      ->condition('type', 'bulk_pledges')
//      ->condition('status', NodeInterface::PUBLISHED)
//      ->execute();
//    if (!empty($result) && is_array($result)) {
//      $bulk_pledge_count = $result[0]['field_bulk_number_sum'];
//    }
    // Get pledge count offset.
    $pledge_count_offset = intval($config->get('pledge_count_offset'));
    if (empty($pledge_count_offset)) {
      $pledge_count_offset = 0;
    }
    // Calculate overall total. Bulk pledge count is calculated in liofa_pledges.module.
    $pledge_count_total = $onsite_pledges + $config->get('bulk_pledge_count') + $pledge_count_offset;
    $config->set('onsite_pledges', $onsite_pledges)->save();
//    $config->set('bulk_pledge_count', $bulk_pledge_count)->save();
    $config->set('pledge_count_total', $pledge_count_total)->save();
  }

}
