<?php

namespace Drupal\judiciaryni_custom_redirects\Commands;

use Drupal\structure_sync\StructureSyncHelper;
use Drupal\redirect\Entity\Redirect;
use Drush\Commands\DrushCommands;

/**
 * Drush custom commands.
 */
class JudiciaryniDrushCommands extends DrushCommands {

  /**
   * Core EntityTypeManager instance.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->entityTypeManager = \Drupal::entityTypeManager();
  }

  /**
   * Drush command to generate old style redirects (sites/judiciary/files)
   * for Drupal 10 style paths (files/judiciaryni).
   *
   * @command judiciary-generate-old-redirects
   */
  public function generateOldStyleRedirects() {
    $storage = $this->entityTypeManager->getStorage('file');
    $fids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->execute();
    $files = $storage->loadMultiple($fids);
    $total = 0;
    foreach ($files as $file) {
      $url = urldecode($file->createFileUrl());
      $oldpath = str_replace('files/judiciaryni/decisions','sites/judiciary/files/decisions',$url);
      $oldpath = str_replace('files/judiciaryni/media-files','sites/judiciary/files/media-files',$url);
      // Check to see if a redirect already exists.
      $redirects = $this->entityTypeManager->getStorage('redirect')->getQuery()
        ->accessCheck(TRUE)
        ->condition('redirect_source.path', substr($oldpath,1))
        ->execute();
      if (empty($redirects)) {
        // Create the new redirect.
        Redirect::create([
          'redirect_source' => substr($oldpath,1),
          'redirect_redirect' => 'internal:' . $url,
          'language' => 'und',
          'status_code' => '301',
        ])->save();
        $total++;
      }
    }
    if ($total > 0) {
      print("Successfully created $total redirects\n");
    } else {
      print("Did not create any redirects (perhaps they already exist ?\n");
    }
  }
}

