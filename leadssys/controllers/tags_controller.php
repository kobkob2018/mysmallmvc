<?php
	class TagsController extends Controller{
		public $add_models = array("tags");
		public function settings(){
			if(isset($_REQUEST['add_tag'])){
				
				Tags::add_tag($_REQUEST['tag_data']);
				$messege = "התיוג עודכן בהצלחה";
			}
			if(isset($_REQUEST['delete_tag'])){
				Tags::delete_tag($_REQUEST['tag_data']);
				$messege = "התיוג נמחק";
			}
			$tag_list = Tags::get_user_tag_list();
			include('views/tags/tags_settings.php');
		}		
	}
?>