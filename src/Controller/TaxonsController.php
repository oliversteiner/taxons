<?php

namespace Drupal\taxons\Controller;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller routines for page example routes.
 */
class TaxonsController extends ControllerBase
{
  /**
   * {@inheritdoc}
   */
  protected function getModuleName()
  {
    return 'taxons';
  }

  /**
   * @param $target_nid
   * @param $term_tid
   * @param $field_name
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public static function toggleTag($target_nid, $term_tid, $field_name, $values)
  {
    $result = self::_toggleTag($target_nid, $term_tid, $field_name, $values);

    $response = new AjaxResponse();
    $selector = '#' . $field_name . '-' . $target_nid . '-' . $term_tid;

    if ($values === 1) {
      // remove all
      $selector_all =
        '.' . $field_name . '-' . $target_nid . '.vat-toggle-tag-single';

      $response->addCommand(
        new InvokeCommand($selector_all, 'removeClass', ['active'])
      );

      // activate new
      $response->addCommand(
        new InvokeCommand($selector, 'addClass', ['active'])
      );
    } else {
      if ($result['mode'] == 'add') {
        $response->addCommand(
          new InvokeCommand($selector, 'addClass', ['active'])
        );
      } elseif ($result['mode'] == 'remove') {
        $response->addCommand(
          new InvokeCommand($selector, 'removeClass', ['active'])
        );
      } else {
        $message = 'Error: Unknown result mode';
        $response->addCommand(
          new ReplaceCommand(
            '.ajax-container',
            '<div class="ajax-container">' . $message . '</div>'
          )
        );
      }
    }
    return $response;
  }

  /**
   * @param $target_nid
   * @param $term_tid
   * @param $field_name
   *
   * @param $value
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function _toggleTag($target_nid, $term_tid, $field_name, $value)
  {
    $output = [
      'status' => false,
      'mode' => false,
      'nid' => $target_nid,
      'tid' => $term_tid,
      'field_name' => $field_name,
    ];

    // Load Node
    try {
      $entity = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($target_nid);
    } catch (InvalidPluginDefinitionException $e) {
    }

    // Field OK?
    if ($entity !== null && !empty($entity->$field_name)) {
      // Load all items
      $all_terms = $entity->get($field_name)->getValue();

      // take only tid
      $arr_item_id = [];
      foreach ($all_terms as $item) {
        $arr_item_id[] = $item['target_id'];
      }

      $item_id_unique = array_unique($arr_item_id); // performace?
      $position = array_search($term_tid, $item_id_unique, false);

      if ($position !== false) {
        // Remove Term
        unset($item_id_unique[$position]);

        $output['mode'] = 'remove';
      } else {
        // Add Term
        $item_id_unique[] = $term_tid;

        $output['mode'] = 'add';
      }

      // Apply Term changes
      $entity->$field_name->setValue($item_id_unique);
      $entity->save();
      $output['status'] = true;
    }

    return $output;
  }
}
