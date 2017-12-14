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

/**
 * Lorem Ipsum block form
 */
class DonorDonationForm extends FormBase
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


      
    $form['personal'] = array(
      '#type' => 'fieldset',
      '#title' => t('Personal Information'),
      '#tree' => TRUE,
    );
        $form['personal']['fname'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('First Name:'),
            '#required' => TRUE,
        );
        $form['personal']['lname'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Last Name:'),
            '#required' => TRUE,
        );
        
        $form['personal']['address'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Address:'),
            '#required' => TRUE,
        );
        $form['personal']['city'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('City:'),
            '#required' => TRUE,
        );
        $form['personal']['state'] = array(
            '#type' => 'select',
            '#title' => $this->t('State:'),
            '#options' => $this->usstates,
            '#required' => TRUE,
        );
        $form['personal']['zip'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Zip:'),
            '#required' => TRUE,
        );

        $form['personal']['home_phone'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Home Phone:'),
            '#required' => TRUE,
        );
        $form['personal']['business_phone'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Business Phone:'),
        );
        $form['personal']['email'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Email:'),
            '#required' => TRUE,
        );

        $form['from'] = array(
          '#type'=> 'item',
          '#title'=> t('THIS GIFT IS:'),


  );
    
      $active = array('memory' => t('In Memory of'), 'honor' => t('In Honor of'));

      $form['gift_direct'] = array(
        '#type' => 'radios',
        '#title' => t('Directed To'),
        '#options' => $active,
        '#description' => t('How to direct the gift'),
      );
        // in memorial of 
      $form['memorial'] = array(
        '#type' => 'fieldset',
        '#title' => t('In Memory of:'),
        '#tree' => TRUE,
        '#required' => TRUE,
    ); 
    
        $form['memorial']['toname'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Name:'),
        );
        $form['memorial']['header'] = array(
        '#markup' => t('Please notify this person of my gift: '),
      );
        $form['memorial']['name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Name:'),
        );
        $form['memorial']['address'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Address:'),
        );
        $form['memorial']['city'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('City:'),
        );
        $form['memorial']['state'] = array(
            '#type' => 'select',
            '#title' => $this->t('State:'),
            '#options' => $this->usstates,
        );
        $form['memorial']['zip'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Zip:'),
        );
 // in honor of 
     $form['honorific'] = array(
      '#type' => 'fieldset',
      '#title' => t('In Honor of:'),
      '#tree' => TRUE,
    );  
         $form['honorific']['name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Name:'),
        );
        $form['honorific']['address'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Address:'),
        );
        $form['honorific']['city'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('City:'),
        );
        $form['honorific']['state'] = array(
            '#type' => 'select',
            '#title' => $this->t('State:'),
            '#options' => $this->usstates,
        );
        $form['honorific']['zip'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Zip:'),
        );
 
 
        
    $form['gift_info'] = array(
      '#type' => 'fieldset',
      '#title' => t('Gift Information:'),
      '#tree' => TRUE,
    );        
    
      $don_type_array = array('one time' => t('One time donation'), 'monthly' => t('Monthly donation'), 'pledge' => t('Pledge payment'));

      $form['gift_info']['direct'] = array(
        '#type' => 'radios',
        '#title' => t('I would like to make a:'),
        '#default_value' => 'one time',
        '#options' =>  $don_type_array,
        '#description' => t('How to direct the gift'),
        '#required' => TRUE,
      );

      $form['recurring_info'] = array(
      '#type' => 'fieldset',
      '#title' => t('Gift Information:'),
      '#tree' => TRUE,
    );   
      $don_recurring_array = array( 'level 1' => t("$21.00 per month (Keystone $250.00 Level)"),
        'level 2' => t("$42.00 per month (Keystone $500.00 Level)"),
        'level 3' => t("$84.00 per month (Keystone $1,000.00 Level)"),
        'Other' => t("Other"));

      $form['recurring_info']['recurring'] = array(
        '#type' => 'radios',
        '#title' => t('I would like to make a*:'),
        '#options' =>  $don_recurring_array,
      );
        $form['recurring_info']['other'] = array(
          '#type' => 'textfield',
          '#title' => t('$'),
        );
        $don_recurring_start_dates = array( 0 => t("1st"),
         1 => t("15th"), );

      $form['recurring_info']['starts'] = array(
        '#type' => 'select',
        '#title' => t('on the'),
        '#options' =>  $don_recurring_start_dates,
      );


      $form['recurring_info']['months'] = array(
        '#type' => 'select',
        '#title' => t('of each month starting in '),
        '#options' => $this->months,
      );

      $form['recurring_info']['years'] = array(
        '#type' => 'select',
        '#options' =>  $this->years,
      );
      $form['pledge'] = array(
        '#type' => 'fieldset',
        '#title' => t('Gift Information:'),
        '#tree' => TRUE,
    ); 
      $form['pledge']['payment'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('I would like to make a pledge payment of $'),
        ); 
      $form['one_gift'] = array(
        '#type' => 'fieldset',
        '#title' => t('Gift Information:'),
        '#tree' => TRUE,
    ); 
      $form['one_gift']['payment'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('I would like to make a payment of $'),
        );   
    $form['target'] = array(
      '#type' => 'fieldset',
      '#title' => t('Please direct my donation to:'),
      '#tree' => TRUE,
    );    
    
        $target = array("greatest need" => t("Greatest need."),'Cancer Services' => t('Cancer Services'), 2 => t('Cardiac Care'));

        $form['target']['direct'] = array(
        '#type' => 'radios',
        '#title' => t('Directed To'),
        '#options' => $target,
        '#default_value' => 0,
      );
      $form['target']['specify'] = array(
            '#type' => 'textfield',
        );
      
    $form['contact'] = array(
      '#type' => 'fieldset',
      '#title' => t('Please contact me about the following:'),
      '#tree' => TRUE,
    ); 
      $contact_array = array("Stock gifts" => t("Stock gifts"),'Planned gifts' => t('Planned gifts'),
          'matching gift' => t('My employer has a matching gift program'), 'talk to someone' => t('I would like to talk to someone about making a pledge'));
          
        $form['contact']['direct'] = array(
        '#type' => 'radios',
        '#title' => t(''),
        '#options' => $contact_array,
      );      

    $form['comments'] = array(
          '#type' => 'textarea',
          '#title' => t('Comments'),
        );
    $form['payment'] = array(
      '#type' => 'fieldset',
      '#title' => t('Payment Options'),
      '#tree' => TRUE,
    );
      $contact_array = array(0 => t("Credit/Debit Card."),1 => t('Bank Account'));
          
        $form['payment']['type'] = array(
        '#type' => 'radios',
        '#title' => t('I would like to pay with*: '),
        '#default_value' => 0,
        '#options' => $contact_array,
      ); 
    
    $form['ccard'] = array(
      '#type' => 'fieldset',
      '#title' => t('Pay By Credit / Debit Card:'),
      '#tree' => TRUE,
    );
         $form['ccard']['name'] = array(
            '#type' => 'textfield',
            '#title' => t('Name on Card*:'),
        );
        $form['ccard']['number'] = array(
            '#type' => 'textfield',
            '#title' => t('Card Number*:'),
        );
        $form['ccard']['month'] = array(
            '#type' => 'select',
            '#options' => $this->months,
            '#title' => t('Expiration Month*:'),
        );
        $form['ccard']['year'] = array(
            '#type' => 'select',
            '#options' => $this->ccyears,
            '#title' => t('Expiration Year*:'),
        );
        $form['ccard']['cvv'] = array(
            '#type' => 'textfield',
            '#title' => t('CVV Code*:'),
        );
        $form['ccard']['type'] = array(
            '#type' => 'select',
            '#options' => $this->types,
            '#title' => t('Credit Card'),
        );
      
    $form['eft'] = array(
      '#type' => 'fieldset',
      '#title' => t('Pay with Bank Account:'),
      '#tree' => TRUE,
    );
      
      $eft_type_array = array('CHECKING' => t("Checking."),'SAVING' => t('Savings'));
    
        $form['eft']['type'] = array(
        '#type' => 'radios',
        '#title' => t('Account Type*: '),
        '#options' => $eft_type_array,
      );
      $form['eft']['bank'] = array(
            '#type' => 'textfield',
            '#title' => t('Bank Name*:'),
        );
        $form['eft']['routing'] = array(
            '#type' => 'textfield',
            '#title' => t('Routing Number*:'),
        );
        $form['eft']['account'] = array(
           '#type' => 'textfield',
            '#title' => t('Account Number*'),
        );
        $form['eft']['account2'] = array(
            '#type' => 'textfield',
            '#title' => t('Re-type Account Number*:'),
        );
        
        // Submit
      $form['submit'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Submit Donation'),
      );

      $form['disclaimer'] = array(
        '#markup' => t('I understand that by submitting this form, I am authorizing <name> to charge the payment information above for the amount I specified.'),
        '#attached' => array(
            'library' =>  array(
                'donor_forms/donor_forms',
            ),
        ),      );
        return $form;
    }                              
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        module_load_include('php', 'donor_forms', 'lib/ccvalidate');

      if($form_state->getValue('gift_info')['direct'] == 'one time'){
          if(empty($form_state->getValue('one_gift')['payment'])){
             $form_state->setErrorByName('one_gift][payment', t('Please specify a payment amount.'));
          };        
        if($form_state->getValue('payment')['type'] == 0){
          if(empty($form_state->getValue('ccard')['number'])){
             $form_state->setErrorByName('ccard][number', t('You have to specify a valid credit card number.'));
          };
          if(drupal_forms_validate_credit_card_number($form_state->getValue('ccard')['number']) == FALSE){
            $form_state->setErrorByName('ccard][number', t('You have to specify a valid credit card number.'));
          }
          if(empty($form_state->getValue('ccard')['name'])){
             $form_state->setErrorByName('ccard][name', t('You have to specify a valid credit card type.'));
          };
          if(empty($form_state->getValue('ccard')['cvv'])){
             $form_state->setErrorByName('ccard][cvv', t('Please specify a valid cvv number.'));
          };
          if(drupal_forms_validate_cvv($form_state->getValue('ccard')['number'],$form_state->getValue('ccard')['cvv']) == FALSE){
             $form_state->setErrorByName('ccard][cvv', t('Please specify a valid cvv number.'));
          }
          
          if($error = drupal_forms_validate_credit_card_cvv($form_state->getValue('ccard')['month'], $form_state->getValue('ccard')['year']) !== TRUE){
             if($error == 'month'){
                $form_state->setErrorByName('ccard][month', t('The expiration date is set early than today.'));
             } else {
               $form_state->setErrorByName('ccard][month', t('The year is invalid.'));
             }
          }
        } else
        {
          if(empty($form_state->getValue('eft')['account']) OR empty($form_state->getValue('eft')['account2'])){
              $form_state->setErrorByName('eft][account', t('The account number is missing'));
          }
          if($form_state->getValue('eft')['account'] != $form_state->getValue('eft')['account2']){
                $form_state->setErrorByName('eft][account', t("The account numbers don't match"));
            }
          if(empty($form_state->getValue('eft')['type'])){
             $form_state->setErrorByName('eft][type', t('Which account would you like the funds taken from?'));
          };
          if(empty($form_state->getValue('eft')['routing'])){
             $form_state->setErrorByName('eft][routing', t("Please specify your bank's routing number."));
          };
            if(drupal_forms_validate_bank_routing($form_state->getValue('eft')['routing']) == false){
                $form_state->setErrorByName('eft][routing', t("The bank routing number is not correct."));
            };

          if(empty($form_state->getValue('eft')['account'])){
             $form_state->setErrorByName('eft][account', t('Please specify your account number.'));
          };
          if(drupal_forms_validate_bank_account($form_state->getValue('eft')['account']) == false){
                $form_state->setErrorByName('eft][account', t('The account number has characters other than numbers.'));
            };
        }
            
      }
    if(($form_state->getValue('target')['direct'] == "program or department") AND ( empty($form_state->getValue('target')['specify']) )){
      $form_state->setErrorByName('target][specify', t('Please specify a program or department for the donation.'));
    };        

    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {

      $result = FALSE;
      $config = \Drupal::config('donor_forms.settings');
      $donation_data = $config->get('donations');

      $admin_email = $donation_data['email'];
      if($form_state->getValue('gift_info')['direct'] == 'one time'){

        module_load_include('inc', 'donor_forms', 'iATS');
        // get billing info
        $info['first'] = $form_state->getValue('personal')['fname'];
        $info['last'] = $form_state->getValue('personal')['lname'];
        $info['address1'] = $form_state->getValue('personal')['address'];
        $info['city'] = $form_state->getValue('personal')['city'];
        $info['state'] = $form_state->getValue('personal')['state'];
        $info['zip'] = $form_state->getValue('personal')['zip'];

        // process credit cards
        if( $form_state->getValue('payment')['type'] == 0){
          $info['ccnum'] = strval($form_state->getValue('ccard')['number']);
          $info['expires'] = $form_state->getValue('ccard')['month'] . '/' . $form_state->getValue('ccard')['year'];
          $info['cvv'] = $form_state->getValue('ccard')['cvv'];
          $info['type'] = $form_state->getValue('ccard')['type'];
          $info['total'] = $form_state->getValue('one_gift')['payment'];
          $result = donor_cc_process($info);
                  
        } elseif( $form_state->getValue('payment')['type'] == 1){
          $info['acct'] = strval($form_state->getValue('eft')['account']) . strval($form_state->getValue('eft')['routing']); // The customer's bank account number.
          $info['type'] = $form_state->getValue('eft')['type']; // The customer's bank account type.
          $info['total'] = $form_state->getValue('one_gift')['payment'];

          $result = donor_eft_process($info);
        }
      if($result == FALSE){
        drupal_set_message(t('An error occurred while processing your payment.'), 'error');
        return;
      } else {
        drupal_set_message(t('Your payment was successfully processed.'), 'status');
      }
      
      }

      $new_page_values = array();
      $new_page_values['type'] = 'donor_record';
      $new_page_values['title'] = $form_state->getValue('personal')['fname'] . ' ' . $form_state->getValue('personal')['lname'];

      $new_page_values['body'] = $form_state->getValue('body');
      $new_page_values['field_directed'] = $form_state->getValue('field_directed');
      $new_page_values['field_donor_address'] = $form_state->getValue('personal')['address'];
      $new_page_values['field_donor_business_phone'] = $form_state->getValue('personal')['business_phone'];
      $new_page_values['field_donor_comments'] = $form_state->getValue('comments');
      $new_page_values['field_donor_email'] = $form_state->getValue('personal')['email'];
      $new_page_values['field_donor_home_phone'] = $form_state->getValue('personal')['home_phone'];
      $new_page_values['field_full_donor_name'] = $form_state->getValue('personal')['fname'] . ' ' . $form_state->getValue('personal')['lname'];
      $new_page_values['field_gift_program'] = $form_state->getValue('target')['direct'];
      $new_page_values['field_gift_donation_type'] = $form_state->getValue('gift_info')['direct'];
      $new_page_values['field_gift_program_specify'] = $form_state->getValue('target')['specify'];
      $new_page_values['field_honor_of_address'] = $form_state->getValue('honorific')['address'];
      $new_page_values['field_honor_of_name'] = $form_state->getValue('honorific')['name'];
      $new_page_values['field_memorial_notify_address'] = $form_state->getValue('memorial')['address'];
      $new_page_values['field_memorial_notify_name'] = $form_state->getValue('memorial')['name'];
      $new_page_values['field_memorial_to_name'] = $form_state->getValue('memorial')['toname'];
      $new_page_values['field_pledge_gift'] = $form_state->getValue('one_gift')['name'];
      $new_page_values['field_pledge_payment'] = $form_state->getValue('one_gift')['payment'];
      $new_page_values['field_recurring_amount'] = $form_state->getValue('recurring_info')['recurring'];
      $new_page_values['field_recurring_other_amount'] = $form_state->getValue('recurring_info')['other'];
      $new_page_values['field_recurring_start_date'] = $form_state->getValue('recurring_info')['starts'];
      $new_page_values['field_recurring_start_month'] = $form_state->getValue('recurring_info')['months'];
      $new_page_values['field_recurring_start_year'] = $form_state->getValue('recurring_info')['years'];
      $new_page = entity_create('node', $new_page_values);
      $return = $new_page->save();
      drupal_set_message(t('Thank you for your donation.'), 'status');
      $this->donor_response_mail($form_state->getValue('personal')['email']);
      $this->donor_admin_mail($form_state->getValue('personal')['fname'] . ' ' . $form_state->getValue('personal')['lname'],$admin_email);
    }
     private function donation_mail($info){
        $mailManager = \Drupal::service('plugin.manager.mail');
        $module = 'donor_forms';
        $key = 'donor_forms';
        $to = t('donald@fane.com');
        $params['message'] = $info;
        $params['node_title'] = $info->title;
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $send = true;
        $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
        if ($result['result'] !== true) {
            $message = t('An email notification has failed');
            \Drupal::logger('donor_forms')->notice($message);
        }
    }
    
    private function donor_response_mail($info){
      $email =  'THANK YOU';

      $site_mail = \Drupal::config('system.site')->get('mail');
      module_load_include('module', 'donor_forms', 'donor_forms');
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'donor_forms';
      $key = 'donor_forms';
      $to = t($info);
      $params['message'] = $email;
      $params['subject'] = "Thank you for your donation";
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = true;
      $result = $mailManager->mail($module, $key, $to, $langcode, $params, "donald@fane.com", $send);
      if ($result['result'] !== true) {
          $message = t('An email notification has failed');
          \Drupal::logger('donor_forms')->notice($message);
      }

    }
        
    private function donor_admin_mail($info,$admin_email){
      $config = \Drupal::config('donor_forms.settings');

      global $base_url;
      $email =  'A new donation has been created by ' . $info . '. You can see the records at ' . $base_url . 'admin/reports/donations-and-payments';
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'donor_forms';
      $key = 'donor_forms';
      $to = $admin_email;
      $params['message'] = $email;
      $params['node_title'] = $info;
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = true;
      $result = $mailManager->mail($module, $key, $to, $langcode, $params, $to, $send);
      if ($result['result'] !== true) {
          $message = t('An email notification has failed');
          \Drupal::logger('donor_forms')->notice($message);
      }

    }
public $months = array(
  '01' =>'Jan',
  '02' =>'Feb',
  '03' =>'Mar',
  '04' =>'Apr',
  '05' =>'May',
  '06' =>'Jun',
  '07' =>'Jul',
  '08' =>'Aug',
  '09' =>'Sep',
  '10' =>'Oct',
  '11' =>'Nov',
  '12' =>'Dec',);
  
public $years = array(
  '2016'=>'2016',
  '2017'=>'2017',
  '2018'=>'2018',
  '2019'=>'2019',
  '2020'=>'2020',
  '2021'=>'2021',
  '2022'=>'2022',
  '2023'=>'2023',
  '2024'=>'2024',
  '2025'=>'2025');  
  
public $ccyears = array(
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
    
    public $types = array( 'VISA' => 'Visa','MC' => "Master Charge", 'AMX' => 'American Express', 'DSC' => 'Discover');
    
}
