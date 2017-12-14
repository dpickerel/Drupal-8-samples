<?php

/**
 * @file
 * Contains \Drupal\pathauto\Form\PathautoSettingsForm.
 */

namespace Drupal\donor_forms\Form;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
define('PAYPAL_TXN_MODE_LIVE', 'live');
define('PAYPAL_TXN_MODE_SANDBOX', 'sand_box');
 
/**
 * Configure Gateway for the Donation Form payment page.
 */
class DonorSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'donations_gateway_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['donor_forms.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('donor_forms.settings');

    $form = array();
        
    $form['donations'] = array(
      '#type' => 'fieldset',
      '#title' => t('Donations Gateway'),
      '#tree' => TRUE,
    );
    $donation_data = $config->get('donations');
    $form['donations']['email'] = array(
      '#type' => 'textfield',
      '#title' => t('Admin Email'),
      '#maxlength' => 32,
      '#default_value' => $donation_data['email'],
      '#description' => t('Email to send notices to'),
    );
    
    $form['donations']['gateway'] = array(
      '#type' => 'textfield',
      '#title' => t('Gateway'),
      '#maxlength' => 32,
      '#default_value' => $donation_data['gateway'],
      '#description' => t('Gateway endpoint to use'),
    );

    $form['donations']['userid'] = array(
      '#type' => 'textfield',
      '#title' => t('User ID'),
      '#maxlength' => 32,
      '#default_value' => $donation_data['userid'],
      '#description' => t('User ID to access account. Defaults to test account'),
    );

    $form['donations']['password'] = array(
      '#type' => 'textfield',
      '#title' => t('Password'),
      '#maxlength' => 32,
      '#default_value' => $donation_data['password'],
      '#description' => t('Gateway account password. Defaults to test account'),
    );
    $donor_email = 'THANK YOU';
      $form['donor_mail'] = array(
        '#title' => t('Donor Email Text'),
        '#type' => 'textarea',
        '#default_value' => isset( $donation_data['donor_mail']) ? $donation_data['donor_mail'] : $donor_email,
    );
    $form['payments'] = array(
      '#type' => 'fieldset',
      '#title' => t('Payments Gateway'),
      '#tree' => TRUE,
    );
    $payment_data = $config->get('payments');
    
    $form['payments']['email'] = array(
      '#type' => 'textfield',
      '#title' => t('Admin Email'),
      '#maxlength' => 32,
      '#default_value' => $payment_data['email'],
      '#description' => t('Email to send notices to'),
    );
    
    $form['payments']['gateway'] = array(
      '#type' => 'textfield',
      '#title' => t('Gateway'),
      '#maxlength' => 32,
      '#default_value' => $payment_data['gateway'],
      '#description' => t('Gateway endpoint to use'),
    );
    
    $modes = array('test' => 'Test', 'prod' => 'Production');
    $form['payments']['mode'] = array(
      '#type' => 'select',
      '#title' => t('Transaction mode'),
      '#options' => array(
        PAYPAL_TXN_MODE_LIVE => t('Live transactions in a live account'),
        PAYPAL_TXN_MODE_SANDBOX => t('Sandbox'),
      ),     
      '#default_value' => $payment_data['mode'],
      '#description' => t('Paypal test or production mode'),
    );
    
    $form['payments']['userid'] = array(
      '#type' => 'textfield',
      '#title' => t('User ID'),
      '#maxlength' => 32,
      '#default_value' => $payment_data['userid'],
      '#description' => t('User ID to access account. Defaults to test account'),
    );
  
  $form['payments']['vendor'] = array(
    '#type' => 'textfield',
    '#title' => t('Vendor'),
    '#description' => t('Your merchant login ID that you created when you registered for the account.'),
    '#default_value' => $payment_data['vendor'],
    '#required' => TRUE,
  );
  
  $form['payments']['partner'] = array(
    '#type' => 'textfield',
    '#title' => t('Partner'),
    '#description' => t('The ID provided to you by the authorized PayPal Reseller who registered you for the Payflow SDK. If you purchased your account directly from PayPal, use PayPal.'),
    '#default_value' => $payment_data['partner'],
    '#required' => TRUE,
  );
  
    $form['payments']['password'] = array(
      '#type' => 'textfield',
      '#title' => t('Password'),
      '#maxlength' => 32,
      '#default_value' => $payment_data['password'],
      '#description' => t('Gateway account password. Defaults to test account'),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
 
    $config = $this->config('donor_forms.settings');

    $form_state->cleanValues();
    foreach ($form_state->getValues() as $key => $value) {
      $config->set($key, $value);
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
