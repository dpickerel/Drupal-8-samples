/**
 * @file
 * Donor forms related javascript for form control
 *
 */
(function ($, Drupal, settings) {
  "use strict";
  Drupal.behaviors.Donor = { //the name of our behavior
    attach: function (context, settings) {

      function strip_tags(input, allowed) { //the strip_tags function that cuts unnecessary tags on regular expression and returns clean text. Important! The input parameter works correctly only string data type.
        allowed = (((allowed || '') + '')
          .toLowerCase()
          .match(/<[a-z][a-z0-9]*>/g) || [])
          .join('');
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
          commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
        return input.replace(commentsAndPhpTags, '')
          .replace(tags, function($0, $1) {
            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
          });
      }
// is this a gift in memory of someone or in honor of someone
    $( "#edit-gift-direct-memory" ).change(function() {
     if($('#edit-gift-direct-memory').is(':checked')){
       $('#edit-memorial').css('display', 'block');
       $('#edit-honorific').css('display', 'none');
     } else {
       $('#edit-memorial').css('display', 'none');
     }
    });
  $( "#edit-gift-direct-honor" ).change(function() {
     if($('#edit-gift-direct-honor').is(':checked')){
       $('#edit-honorific').css('display', 'block');
       $('#edit-memorial').css('display', 'none');
     } else {
       $('#edit-honorific').css('display', 'none');
     }
});

   // Directed To
   $( "input[name*='target[direct]']" ).change(function() {
     if($('#edit-target-direct-program-or-department').is(':checked')){
       $('#edit-target-specify').css('display', 'block');
     } else {
       $('#edit-target-specify').css('display', 'none');
     }
});

   // one time, recurring, or a plege gift
   $( "#edit-gift-info-direct-one-time" ).change(function() {
     if($('#edit-gift-info-direct-one-time').is(':checked')){
       $('#edit-one-gift').css('display', 'block');
       $('#edit-payment').css('display', 'block');
       $('#edit-ccard').css('display', 'block');
       $('#edit-pledge').css('display', 'none');
       $('#edit-recurring-info').css('display', 'none');
     } else {
       $('#edit-one-gift').css('display', 'none');
     }
});
  $( "#edit-gift-info-direct-monthly" ).change(function() {
     if($('#edit-gift-info-direct-monthly').is(':checked')){
       $('#edit-recurring-info').css('display', 'block');
       $('#edit-pledge').css('display', 'none');
       $('#edit-one-gift').css('display', 'none');
       $('#edit-payment').css('display', 'none');
       $('#edit-ccard').css('display', 'none');
     } else {
       $('#edit-recurring-info').css('display', 'none');
     }
});
  $( "#edit-gift-info-direct-pledge" ).change(function() {
     if($('#edit-gift-info-direct-pledge').is(':checked')){
       $('#edit-pledge').css('display', 'block');
       $('#edit-one-gift').css('display', 'none');
       $('#edit-recurring-info').css('display', 'none');
       $('#edit-payment').css('display', 'none');
       $('#edit-ccard').css('display', 'none');
     } else {
       $('#edit-pledge').css('display', 'none');
     }
});  

// Credit card or bank draft
   $( "#edit-payment-type-0" ).change(function() {
     if($('#edit-payment-type-0').is(':checked')){
       $('#edit-ccard').css('display', 'block');
       $('#edit-eft').css('display', 'none');
     } else {
       $('#edit-ccard').css('display', 'none');
     }
});
  $( "#edit-payment-type-1" ).change(function() {
     if($('#edit-payment-type-1').is(':checked')){
       $('#edit-eft').css('display', 'block');
       $('#edit-ccard').css('display', 'none');
     } else {
       $('#edit-eft').css('display', 'none');
     }
});
   
    }
  };
})(jQuery, Drupal, drupalSettings);