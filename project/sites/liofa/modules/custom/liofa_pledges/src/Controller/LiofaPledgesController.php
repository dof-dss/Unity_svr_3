<?php
namespace Drupal\liofa_pledges\Controller;

use Drupal\Core\Controller\ControllerBase;

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
    return [
      '#markup' => 'Hello, world',
    ];
  }

}
