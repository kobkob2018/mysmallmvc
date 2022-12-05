jQuery(function($){
	$(document).ready(function(){
		$(".form-validate").validate();
		$('.datepicker-input').datepicker({
			format: 'dd/mm/yyyy',
			autoclose: true
		});
		$(document).click(function(event) { 
			if(!$(event.target).closest('#notifications_wrap').length && !$(event.target).closest('#header_notifications_door').length) {
				if($("#header_notifications_door").attr("rel") == 'open'){
					$("#header_notifications_door").click();
				}
			}
			if(!$(event.target).closest('#usermenu_wrap').length && !$(event.target).closest('#header_usermenu_door').length) {
				if($("#header_usermenu_door").attr("rel") == 'open'){
					$("#header_usermenu_door").click();
				}
			}			
		});		
	});
});

function update_notifications(){
	jQuery(function($){
		//begin_ajax_call_view();
		var url = "notifications/get_list/";
		$.ajax({
		  url: url,
		}).done(function( data ) {
			$("#notifications_recived_content").html(data);

			
		}).error(function(){
			//sudjest_refresh_page();
		});
	});	
}

function check_notifications_interval(){
	setInterval(function(){check_notifications()},60000);
}

function check_notifications(){
	if($("#header_notifications_door").attr("data-touched") == '1'){
		return;
	}
	jQuery(function($){
		//begin_ajax_call_view();
		var url = "notifications/check_list/";
		$.ajax({
		  url: url,
		}).done(function( data ) {
			if(data == 'true'){
				$("#notifications_bugger_wrap").show();
				setTimeout(function(){$("#notifications_bugger_wrap").hide();},7000);
			}
		}).error(function(){
			//sudjest_refresh_page();
		});
	});	
}

function open_close_filter(el){
	jQuery(function($){

		if($(el).hasClass("open")){

			$(el).removeClass("open").addClass("closed");
			$("#filter_form_wrap").animate({right:"-400px"});
		}
		else{
			$(el).removeClass("closed").addClass("open");
			$("#filter_form_wrap").animate({right:"-1px"});	
			var window_height = jQuery(window).height();
			var footer_height = jQuery("#footer").outerHeight();
			var table_pos_top = jQuery("#filter_form_wrap").position().top;
			var fit_height = window_height - footer_height - table_pos_top - 50;			
			jQuery("#filter_form_wrap").attr("style","max-height: "+fit_height+"px; overflow: auto;");			
		}
	});	
}

function open_close_notifications(){
	$("#header_notifications_door").attr("data-touched","1");
	$("#notifications_bugger_wrap").hide();
	if($("#header_notifications_door").attr("rel") == "closed"){
		$("#header_notifications_door").attr("rel","open");
		$("#notifications_wrap").show();
		var window_width = jQuery(window).width();
		if(window_width <520){
			jQuery("#notifications_content").css("width",(window_width-20)+"px");
		}
		else{
			jQuery("#notifications_content").css("width","500px");
		}
		update_notifications();
	}
	else{
		$("#header_notifications_door").attr("rel","closed");
		$("#notifications_wrap").hide();
	}
}
function hide_notifications_bugger(){
	$("#header_notifications_door").attr("data-touched","1");
	$("#notifications_bugger_wrap").hide();
	$("#notifications_wrap").show();
	var window_width = jQuery(window).width();
	if(window_width <520){
		jQuery("#notifications_content").css("width",(window_width-20)+"px");
	}
	else{
		jQuery("#notifications_content").css("width","500px");
	}
	update_notifications();

}
function open_close_usermenu(){
	if($("#header_usermenu_door").attr("rel") == "closed"){
		$("#header_usermenu_door").attr("rel","open");
		$("#usermenu_wrap").show();
		var window_width = jQuery(window).width();
		if(window_width <520){
			jQuery("#usermenu_content").css("width",(window_width-20)+"px");
		}
		else{
			jQuery("#usermenu_content").css("width","500px");
		}
	}
	else{
		$("#header_usermenu_door").attr("rel","closed");
		$("#usermenu_wrap").hide();
	}
}

function moreDate(id1)
{
	obj = document.getElementById("line_"+id1).style.display;		
	document.getElementById("line_"+id1).style.display=(obj?"":"none");
	jQuery(function($){
		//begin_ajax_call_view();
		var url = "notifications/mark_as_read/?messege_id="+id1;
		$.ajax({
		  url: url,
		});
	});		
	
}