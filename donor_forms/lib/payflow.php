<?php
/**
 * @file
 * Implements Paypals Payflow Pro payment services for use in Drupal Commerce.
 */
 
define('PAYPAL_TXN_MODE_LIVE', 'live');
define('PAYPAL_TXN_MODE_SANDBOX', 'sand_box');
 

/**
 * Payment method callback: checkout form submission.
 */
function drupal_forms_payflow_pro_submit($nvp, $charge) {


  // Build a description for the order.
  $description = array();

  // Submit the request
  $response = drupal_forms_payflow_pro_request($nvp);

  // Something went wrong with the request. Abort.
  if (!$response) {
    $message = var_export($response,TRUE);
    \Drupal::logger('donor_forms')->error('Transaction aborted. Details: @type.',
        array(
            '@type' => $message ));
    drupal_set_message(t('We are unable to process your request at this time. Please try again.'), 'error');
    return FALSE;  
  }

  // If we didn't get an approval response code...
  if ($response['RESULT'] != '0') {
    // Create a failed transaction with the error message.
    $transaction->status = COMMERCE_PAYMENT_STATUS_FAILURE;
  }
  else {
        $transaction->status = COMMERCE_PAYMENT_STATUS_SUCCESS;
    }


  // Store the type of transaction in the remote status.
  $transaction->remote_status = $response['RESULT'];
  // Build a meaningful response message.
  $message = array(
    '<b>' . drupal_forms_payflow_pro_reverse_txn_type($nvp['TENDER']) . '</b>',
    '<b>' . ($response['RESULT'] != '0' ? t('REJECTED') : t('ACCEPTED')) . ':</b> ',
    t('AVS response: ' . t($response['RESPMSG'])),
  );

  // Add the CVV response if enabled.
  if (isset($response['CVV2MATCH'])) {
    $message[] = t('CVV2 match: @cvv', array('@cvv' => drupal_forms_payflow_pro_cvv_response($response['CVV2MATCH'])));
  }

  $transaction->message = implode('<br />', $message);

  // If the payment failed, display an error and rebuild the form.
  if ($response['RESULT'] != '0') {
    drupal_set_message(t('We received the following error processing your card. Please enter you information again or try a different card.'), 'error');
    drupal_set_message(t($response['RESPMSG']), 'error');
    return FALSE;
  }else {
     drupal_set_message(t("Your payment was successfully processed."), 'info');
  }
  return $response['PNREF'];
}

/**
 * Submits a request to PaypalDirect.
 *
 * @param $payment_method
 *   The payment method instance array associated with this API request.
 */
function drupal_forms_payflow_pro_request( $nvp = array(), $order = NULL) {
  $config = \Drupal::config('donor_forms.settings');
  $payment_data = $config->get('payments');
  
  // Get the API endpoint URL for the method's transaction mode.
  $url = drupal_forms_payflow_pro_server_url( $payment_data['mode']);

  // Add the default name-value pairs to the array.
  $nvp += array(
    // API credentials
    'PARTNER' => $payment_data['partner'],
    'USER'    => $payment_data['vendor'],
    'PWD'     => $payment_data['password'],
    'VENDOR'  => $payment_data['vendor'],
    'VERBOSITY' => 'MEDIUM',
  );

  // Prepare the name-value pair array to be sent as a string.
  $pairs = array();
  foreach ($nvp as $key => $value) {
    $pairs[] = $key . '=' . $value;
  }

  // Setup the cURL request.
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $pairs));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_NOPROGRESS, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
  $result = curl_exec($ch);
  
  // Log any errors to the watchdog.
  if ($error = curl_error($ch)) {
    $message = var_export($result,TRUE);
    unset($nvp['ACCT']);
    unset($nvp['CVV2']);
    $info_in = var_export($nvp,TRUE);
    \Drupal::logger('donor_forms')->error('cURL error in payflow interface. Details: @type:, Data: @data ',
        array(
            '@type' => $message,
            '@data' => $info_in));
    return FALSE;
  }
  curl_close($ch);

  // Make the response an array and trim off the encapsulating characters.
  $response = explode('&', $result);
  
  $return = array();
  for ($i = 0; $i < count($response); $i++) {
    $kv = explode('=', $response[$i]);
    $return[$kv[0]] = $kv[1];
  }

 
  if (!isset($return['PNREF'])) {
    $message = var_export($return,TRUE);
    $info_in = var_export($nvp,TRUE);
   \Drupal::logger('donor_forms')->error('Payflow Pro: Unable to complete payflow transaction.  Details: @type: Values: @data', array(
      '@type' => $message,
      '@data' => $info_in));      
    return FALSE;
  }else {
     drupal_set_message(t("Your payment was successfully processed."), 'info');
  }

  return $return;
}

