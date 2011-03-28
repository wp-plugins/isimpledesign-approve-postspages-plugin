jQuery(document).ready(function() {
	// main blog approve posts

	$('.check').live('click', function() {
							   
			$(this).removeClass('check').addClass('check_already_approved');
			var testor = $(this).parent().parent().html();
			$(this).parent().parent().empty();
			$('#main_bloc_allready_approved').find('tbody').prepend('<tr>'+testor+'</tr>');
			
			var approve = $(this).attr("alt");
			
			$(this).hide();
			
			$(this).hide();
			
			$('.load-' + approve).fadeIn('slow');
					
		var data = {
			action: 'ajax_approve',
			whatever: approve
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			
		 }, "json" );				
		 return false;
	});
	
	$('.check_already_approved').live('click', function(){
			
			$(this).removeClass('check_already_approved').addClass('check')
			var testor = $(this).parent().parent().html();
			$(this).parent().parent().empty();
			
			$('#main_bloc_approved').find('tbody').prepend('<tr>'+testor+'</tr>');
			
			//bindEm();	
			
			var approve = $(this).attr("alt");
			
			$(this).hide();
			
			$('.load-' + approve).fadeIn('slow');
			
		var data = {
			action: 'ajax_allapproved',
			whatever: approve
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			
		 }, "json" );				
		 return false;
	});


	

// approved - allready approved section

$('.allready_approved').live('click', function(){
				
	 $('#main_bloc_approved').slideUp();
	//location.reload();
	 $('#main_bloc_allready_approved').fadeTo(0,1);				
	 return false;
});

$('.approved').live('click', function(){
			
	$('#main_bloc_allready_approved').slideUp();
	//location.reload();
	$('#main_bloc_approved').fadeTo(0,1);	
	 return false;
});
	
});