var leadsApp = angular.module('leadsApp', []);
leadsApp.controller('leadsCtrl', function($scope, $http) {
	$scope.lead_data = false;
	$scope.small_window = '1';
	$scope.removelead = function(){$scope.lead_data = false;};
	$scope.user = false;
	$scope.send_filter = function(){
		if(jQuery(window).width()>800){
			$scope.small_window = '0';
		}		
		var filter_data = jQuery("#leads_filter_form").serialize();
		$scope.close_lead_view();
		
		$scope.show_loading();
		
		$http({
			method : "GET",
			url : "https://ilbiz.co.il/myleads/leads/ajax_list/?"+filter_data,
		})
		.then(
			function leadsSuccess(response) {

				var return_array = response.data;
				if(!($scope.manage_user(return_array['user']))){
					return;
				}
				$scope.leadsList = return_array['list'];
				$scope.pagesData = return_array['pages_data'];
				$scope.filter = return_array['filter'];
				setTimeout(function(){
					jQuery(".page-option-wrap").removeClass("selected");
					if($scope.filter['page_num'] == '1'){
						jQuery("#page_option_first").addClass("selected");
					}
					if($scope.pagesData['page_count'] == $scope.filter['page_num']){
						jQuery("#page_option_last").addClass("selected");
						jQuery(".pagination-next ").addClass("no-more");
					}
					else{
						jQuery(".pagination-next").removeClass("no-more");
					}
					if($scope.filter['page_num'] == '1'){
						jQuery(".pagination-previos ").addClass("no-more");
					}
					else{
						jQuery(".pagination-previos").removeClass("no-more");
					}
				},0);
				if(jQuery("#fixColTable").length){
					jQuery("#fixColTable").remove();
					jQuery("#cornerCell").remove();
					jQuery("#headTable").remove();
				}

				if(jQuery(window).width()>800){

					jQuery("#listTable").find("td,th").each(function(){
						jQuery(this).attr("style","");
					});
					jQuery("#listTable").find("thead").show();
					setTimeout(function(){

						var window_height = jQuery(window).height();
						var footer_height = jQuery("#footer").outerHeight();
						var table_pos_top = jQuery("#listTable_wrap").position().top;
						var fit_height = window_height - footer_height - table_pos_top;
						jQuery("#listTable_wrap").attr("style","width: 100%;max-height: "+fit_height+"px; overflow: auto;position:relative;");

						jQuery("#listTable").find("th").each(function(){
							jQuery(this).css("min-width",jQuery(this).width()).css("max-width",jQuery(this).width());
							jQuery(this).css("height",jQuery(this).height());
						});
						jQuery("#listTable tbody").find("tr:first").find("td").each(function(){
							jQuery(this).css("min-width",jQuery(this).width()).css("max-width",jQuery(this).width());
							jQuery(this).css("height",jQuery(this).height());
						});					
						var $headTable = jQuery("#listTable").clone();
						$headTable.attr("id","headTable");
						$headTable.find("tbody").remove();
						var $cornerCell = $headTable.clone();
						jQuery("#listTable").find("thead").hide();
						$cornerCell.attr("id","cornerCell");
						$cornerCell.find("th").not(":first").remove();
						$cornerCell.appendTo("#listTable_header_wrap"); 
						$cornerCell.attr("style","position: sticky;right: 0px;top: 0px;margin-bottom: -"+$cornerCell.height()+"px;background: #f7f7f7;z-index: 200;");
						$headTable.appendTo("#listTable_header_wrap"); 
						$headTable.attr("style","position: sticky;top: 0px;background: #f7f7f7;");
						var $fixColTable = jQuery("#listTable").clone();
						$fixColTable.attr("id","fixColTable");
						$fixColTable.find("thead").remove();
						$fixColTable.find("td").not(".row_id_col").remove();
						$fixColTable.appendTo("#listTable_header_wrap"); 
						$fixColTable.attr("style","position: sticky;right: 0px;margin-bottom: -"+($fixColTable.height()+2)+"px;background: #f7f7f7;");
						jQuery("#listTable_wrap").scrollTop(0);
						$scope.hide_loading();
						$scope.check_auto_show_row();
					},0);
				}
				else{
					setTimeout(function(){
						var window_height = jQuery(window).height();
						var footer_height = jQuery("#footer").outerHeight();
						var table_pos_top = jQuery("#listTable_wrap").position().top;
						var fit_height = window_height - footer_height - table_pos_top;
						jQuery("#listTable_wrap").attr("style","width: 100%;max-height: "+fit_height+"px; overflow: auto;position:relative;");

						jQuery("#listTable").find("th").each(function(){
							jQuery(this).css("min-width",jQuery(this).width()).css("max-width",jQuery(this).width());
							jQuery(this).css("height",jQuery(this).height());
						});
						jQuery("#listTable_wrap").scrollTop(0);
						$scope.hide_loading();
						$scope.check_auto_show_row();
					},0);
				}
			}, 
			function myError(response) {
				console.log(response.statusText);
			}
		);		
	};
	

	$scope.show_lead = function($event){
		var el = angular.element($event.target).parent();
		if(jQuery(window).width()>800){	
			var lead_id = jQuery(el).attr("data-lead_id");
			$scope.show_lead_final(lead_id);
		}
		else{
			if(jQuery(el).hasClass("responsive-small")){
				jQuery(el).removeClass("responsive-small");
			}
			else{
				jQuery(el).addClass("responsive-small");
			}
		}
	};
	
	$scope.show_lead_respo = function($event){
		var el = angular.element($event.target);
		var lead_id = jQuery(el).attr("data-lead_id");
		$scope.show_lead_final(lead_id);
	};	
	
	$scope.show_lead_final = function(lead_id){
		$scope.close_lead_view();
		$scope.show_loading();
		$http({
			method : "GET",
			url : "https://ilbiz.co.il/myleads/leads/ajax_lead_data/?lead_id="+lead_id,
		})
		.then(
			function leadsSuccess(response) {
				var return_array = response.data;
				if(!($scope.manage_user(return_array['user']))){
					return;
				}
				$scope.lead_data = return_array['lead']['estimate_form_data'];
				console.log($scope.lead_data);
				setTimeout(function(){
					var window_height = jQuery(window).height();
					var footer_height = jQuery("#footer").outerHeight();
					jQuery("#lead_edit_view").find(".tab-content").each(function(){
						var tab_pos_top = jQuery(this).position().top;
						var fit_height = window_height - footer_height - tab_pos_top;
						jQuery(this).attr("style","width: 100%;max-height: "+fit_height+"px; overflow: auto;position:relative;");	
					});
					jQuery("#lead_content_textarea").height(jQuery("#lead_content_textarea").prop('scrollHeight'));
					$scope.hide_loading();
				},0);				
				
			}, 
			function myError(response) {
				console.log(response.statusText);
			}
		);
	};	
	$scope.check_auto_show_row = function(){
		if(jQuery("#filter_auto_show_row").length){
			var row_id = jQuery("#filter_auto_show_row").val();
			jQuery("#filter_auto_show_row").remove();
			$scope.show_lead_final(row_id);
		}
	};
	$scope.manage_user = function(user_data){
		if(typeof(user_data) == 'undefined'){
			return false;
		}
		if(user_data['login'] == '0'){
			location.reload();
			return false;
		}
		else{
			if($scope.user){
				if($scope.user['id'] != user_data['id']){
					alert("התחלף משתמש");
					location.reload();
					return false;
				}
			}
			$scope.user = user_data;
			return true;
		}
	}	
	$scope.update_filter = function(){
		$scope.go_to_page("1");
	};
	$scope.result_not_empty = true;
	$scope.go_to_page = function(page_id){
		if(page_id == "first"){
			$scope.filter['page_num'] = "1";
			jQuery("#pagination_select_door").click();
		}
		else if(page_id == "last"){
			$scope.filter['page_num'] = $scope.pagesData['page_count'];
			jQuery("#pagination_select_door").click();
		}
		else{
			$scope.filter['page_num'] = page_id;
		}
		jQuery("#page_input").val($scope.filter['page_num']);
		$scope.send_filter();
	}
	$scope.go_to_next_page = function(){
		if($scope.filter['page_num'] >= $scope.pagesData['page_count']){
			return;
		}
		else{
			$scope.go_to_page($scope.filter['page_num']-1+2);
		}
	}	
	$scope.go_to_previos_page = function(){
		if($scope.filter['page_num'] == 1){
			return;
		}
		else{
			$scope.go_to_page($scope.filter['page_num']-1);
		}
		
	}
	$scope.change_page_size = function(leads_in_page){
		$scope.filter['leads_in_page'] = leads_in_page;
		jQuery("#leads_in_page_input").val($scope.filter['leads_in_page']);
		$scope.go_to_page(1);
		jQuery(".pagination-option-wrap").removeClass("selected");
		jQuery("#pagination_option_"+leads_in_page).addClass("selected");
		jQuery("#pagination_select_door").click();
	}
	$scope.close_lead_view	= function(){$scope.lead_data = false;};
	$scope.open_lead_sub_form = function(sub_form){
		jQuery("#"+sub_form).show();
	};
	$scope.close_lead_sub_form = function(sub_form){
		jQuery("#"+sub_form).hide(); 
	};	
	$scope.delete_lead = function(){
		var lead_id = $scope.lead_data['row_id'];
		$scope.show_loading();
		$http({
			method : "GET",
			url : "https://ilbiz.co.il/myleads/leads/ajax_lead_delete/?lead_id="+lead_id,
		})
		.then(
			function leadsSuccess(response) {
				var return_array = response.data;
				if(!($scope.manage_user(return_array['user']))){
					return;
				}
				$scope.lead_data = return_array['lead']['estimate_form_data'];
				if($scope.lead_data['deleted'] == '1'){
				}
				$scope.lead_data = false;
				$scope.send_filter();
			}, 
			function myError(response) {
				console.log(response.statusText);
			}
		);
	};	
	$scope.update_lead = function(){
		var lead_id = $scope.lead_data['row_id'];
		var update_data = jQuery("#lead_update_form").serialize();
		$scope.show_loading();
		$http({
			method : "GET",
			url : "https://ilbiz.co.il/myleads/leads/ajax_lead_update/?lead_id="+lead_id+"&"+update_data,
		})
		.then(
			function leadsSuccess(response) {
				var return_array = response.data;
				if(!($scope.manage_user(return_array['user']))){
					return;
				}
				$scope.lead_data = return_array['lead']['lead']['estimate_form_data'];
				$scope.hide_loading();
				$scope.show_msg("הליד עודכן בהצלחה");
				//$scope.send_filter();
			}, 
			function myError(response) {
				console.log(response.statusText);
			}
			
		);
	};	
	$scope.send_lead_refund_request = function(){
		var lead_id = $scope.lead_data['row_id'];
		var request_data = "&"+jQuery("#refund_request_form").serialize();
		console.log("https://ilbiz.co.il/myleads/leads/ajax_send_lead_refund_request/?lead_id="+lead_id+request_data);
		$scope.show_loading();
		$http({
			method : "GET",
			url : "https://ilbiz.co.il/myleads/leads/ajax_send_lead_refund_request/?lead_id="+lead_id+request_data,
		})
		.then(
			function leadsSuccess(response) {
				var return_array = response.data;
				if(!($scope.manage_user(return_array['user']))){
					return;
				}
				$scope.lead_data = return_array['lead']['estimate_form_data'];
				$scope.send_filter();
			}, 
			function myError(response) {
				console.log(response.statusText);
			}
			
		);
	};	

	$scope.buy_lead = function(){
		var lead_id = $scope.lead_data['row_id'];
		$scope.show_loading();
		$scope.close_lead_sub_form("buy_lead_sub_form");
		$http({
			method : "GET",
			url : "https://ilbiz.co.il/myleads/leads/ajax_lead_buy/?lead_id="+lead_id,
		})
		.then(
			function leadsSuccess(response) {
				var return_array = response.data;
				if(!($scope.manage_user(return_array['user']))){
					return;
				}
				$scope.lead_data = return_array['lead']['estimate_form_data'];
				if(return_array['success'] != '1'){
					if(return_array['fail_reason'] = 'no_credit'){
						$scope.open_lead_sub_form("no_credits_alert_sub_form");
					}
				}
				$scope.hide_loading();
			}, 
			function myError(response) {
				console.log(response.statusText);
			}
		);
	};
	$scope.show_loading = function(){
		$("#loading_mask").show();
	};	
	$scope.hide_loading = function(){
		$("#loading_mask").hide();
	};
	$scope.show_msg = function(msg_str){
		$scope.show_msg_box(msg_str);
		setTimeout(function(){$scope.hide_msg_box();},2000);
	};
	$scope.show_msg_box = function(msg_str){
		$("#leads_messege_holder").html(msg_str);
		$("#leads_messege_mask").show();
	};	
	$scope.hide_msg_box = function(){
		$("#leads_messege_holder").html("");
		$("#leads_messege_mask").hide();
	};		
	$scope.send_filter();
});

