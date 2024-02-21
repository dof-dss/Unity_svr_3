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
   * Count storage.
   */
  protected $onsite_pledges;
  protected $bulk_pledge_count;
  protected $pledge_count_offset;

  /**
   * Constructs the LiofaPledgesController object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->config = $this->configFactory->get('online_pledge_count.countsettings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function pledgesSummary() {
    $this->generateTotals();
    // Calculate overall total.
    $total = $this->onsite_pledges + $this->bulk_pledge_count + $this->pledge_count_offset;
    // Output table.
    $table_html = '<table id="pledges-table">
            <thead><tr><th>Component</th><th class="right">Value</th> </tr></thead>
            <tbody>
             <tr class="odd"><td>Pledges submitted online</td><td class="right">' . $this->onsite_pledges . '</td> </tr>
             <tr class="even"><td>Bulk pledges</td><td class="right">' . $this->bulk_pledge_count . '</td> </tr>
             <tr class="odd"><td>Pledge count offset</td><td class="right">' . $this->pledge_count_offset . '</td> </tr>
             <tr class="even"><td class="total">Total</td><td class="right total">' . $total . '</td> </tr>
            </tbody>
      </table>';
    return [
      '#markup' => 'The table below shows a breakdown of the pledge count value shown on the home page.' . $table_html,
    ];
  }

  protected function generateTotals() {
    // Retrieve pledge count submitted online.
    $this->onsite_pledges = intval($this->config->get('pledge_count_submissions'));
    // Now need to get bulk pledges count.
    $this->bulk_pledge_count = 0;
    // \Drupal::entityQueryAggregate('node')
    $result = $this->entityTypeManager->getStorage('node')->getAggregateQuery('AND')
      ->accessCheck(FALSE)
      ->aggregate('field_bulk_number', 'sum')
      ->condition('type', 'bulk_pledges')
      ->condition('status', NodeInterface::PUBLISHED)
      ->execute();
    if (!empty($result) && is_array($result)) {
      $this->bulk_pledge_count = $result[0]['field_bulk_number_sum'];
    }
    // Get pledge count offset.
    $this->pledge_count_offset = intval($this->config->get('pledge_count_offset'));
    if (empty($this->pledge_count_offset)) {
      $this->pledge_count_offset = 0;
    }
  }

}
