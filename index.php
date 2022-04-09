<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

spl_autoload_register(function($classname){
    include "classes/$classname.php";
});
session_start(); 

$path = parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
$path = str_replace("/HoosHere/","",$path);
$parts = explode("/",$path);


if(!isset($_SESSION["email"])){
    
    //need to login 
    $parts=["login"];   
}

;
$controller = new Controller();
$controller->run($parts[0]);?>