jQuery(function($){
	$(document).ready(function(){
		$(".checkbox_wrap").click(function(){
			var checkbox = $(this).find(".checkbox").click();
		});
		$(".period-option").click(function($event){
			$(".period-option-wrap").removeClass("selected");
			$("#period_option_"+$(this).attr("data-val")).addClass("selected");
			$("#period_select_val").val($(this).attr("data-val"));
			if($(this).attr("data-val") == "custom"){
				$("#period_select_text").val($("#period_custom_from").val()+" - "+$("#period_custom_to").val());
				$("#period_select_text").addClass("period_select_text_custom");
			}
			else{
				$("#period_select_text").val($(this).attr("data-str"));
				$("#period_select_text").removeClass("period_select_text_custom"); 
			}
			$("#leads_filter_send").click();
		});
		$("#period_select_text").click(function(){
			if($(this).hasClass("open")){
				$(this).removeClass("open");
				$("#period_options").hide();
			} 
			else{
				$(this).addClass("open");
				$("#period_options").show();				
			}
		});		
		$(document).click(function(event) { 
			if(!$(event.target).closest('#period_options').length && !$(event.target).closest('#period_select_text').length) {
				if($("#period_select_text").hasClass("open")){
					$("#period_select_text").click();
				}
			}
			if(!$(event.target).closest('#pagination_select').length && !$(event.target).closest('#pagination_select_door').length) {
				if($("#pagination_select_door").hasClass("open")){
					$("#pagination_select_door").click();
				}
			} 			
		});
		
	});

});
function open_pegination_select(el_id){
	jQuery(function($){
		if($(el_id).hasClass("open")){
			$(el_id).removeClass("open");
			$("#pagination_select").hide();
		} 
		else{
			$(el_id).addClass("open");
			$("#pagination_select").show();				
		}			
	});
}