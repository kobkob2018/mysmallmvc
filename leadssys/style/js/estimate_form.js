	
	var current_selected_cat = 0;
	var category_select_name = "";	
	var validator = 0;
	function validatePhone(phone) {
		
		if (phone.length > 8) {
			return true;
		}
		else {
			return false;
		}
	}
	function etsimate_form_handler(){
		jQuery(function($){
			validator = $("#estimate_form").validate({
				errorPlacement: function(error, element) {
					error.insertBefore(element);
				},
				messages: {
					required: "*",
				}				
			});
			jQuery.validator.addMethod("phoneNumber", function (phone_number, element) {
				return this.optional(element) || validatePhone(phone_number);
			});			
			var select_trigers = false;
			$("#estimate_form").find(".cat_updater_new").each(function(){
				select_trigers = true;
				$(this).removeClass("cat_updater_new");
				$(this).change(function () {
					$("#estimate_form").validate().resetForm();
					setup_estimate_form_extra_fields($(this).attr("name"));					
				});
			});	
			$("#estimate_form").find(".cat_updater_helper").each(function(){
					setup_estimate_form_extra_fields($(this).attr("rel"));
			});
			if(select_trigers){
				$("#estimate_form").find("#submitit").each(function(){
					$(this).attr("disabled", false);
					$(this).attr("value",$(this).attr("rel_ready"));
				});
			}			
		});		
	}	
	
	function setup_estimate_form_extra_fields(cat_input_id){
		jQuery(function($){
			$("#estimate_form").find(".extra-field").each(function(){
				$(this).remove();
			});	
				
			
			$("#estimate_form").find(".select_"+cat_input_id+"_son").each(function(){
				$(this).remove();
			});					
			
			if($("#"+cat_input_id).length != 0){
				
				$("#estimate_form").find("#submitit").each(function(){
					$(this).attr("disabled", true);
					$(this).attr("value",$(this).attr("rel_loading"));
				});
				var cat_val = $("#"+cat_input_id).val();
				$('body').append("<div style='display:block;' id='estimate_extra_fields_helper'><div/>");
				var params = "&from_cat="+cat_val+"&cat_level="+cat_input_id;
				if($("#estimate_form").find("#Fm_city").hasClass("no-replace")){
					params += "&no_city=1";
				}
				var ef_url = "https://ilbiz.co.il/ClientSite/ajax.php?main=estimateCatExtraFileds&version=version_1" + params ;
				jQuery.ajax({
					url: ef_url,
					type: 'GET',
					success: function (transport) {
						document.getElementById("estimate_extra_fields_helper").innerHTML = transport;
						$("#estimate_extra_fields_helper").find(".cat_tree_ajax_wrap").each(function(){
							//$("#estimate_form").append($(this));
							$(this).insertBefore( "#estimate_form #cat_tree_select_place" );
							$(this).find(".cat_updater_new").each(function(){
								$(this).removeClass("cat_updater_new");
								$(this).change(function(){
									$("#estimate_form").validate().resetForm();
									setup_estimate_form_extra_fields($(this).attr("name"));
								}); 
							});
							
						});
						$("#estimate_extra_fields_helper").find(".form-extra").each(function(){
							//$("#estimate_form").append($(this));
							$(this).insertBefore( "#estimate_form #Fm_note_wrap" );
							$(this).addClass("extra-field");
							
						});
						
						$("#estimate_extra_fields_helper").remove();
						$('.estimate-date').each(function(){
							$(this).datepicker({format: 'dd/mm/yyyy',autoclose:true,language: 'he',isRTL: false,todayHighlight: true});
						});
						$("#estimate_form").find("#submitit").each(function(){
							$(this).attr("disabled", false);
							$(this).attr("value",$(this).attr("rel_ready"));
						});
					},
					error: function () {
						console.log("err:c_c_f.p(1250)");
					}
				});		
			}
		});
	}
	
	function ajax_estimateForm_send()
	{
		jQuery("#estimate_form [name='temp_name']").remove();
		var queryArray = jQuery("#estimate_form").serializeArray();
		jQuery.each( queryArray, function( i, field ) {
		  //can manipulate values here
		});
		jQuery("#estimate_form").find("#submitit").each(function(){
			jQuery(this).attr("disabled", true);
			jQuery(this).attr("value",jQuery(this).attr("rel_sending"));
		});		
		var queryString = jQuery.param(queryArray);
		console.log(queryString);
		var ef_url = "https://ilbiz.co.il/ClientSite/ajax.php?main=estimateSiteInsert&add_wach_link=1&" + queryString ;
		jQuery.ajax({
			url: ef_url,
			type: 'GET',
			success: function (transport) {
				document.getElementById("estimateSiteHeightDiv").innerHTML = transport;
			},
			error: function () {
				console.log("err:c_c_f.p(1250)");
			}
		});	
	}
	var scrltop = 0;
	function open_mobile_estimate_form(el){
		
		if(jQuery(el).attr('rel') == 'closed'){
			jQuery(el).removeClass('closed').addClass('open').attr('rel','open');
			jQuery("#mobile_estimate_form_open_button_wrap").removeClass('closed').addClass('open');
			scrltop = jQuery(window).scrollTop();
			jQuery('#mobile_estimate_form_wrap').show();
			if(jQuery("#page_outer_wrap").length != 0){
				jQuery('#page_outer_wrap').hide();
			}
			if(jQuery("#page_wrap").length != 0){
				jQuery('#page_wrap').hide();
			}
			jQuery(window).scrollTop(0);
		}
		else{
			console.log("close");
			jQuery(el).removeClass('open').addClass('closed').attr('rel','closed');
			jQuery("#mobile_estimate_form_open_button_wrap").removeClass('open').addClass('closed');
			jQuery('#mobile_estimate_form_wrap').hide();
			if(jQuery("#page_outer_wrap").length != 0){
				jQuery('#page_outer_wrap').show();
			}
			if(jQuery("#page_wrap").length != 0){
				jQuery('#page_wrap').show();
			}
			jQuery(window).scrollTop(scrltop);
		}
		
		
	}
		
	jQuery(document).ready(function(){
		
		jQuery(function($){
			if ($("#mobile_estimate_form_open_button").length){
				estimate_form_auto_popup();
			}
		});		
		/*
		if(browser_is_mobile == "true"){
			estimate_form_auto_popup();
		}
		*/
	});
	var nearBottomReached = false;
	function estimate_form_auto_popup(){
		
		var efapc = get_estimate_form_auto_popup_cookie("efapc");
		var popup_do = false;
		if (efapc == null || efapc == "") {
			set_estimate_form_auto_popup_cookie("efapc", "1", 10);
			popup_do = true;
		} else {
			//alert('cookie installed already');
		}		
		
		if(popup_do){
			setTimeout(function(){estimate_form_button_click();},20000);
		}
		jQuery(function($){
			$(window).scroll(function() {
			   if($(window).scrollTop() + $(window).height() > $(document).height()-100 && !nearBottomReached) {
				  estimate_form_button_click();
				  nearBottomReached = true;
			   }
			   if($(window).scrollTop() + $(window).height() < $(document).height()-500) {
				  nearBottomReached = false;
			   }			   
			});	
		});			
	}
	function estimate_form_button_click(){
		jQuery(function($){
			$("#mobile_estimate_form_open_button").find(".closed").each(function(){$(this).click()});
		});
	}

	

	function set_estimate_form_auto_popup_cookie(c_name,value,exdays)
	{
		var exdate=new Date();	
		exdate.setDate(exdate.getDate() + exdays);	
		var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString())  + "; path=/";   
	   document.cookie=c_name + "=" + c_value;
	}

	function get_estimate_form_auto_popup_cookie(c_name) {
		var i, x, y, ARRcookies = document.cookie.split(";");
		for (i = 0; i < ARRcookies.length; i++) {
			x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
			y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
			x = x.replace(/^\s+|\s+$/g,"");
			if (x == c_name) {
				return unescape(y);
			}
		}
	}

	