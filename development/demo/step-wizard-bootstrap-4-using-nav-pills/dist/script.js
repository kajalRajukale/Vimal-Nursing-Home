// JQUERY
$(document).ready(function() {
  // Action next
  $('.btn-next').on('click', function() {
    // Get value from data-to in button next
    const n = $(this).attr('data-to');
    // Action trigger click for tag a with id in value n
    $(n).trigger('click');
  });
  // Action back
  $('.btn-prev').on('click', function() {
    // Get value from data-to in button prev
    const n = $(this).attr('data-to');
    // Action trigger click for tag a with id in value n
    $(n).trigger('click');
  });
});