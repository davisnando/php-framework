<?php
require('classes/model_functions.php');
require('settings/config.php');    
if(strtolower(DEBUG) == "true"){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
if(isset($argv)){
    if($argv[1] == "migrate"){
        RunMigrates();

    }else if($argv[1] == "insertData"){
        inserts();    
    }
}
else{
    if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
        return false;
    } else {
        $root=__dir__;
        $uri=parse_url($_SERVER['REQUEST_URI'])['path'];
        $page=trim($uri,'/');   
    
        if (file_exists("$root/$page") && is_file("$root/$page")) {
            return false; // serve the requested resource as-is.
            exit;
        }
        $_GET['path']=$page;
        require_once('__init__.php');
    }
}