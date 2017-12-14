<?php

/**
 * @file
 * Contains \Drupal\donor_forms\Form\BlockFormController
 */

namespace Drupal\donor_forms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
define('COMMERCE_CREDIT_AUTH_ONLY', 'A');
define('COMMERCE_CREDIT_AUTH_CAPTURE', 'S');
    
/**
 * Lorem Ipsum block form
 */
class DonorPhysicianPaymentForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'donor_donation_block_form';
    }

    /**
     * {@inheritdoc}
     * Lorem ipsum generator block.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {

      
    $form['guarantor'] = array(
      '#type' => 'fieldset',
      '#title' => t('Statement of Account Information'),
      '#tree' => TRUE,
    );
        $form['guarantor']['account'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('1. Guarantor Account ID:'),
            '#required' => TRUE,
        );
        $form['guarantor']['name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('2. Guarantor Name: '),
            '#required' => TRUE,
        );

        $form['guarantor']['date'] = array(
            '#type' => 'date',
            '#title' => $this->t('3. Statement Date:'),
            '#required' => TRUE,
        );

        $form['guarantor']['payment'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('4. Payment Amount:'),
            '#required' => TRUE,
        );
    $form['billing'] = array(
      '#type' => 'fieldset',
      '#title' => t('Billing Information'),
      '#tree' => TRUE,
    );


        $form['billing']['first'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('First Name:'),
            '#required' => TRUE,
        );
  
        $form['billing']['last'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Last Name:'),
            '#required' => TRUE,
        );
        $form['billing']['email'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Email:'),
            '#required' => TRUE,
        );
        $form['billing']['address'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Address'),
            '#required' => TRUE,
        );
        $form['billing']['address2'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Address 2'),
        );
        $form['billing']['city'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('City'),
            '#required' => TRUE,
        );
        $form['billing']['state'] = array(
            '#type' => 'select',
            '#title' => $this->t('State'),
            '#options' => $this->usstates,
            '#required' => TRUE,
        );
        $form['billing']['zip'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Zip'),
            '#required' => TRUE,
        );
        $form['billing']['phone'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Phone:'),
            '#required' => TRUE,
        );        
    $form['ccinfo'] = array(
      '#type' => 'fieldset',
      '#title' => t('Credit Card Information:'),
      '#tree' => TRUE,
    );        
    
      $active = array(0 => t('American Express'), 1 => t('Discovery'), 2 => t('MasterCard'), 3 => t('Visa'));

      $form['ccinfo']['type'] = array(
        '#type' => 'select',
        '#title' => t('Credit Card Type:'),
        '#options' => $active,
        '#required' => TRUE,
      );
      $form['ccinfo']['ccno'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Credit Card Number: '),
            '#required' => TRUE,
        );
      $form['ccinfo']['cvv'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Security Code: '),
            '#description' => t('3 or 4 digits printed on the back of the card)'),
            '#required' => TRUE,
        );        
      $form['ccinfo']['month'] = array(
            '#type' => 'select',
            '#options' => $this->months,
            '#title' => t('Expiration Date:'),
            '#required' => TRUE,
        );
             $form['ccinfo']['year'] = array(
            '#type' => 'select',
            '#options' => $this->years,
            '#required' => TRUE,
        );
        
        // Submit
      $form['submit'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Submit'),
      );

        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {

        module_load_include('php', 'donor_forms', 'lib/ccvalidate');
        
 
          if(empty($form_state->getValue('guarantor')['payment'])){
             $form_state->setErrorByName('guarantor][payment', t('Please specify a payment amount.'));
          };        

          if(empty($form_state->getValue('ccinfo')['ccno'])){
             $form_state->setErrorByName('ccinfo][ccno', t('You have to specify a valid credit card number.'));
          };
          if(drupal_forms_validate_credit_card_number($form_state->getValue('ccinfo')['ccno']) == FALSE){
            $form_state->setErrorByName('ccinfo][ccno', t('You have to specify a valid credit card number.'));
          }
          if(empty($form_state->getValue('ccinfo')['cvv'])){
             $form_state->setErrorByName('ccinfo][cvv', t('Please specify a valid cvv number.'));
          };
          if(drupal_forms_validate_cvv($form_state->getValue('ccinfo')['ccno'],$form_state->getValue('ccinfo')['cvv']) == FALSE){
             $form_state->setErrorByName('ccinfo][cvv', t('Please specify a valid cvv number.'));
          }
          
          if($error = drupal_forms_validate_credit_card_cvv($form_state->getValue('ccinfo')['month'], $form_state->getValue('ccinfo')['year']) !== TRUE){
             if($error == 'month'){
                $form_state->setErrorByName('ccinfo][month', t('The expiration date is set early than today.'));
             } else {
               $form_state->setErrorByName('ccinfo][month', t('The year is invalid.'));
             }
          }
    }

    /**
     * {@inheritdoc}
     */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    module_load_include('php', 'donor_forms', 'lib/payflow');

    $guarantor_array = $form_state->getValues();
     $charge = $form_state->getValue('guarantor')['payment'];
     $firstname = substr($form_state->getValue('billing')['first'], 0, 30);
     $lastname =  substr($form_state->getValue('billing')['last'], 0, 30);
    $nvp = array(
      'FIRSTNAME'     => $firstname,
      'LASTNAME'      => $lastname,
      'COMPANYNAME'   => '',
      'STREET'        => substr($form_state->getValue('billing')['address'], 0, 30),
      'CITY'          => substr($form_state->getValue('billing')['city'], 0, 30),
      'STATE'         => substr($form_state->getValue('billing')['state'], 0, 30),
      'ZIP'           => substr($form_state->getValue('billing')['zip'], 0, 12),
      'COUNTRY'       => 'US',
      'EMAIL'         => substr($form_state->getValue('billing')['email'], 0, 255),
      'CUSTCODE'      => '1',
      'CUSTIP'        => payflow_ip_address(),
      // A - Authorize, S - Auth and Capture
      'TRXTYPE' => 'A',
      // C - Credit card
      'TENDER'  => 'C',
      // The total price
      'AMT'     =>  $form_state->getValue('guarantor')['payment'],
      // The currency code
      'CURRENCY' => 'USD',
      // The credit card number
      'ACCT'    => $form_state->getValue('ccinfo')['ccno'],
      // The expiration date, in format 'mmyy'
      'EXPDATE' => $form_state->getValue('ccinfo')['month'] . $form_state->getValue('ccinfo')['year'],

      'CVV2' => $form_state->getValue('ccinfo')['cvv'],
      // The order number
      'INVNUM' => time(),
    );

    $new_page_values = array();
    $new_page_values['field_payment_confirmation_code'] = drupal_forms_payflow_pro_submit($nvp, $charge);
          

    $new_page_values['body'] = '';
    $new_page_values['type'] = 'physician_payment';
    $new_page_values['title'] = $form_state->getValue('billing')['first'] . ' ' . $form_state->getValue('billing')['last'] ;
    $new_page_values['field_s'] = $form_state->getValue('guarantor')['date'];
    $new_page_values['field_g'] = $form_state->getValue('guarantor')['name'];
    $new_page_values['field_guarantor_account_id'] = $form_state->getValue('guarantor')['account'];
    $new_page_values['field_p'] = $form_state->getValue('guarantor')['payment'];

    $new_page_values['field_f'] = $form_state->getValue('billing')['first'];
    $new_page_values['field_last_name'] = $form_state->getValue('billing')['last'];

    $new_page_values['field_billing_address'] = $form_state->getValue('billing')['address'];
    $new_page_values['field_billing_address_2'] = $form_state->getValue('billing')['address2'];
    $new_page_values['field_c'] = $form_state->getValue('billing')['city'];
    $new_page_values['field_state'] = $form_state->getValue('billing')['state'];
    $new_page_values['field_z'] = $form_state->getValue('billing')['zip'];
    $new_page_values['field_billing_phone'] = $form_state->getValue('billing')['phone'];

    $new_page = entity_create('node', $new_page_values);
    $return = $new_page->save();
    $message = array();
    $params = array();
    drupal_set_message(t('Thank you for your payment.'), 'status');
    $this->donor_admin_mail($form_state->getValue('billing')['first'] . ' ' . $form_state->getValue('billing')['last']);
 
    }
    /**
     * @param $info
     * create email to accounting staff
     */
    private function donor_admin_mail($info){
        global $base_url;
        $config = \Drupal::config('donor_forms.settings');
        $payment_data = $config->get('payments');
        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'donor_forms';
        $key = 'donor_forms';
        $to = $payment_data['email'];
        $params['message'] = 'A physician payment has just been processed. You can see all payments at ' . $base_url . '/admin/reports/physician-payments';
        $params['node_title'] = $info;
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $send = true;
        $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
        if ($result['result'] !== true) {
            $message = t('An email notification has failed');
            \Drupal::logger('donor_forms')->notice($message);
        }
    }

public $months = array(
  '01' => 'Jan',
  '02' => 'Feb',
  '03' => 'Mar',
  '04' => 'Apr',
  '05' => 'May',
  '06' => 'Jun',
  '07' => 'Jul',
  '08' => 'Aug',
  '09' => 'Sep',
  '10' => 'Oct',
  '11' => 'Nov',
  '12' => 'Dec',);
  
public $years = array(
  '16'=>'2016',
  '17'=>'2017',
  '18'=>'2018',
  '19'=>'2019',
  '20'=>'2020',
  '21'=>'2021',
  '22'=>'2022',
  '23'=>'2023',
  '24'=>'2024',
  '25'=>'2025');  
  
public $usstates = array(
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PA' => 'Pennsylvania',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VA' => 'Virginia',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming',
    'DC' => 'District of Columbia',
    'AS' => 'American Samoa',
    'GU' => 'Guam',
    'MP' => 'Northern Mariana Islands',
    'PR' => 'Puerto Rico',
    'UM' => 'United States Minor Outlying Islands',
    'VI' => 'Virgin Islands, U.S.');
}
  

