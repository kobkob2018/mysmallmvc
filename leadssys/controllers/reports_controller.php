<?php
	class ReportsController extends Controller{
		public $add_models = array("reports");
		public function banners(){
			$user_banners_data = Reports::get_user_banners_data();
			include('views/reports/banners.php');
		}	
}	

?>