<?php
  require 'lib/Core.php';
  require 'lib/CustomerLink.php';
  require 'lib/ProcessLink.php';
  require 'lib/ReportLink.php';
  
function donor_cc_process($info){
  $config = \Drupal::config('donor_forms.settings');
  $donation_data = $config->get('donations');
  $agentCode = $donation_data['userid']; 
  $password = $donation_data['password']; 
  if(empty ($agentCode) OR empty ($password)){
    drupal_set_message(t('There is a configuration error. Please contact the system administrator.'), 'error');
    $message = t("The donor payment gateway isn't configured");
            \Drupal::logger('donor_forms')->notice($message);
    return false;
  }
  $iATS_PL = new iATS\ProcessLink($agentCode, $password);

  $request = array(
      'creditCardNum' => $info['ccnum'],
      'creditCardExpiry' => $info['expires'],
      'cvv2' => $info['cvv'],
      'mop' => $info['type'],
      'firstName' => $info['first'],
      'lastName' => $info['last'],
      'address' => $info['address1'],
      'city' => $info['city'],
      'state' => $info['state'],
      'zipCode' => $info['zip'],
      'total' => $info['total'],
      'currency' => 'USD',
  );

  //// Make the API call using the ProcessLink service.
  $iats_response = $iATS_PL->processCreditCard($request);

  // Verify successful call
  if (substr(trim($iats_response['AUTHORIZATIONRESULT']),0,2) == 'OK'){
  return TRUE ;
} else {
  // Handle failed transaction
  return FALSE;
}

}
function donor_eft_process($info){
  $config = \Drupal::config('donor_forms.settings');
  $donation_data = $config->get('donations');
  
  $agentCode = $donation_data['userid']; 
  $password = $donation_data['password']; 
    if(empty ($agentCode) OR empty ($password)){
    drupal_set_message(t('There is a configuration error. Please contact the system administrator.'), 'error');
    $message = t("The donor payment gateway isn't configured");
            \Drupal::logger('donor_forms')->notice($message);
    return false;
  }
  
  $iATS_PL = new iATS\ProcessLink($agentCode, $password);

  $request = array(
    'accountNum' => $info['acct'], // The customer's bank account number.
    'accountType' => $info['type'], // The customer's bank account type.
    'firstName' => $info['first'],
    'lastName' => $info['last'],
    'address' => $info['address1'],
    'city' => $info['city'],
    'state' => $info['state'],
    'zipCode' => $info['zip'],
     // Options: CHECKING, SAVING (North America only.)
    'total' => $info['total'], // The total payment amount.

  );

// Make the API call using the ProcessLink service.
$iats_response = $iATS_PL->processACHEFT($request);

// $response will be an array, or an API error.
// Save your transaction ID so you can check on the transaction later.
$transactionID = $iats_response['TRANSACTIONID']; 

if (substr(trim($iats_response['AUTHORIZATIONRESULT']),0,10) == 'OK: 555555'){
  
     drupal_set_message(t("Your payment was successfully processed."), 'info');
  
  return TRUE ;
} else {
  drupal_set_message(t("Your payment failed."), 'info');
  // Handle failed transaction
  return FALSE;
}
}