<?php

 /**
 * @file
 * Contains \Drupal\block_example\Plugin\Block\DonorDonationsBlock.
 */
 
namespace Drupal\donor_forms\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;


/**
 * Provides a 'Donor Forms' Block
 *
 * @Block(
 *   id = "donation_form_block",
 *   admin_label = @Translation("Donation Form Block"),
 * )
 */
class DonorDonationsBlock extends BlockBase implements BlockPluginInterface {
    /**
     * {@inheritdoc}
     */
    public function build() {
        $config = $this->getConfiguration();

        if (!empty($config['gateway'])) {
            $gateway = $config['gateway'];
        }
        else {
            $gateway = $this->t('none set');
        }
        return \Drupal::formBuilder()->getForm('Drupal\donor_forms\Form\DonorDonationForm');
    }
    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);

        $config = $this->getConfiguration();

        $form['donor_forms_block_gateway'] = array (
            '#type' => 'textfield',
            '#title' => $this->t('Gateway'),
            '#description' => $this->t('Enter gateway login information'),
            '#default_value' => isset($config['gateway']) ? $config['gateway'] : 'Gateway 1',
        );

        $form['donor_forms_block_gateway_user'] = array (
            '#type' => 'textfield',
            '#title' => $this->t('Gateway Userid'),
            '#description' => $this->t('Enter gateway login User ID'),
            '#default_value' => isset($config['user']) ? $config['user'] : 'User 1',
        );
        $form['donor_forms_block_gateway_password'] = array (
            '#type' => 'textfield',
            '#title' => $this->t('Gateway Password'),
            '#description' => $this->t('Enter gateway login password'),
            '#default_value' => isset($config['password']) ? $config['password'] : 'Password 1',
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        $default_config = \Drupal::config('donor_forms.settings');
        return array(
            'gateway' => $default_config->get('donor.gateway'),
            'user' => $default_config->get('donor.user'),
            'password' => $default_config->get('donor.password')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $this->setConfigurationValue('gateway', $form_state->getValue('donor_forms_block_gateway'));
        $this->setConfigurationValue('user', $form_state->getValue('donor_forms_block_gateway_user'));
        $this->setConfigurationValue('password', $form_state->getValue('donor_forms_block_gateway_password'));
    }


}