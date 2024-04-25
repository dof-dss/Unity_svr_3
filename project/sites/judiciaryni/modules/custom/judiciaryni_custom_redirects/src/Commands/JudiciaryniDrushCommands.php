<?php

namespace Drupal\judiciaryni_custom_redirects\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
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
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct();
    $this->entityTypeManager = $entity_type_manager;
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
      // Retrieve the current URL for the file.
      $url = urldecode($file->createFileUrl());
      // Generate the old version of the URL.
      $oldpath = str_replace('files/judiciaryni/decisions','sites/judiciary/files/decisions',$url);
      $oldpath = str_replace('files/judiciaryni/media-files','sites/judiciary/files/media-files',$oldpath);
      $oldpath = str_replace('files/judiciaryni/2024-02','sites/judiciary/files/media-files',$oldpath);
      $oldpath = str_replace('files/judiciaryni/2024-01','sites/judiciary/files/media-files',$oldpath);
      // Only create the redirect if the url has been changed.
      if ($oldpath != $url) {
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
    }
    if ($total > 0) {
      print("Successfully created $total redirects\n");
    } else {
      print("Did not create any redirects (perhaps they already exist ?\n");
    }
  }
}

