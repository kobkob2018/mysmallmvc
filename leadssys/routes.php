<?php
  function call($controller, $action) {
	
    
	$controller_class = ucfirst($controller)."Controller";
	
	$controller = new $controller_class();
    $controller->print_layout($action);

    //$controller->{ $action }();
  }
  
  function print_view($controller,$action){
		require_once('controllers/controller.php');
		// we're adding an entry for the new controller and its actions
		require_once('controllers/pages_controller.php');
		if(file_exists('controllers/' . $controller . '_controller.php')){
			require_once('controllers/' . $controller . '_controller.php');
			if(method_exists(ucfirst($controller)."Controller",$action)){
				call($controller, $action);
			}
			else{
				 call('pages', 'error');
			}
		}
		else{
			 call('pages', 'error');
		}
	}

?>