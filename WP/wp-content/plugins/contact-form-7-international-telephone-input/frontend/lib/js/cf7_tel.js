jQuery(document).ready(function($) {
	$( ".wpcf7-tel" ).each(function( index ) { 
		var onlyCountries_data = $(this).data("onlyct");
		var preferredCountries_data = $(this).data("pre");
		var geoIpLookup_data = $(this).data("auto");
		var initialCountry_data = $(this).data("defcountry");
		var data = [];
		if (onlyCountries_data == "") { 
			onlyCountries_data = [];
		}else{
			onlyCountries_data = onlyCountries_data.split('|');
		}
		
		if (preferredCountries_data == "") { 
			preferredCountries_data = [ "us", "gb" ];
		}else{
			preferredCountries_data = preferredCountries_data.split('|');
		}

		if (initialCountry_data == "") { 
			initialCountry_data = "auto";
		}
		//data.push({preferredCountries:preferredCountries_data});
		//data.push({initialCountry:initialCountry_data});
		//data.push({utilsScript:cf7_tel.utilsScript});
		//data.push({separateDialCode:true});
		//console.log(onlyCountries_data);
		
		if( $(this).data("auto") == 1 ){
			$( this ).intlTelInput({
				onlyCountries: onlyCountries_data,
				initialCountry: initialCountry_data,
				preferredCountries: preferredCountries_data,
				utilsScript: cf7_tel.utilsScript,
				separateDialCode: true,
				geoIpLookup: function(success, failure) {
				    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
				      var countryCode = (resp && resp.country) ? resp.country : "";
				      success(countryCode);
				    });
				 },
			});
		}else{
			$( this ).intlTelInput({
				onlyCountries: onlyCountries_data,
				initialCountry: initialCountry_data,
				preferredCountries: preferredCountries_data,
				utilsScript: cf7_tel.utilsScript,
				separateDialCode: true
			});
		}
	})

	$("body").on("blur",".wpcf7-validates-as-tel",function(){
		var content = $.trim($(this).val());
		console.log( $(this).intlTelInput("isValidNumber") );
		if( $(this).data("validation") == 1 ) {
			if ($(this).intlTelInput("isValidNumber")) { 
				console.log( $(this).intlTelInput("isValidNumber") );
				$(this).addClass('wpcf7-not-valid-blue').removeClass('wpcf7-not-valid-red');
				var number_tel = $(this).intlTelInput("getNumber");
				$(this).val(number_tel);
			}else{
				$(this).addClass('wpcf7-not-valid-red').removeClass('wpcf7-not-valid-blue');
			}
		}
	})
	$("body").on("focus",".wpcf7-validates-as-tel",function(){
		$(this).removeClass('wpcf7-not-valid-blue').removeClass('wpcf7-not-valid-red');
	})
});