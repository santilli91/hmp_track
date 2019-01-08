<?php

namespace Drupal\hmp_track\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use \Drupal\node\Entity\Node;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Block for ad positions.  Do not use HTML ads in combination with big pipe. 
 * If HTML ads are needed + Big Pipe, embed ads directly in template 
 *
 * @Block(
 *   id = "hmp_ad_block",
 *   admin_label = @Translation("Ad Position"),
 * )
 */
class AdBlock extends BlockBase implements BlockPluginInterface {

  /**
 * {@inheritdoc}
 */
public function blockForm($form, FormStateInterface $form_state) {
  $form['zid'] = array(
    '#type' => 'textfield',
    '#title' => $this->t('ZID'),
    '#description' => $this->t('Zone ID'),
    '#default_value' => isset($this->configuration['zid']) ? $this->configuration['zid'] : '',
    '#maxlength' => 12,
    '#size' => 12,
    '#weight' => '0',
    '#required' => true,
  );
  $form['ad_type'] = array(
    '#type' => 'select',
    '#title' => $this->t('Ad Type'),
    '#description' => $this->t('Ad Type - AJAX / HTML'),
    '#default_value' => isset($this->configuration['ad_type']) ? $this->configuration['ad_type'] : '',
    '#options' => array(
      'ajax' => 'AJAX',
      'html' => 'HTML',
    ),
    '#multiple' => false,
    '#required' => true,
  );
  $form['ad_size'] = array(
    '#type' => 'select',
    '#title' => $this->t('Ad Size'),
    '#description' => $this->t('Ad Size - Used for Proclivity'),
    '#default_value' => isset($this->configuration['ad_size']) ? $this->configuration['ad_size'] : '',
    '#options' => array(
      '0' => 'N/A',
      '300x250' => '300x250',
      '728x90' => '728x90',
    ),
    '#multiple' => false,
    '#required' => true,
  );

  return $form;
}

/**
 * {@inheritdoc}
 */
public function blockSubmit($form, FormStateInterface $form_state) {
  $this->configuration['zid'] = $form_state->getValue('zid');
  $this->configuration['ad_size'] = $form_state->getValue('ad_size');
  $this->configuration['ad_type'] = $form_state->getValue('ad_type');
}

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $output = '';
    if (!empty($config['zid'])) {
      $output = hmp_track_get_adcode($config['zid'],$config['ad_type'],$config['ad_size']);
    }
    return array(
      '#markup' => $this->t($output),
      '#cache' => [
      	'max-age' => 0,
      ],
    );
  }

/* */
}?>