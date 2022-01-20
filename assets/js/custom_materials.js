(function($){

	$(document).ready(function() {
	   setTimeout(function(){
  window.location.reload(1);
}, 60000);
	}); 
	
	$('#screen').each(function(){
		var content='';
		content += screen.width+'*'+screen.height;					
		$(this).html('<h5 style="font-size:18px">'+content+'</h5>');
	})

})(jQuery);