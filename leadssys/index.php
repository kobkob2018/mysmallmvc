<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
require_once('../../../global_func/vars.php');
if(!isWebsiteSecure()){
	$https_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	header( "HTTP/1.1 301 Moved Permanently" );
	header( "location: ".$https_link."" );
}
require("../../../global_func/global_functions.php");
require("../../../global_func/class.phpmailer.php");
require_once('connection.php');
require_once('routes.php');
require_once('models/model.php');
if (isset($_GET['controller']) && isset($_GET['action'])) {
	$controller = $_GET['controller'];
	$action     = $_GET['action'];
} else {
	$controller = 'leads';
	$action     = 'all';
}

print_view($controller,$action);
function utpr($val){
	echo utgt($val);
}
function utgt($val){
	return iconv("windows-1255//IGNORE", "UTF-8//IGNORE",$val);
}
function wipr($val){
	echo wigt($val);
}
function wigt($val){
	return iconv("UTF-8","windows-1255",$val);
}
function hebdt($datetime_str){
	$date = new DateTime($datetime_str);
	return $date->format('d-m-Y H:i:s');
}
 
?>