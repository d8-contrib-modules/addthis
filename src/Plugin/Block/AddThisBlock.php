<?php
/**
 * @file
 * Contains \Drupal\addthis\Plugin\Block\AddThisBlock.
 */

namespace Drupal\addthis\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\addthis\AddThis;

/**
 * Provides my custom block.
 *
 * @Block(
 *   id = "addthis_block",
 *   admin_label = @Translation("AddThis"),
 *   category = @Translation("Blocks")
 * )
 */
class AddThisBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    //@TODO Implement block markup
    $markup = 'Testing Markup';
    return array(
      '#markup' => $markup,
    );
  }

  function blockForm($form, FormStateInterface $form_state) {
    //@TODO: Implement block form.
    return $form;
  }
  public function blockSubmit($form, FormStateInterface $form_state) {
    //@TODO: Implement block submit.
  }

}

?>