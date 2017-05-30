(function( $ ) {
	'use strict';

	$( window ).load(function() {

		//Meter ui
		$("#peruse-meter_quantity").change(function (e) {
	      if($(e.target).val() == 0){
	      	$("#peruse-meter_schedule").prop('disabled', true);
	      	return;
	      }
	      $("#peruse-meter_schedule").prop('disabled', false);
    	});


		//Show/hide secret
	  	$( "#secret-toggle" ).click(function(e) {
		  e.preventDefault();

		  var $input = $("#peruse-oauth2_secret");

		  var change = $(e.target).hasClass('genericon-password') ? 'text' : 'password';

		  $('#secret-toggle').removeClass().addClass('button-secondary genericon genericon-'+change);
		  	
		  var rep = $("<input type='" + change + "' />")
            .attr("id", $input.attr("id"))
            .attr("name", $input.attr("name"))
            .attr('class', $input.attr('class'))
            .val($input.val())
            .insertBefore($input);
		  	
		  $input.remove();
          $input = rep;
		  
		});

	  	//price mask
		$('#peruse-price').maskMoney({prefix:'$ ', allowZero:true, affixesStay: false});

		$("#peruse-enable").change(function (e) {
	      if(!this.checked){
	      	$('.article_child_element').prop('disabled', true);
	      	return;
	      }

	      $('.article_child_element').prop('disabled', false);
	      $('#peruse-integration').prop("checked", true);

    	});


	}); //window

})( jQuery );
