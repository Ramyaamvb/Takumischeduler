(function($){

setTimeout(function() { 
        $('#preloader').fadeOut('slow', function () {
           $(this).remove();
        });
    }, 500);
})(jQuery);