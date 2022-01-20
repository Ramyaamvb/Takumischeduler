(function($){
$('.screen_content').each(function() {
var type = $('#type').val();
var cell = $('#cell').val();

   setTimeout(function(){
				window.location = '/cellmetrics/cell/'+cell+'/'+type; }, 60000);
  
}); 
})(jQuery);