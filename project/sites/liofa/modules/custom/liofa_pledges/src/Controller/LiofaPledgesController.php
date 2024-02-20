<?php
namespace Drupal\liofa_pledges\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\views\Views;

/**
 * Provides route responses for the Example module.
 */
class LiofaPledgesController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function pledgesSummary() {
    $config = \Drupal::configFactory()->get('online_pledge_count.countsettings');
    // Retrieve pledge count submitted online.
    $onsite_pledges = intval($config->get('pledge_count_submissions'));
    // Now need to get bulk pledges count.
    $bulk_pledge_count = 0;
    $result = \Drupal::entityQueryAggregate('node')
      ->accessCheck(FALSE)
      ->aggregate('field_bulk_number', 'sum')
      ->condition('type', 'bulk_pledges')
      ->condition('status', NodeInterface::PUBLISHED)
      ->execute();
    if (!empty($result) && is_array($result)) {
      $bulk_pledge_count = $result[0]['field_bulk_number_sum'];
    }
    object_log('totals', $result);

    $table_html = '<table id="pledges-table" class="sticky-enabled tableheader-processed sticky-table">
            <thead><tr><th>Component</th><th class="right">Value</th> </tr></thead>
            <tbody>
             <tr class="odd"><td>Pledges submitted online</td><td class="right">' . $onsite_pledges . '</td> </tr>
             <tr class="even"><td>Bulk pledges</td><td class="right">' . $bulk_pledge_count . '</td> </tr>
             <tr class="odd"><td>Pledge count offset</td><td class="right">1,000</td> </tr>
             <tr class="even"><td class="total">Total</td><td class="right total">26,258</td> </tr>
            </tbody>
      </table>';
    return [
      '#markup' => 'The table below shows a breakdown of the pledge count value shown on the home page.' . $table_html,
    ];
  }

}
