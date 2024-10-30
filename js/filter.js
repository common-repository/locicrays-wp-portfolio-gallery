jQuery( document ).ready(function() {
  /* activate jquery isotope */
  var $container = jQuery('#lrportfolio').isotope({
    itemSelector : '.item',
    isFitWidth: true
  });
  $container.isotope({ filter: '*' });
  // filter items on button click
  jQuery('#filters').on( 'click', 'button', function() {
    var filterValue = jQuery(this).attr('data-filter');
    $container.isotope({ filter: filterValue });
  });
});