/**
 * Returns the URL to the Paypal server determined by transaction mode.
 *
 * @param $txn_mode
 *   The transaction mode that relates to the live or test server.
 *
 * @return
 *   The URL to use to submit requests to the server.
 */
function drupal_forms_payflow_pro_server_url($txn_mode) {
  switch ($txn_mode) {
    case PAYPAL_TXN_MODE_LIVE:
      return 'https://payflowpro.paypal.com';
    case PAYPAL_TXN_MODE_SANDBOX:
      return 'https://pilot-payflowpro.paypal.com'; 
  }
}


/**
 * Returns the description of a transaction type.
 *
 * @param $txn_type
 *   Transaction type string.
 */
function drupal_forms_payflow_pro_reverse_txn_type($txn_type) {
  switch (strtoupper($txn_type)) {
    case 'S': return t('Sale Transaction');
    case 'A': return t('Authorization');
    case 'D': return t('Delayed Capture');
    case 'C': return t('Credit');
    case 'V': return t('Void');
  }
}


/**
 * Returns the message text for a CVV match.
 */
function drupal_forms_payflow_pro_cvv_response($code) {
  switch ($code) {
    case 'Y':
      return t('Match');
    case 'N':
      return t('No Match');
    default:
      return t('Not Processed');
  }
}

/**
 * Validates a set of credit card details entered via the credit card form.
 *
 * @param $details
 *   An array of credit card details as retrieved from the credit card array in
 *   the form values of a form containing the credit card form.
 * @param $settings
 *   Settings used for calling validation functions and setting form errors:
 *   - form_parents: an array of parent elements identifying where the credit
 *     card form was situated in the form array
 *
 * @return
 *   TRUE or FALSE indicating the validity of all the data.
 *
 * @see commerce_payment_credit_card_form()
 */
function drupal_forms_credit_card_validate($details, $settings) {
  $prefix = implode('][', $settings['form_parents']) . '][';
  $valid = TRUE;

  // Validate the credit card type.
  if (!empty($details['valid_types'])) {
    $type = commerce_payment_validate_credit_card_type($details['number'], $details['valid_types']);

    if ($type === FALSE) {
      form_set_error($prefix . 'type', t('You have entered a credit card number of an unsupported card type.'));
      $valid = FALSE;
    }
    elseif ($type != $details['type']) {
      form_set_error($prefix . 'number', t('You have entered a credit card number that does not match the type selected.'));
      $valid = FALSE;
    }
  }

  // Validate the credit card number.
  if (!commerce_payment_validate_credit_card_number($details['number'])) {
    form_set_error($prefix . 'number', t('You have entered an invalid credit card number.'));
    $valid = FALSE;
  }

  // Validate the expiration date.
  if (($invalid = commerce_payment_validate_credit_card_exp_date($details['exp_month'], $details['exp_year'])) !== TRUE) {
    form_set_error($prefix . 'exp_' . $invalid, t('You have entered an expired credit card.'));
    $valid = FALSE;
  }

  // Validate the security code if present.
  if (isset($details['code']) && !commerce_payment_validate_credit_card_security_code($details['number'], $details['code'])) {
    form_set_error($prefix . 'code', t('You have entered an invalid card security code.'));
    $valid = FALSE;
  }

  // Validate the start date if present.
  if (isset($details['start_month']) && ($invalid = commerce_payment_validate_credit_card_start_date($details['start_month'], $details['start_year'])) !== TRUE) {
    form_set_error($prefix . 'start_' . $invalid, t('Your have entered an invalid start date.'));
    $valid = FALSE;
  }

  // Validate the issue number if present.
  if (isset($details['issue']) && !commerce_payment_validate_credit_card_issue($details['issue'])) {
    form_set_error($prefix . 'issue', t('You have entered an invalid issue number.'));
    $valid = FALSE;
  }

  return $valid;
}

function payflow_ip_address() {
  $ip_address = &drupal_static(__FUNCTION__);

  if (!isset($ip_address)) {
    $ip_address = $_SERVER['REMOTE_ADDR'];

  }

  return $ip_address;
}

