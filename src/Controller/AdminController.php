<?php

namespace Drupal\taxons\Controller;

use Drupal\Core\Controller\ControllerBase;
/**
 * Controller routines for page example routes.
 */
class AdminController extends ControllerBase {



  /**
   * {@inheritdoc}
   */
  protected function ajaxtest() {
    return 'test';
  }

  /**
   * @return array
   */
  public function unigConfig() {

    // Default settings.
    $config = \Drupal::config('taxons.settings');
    // Page title and source text.


    return [
      '#markup' => '<p>taxons</p>'
    ];
  }

  /**
   * @return array
   */
  public function sandboxPage() {



    $output = [];

    $form['list'] = [
      '#markup' => '<p>Sandbox</p>' .
        '<hr>' .
        //
       '<div class="taxons-sandbox"><pre>' .$output[0]. '</pre></div>' .
        '<div class="taxons-sandbox"><pre>' .$output[1]. '</pre></div>' .
        '<hr>',
    ];


    return $form;
  }



}
