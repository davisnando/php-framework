<?php
session_start();
require_once("settings/config.php");
require_once("classes/model.php");
if(strtolower(DEBUG) == "true"){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
require_once("settings/functions.php");
$var = "^".$_GET['path'];
Run("settings/urls.php", $var);
function Run($urlfile, $LocationUrl,$oldpath = null){
    require($urlfile);
    if($LocationUrl != ""){
        if(substr($LocationUrl, -1) == "/"){
            $LocationUrl = str_replace("/","",$LocationUrl);
        }
    }
    
    $path1 = explode('/',$LocationUrl);
    if(array_key_exists($LocationUrl,$url)){
        CheckForUrls($url[$LocationUrl],$LocationUrl);
    }else if(array_key_exists($LocationUrl."$",$url)){
        CheckForUrls($url[$LocationUrl."$"],$LocationUrl);
    }
    for($i = count($path1) - 1 ; $i >= 0; $i--){
        $key = GetKeyName($path1, $i);
        if(array_key_exists($key,$url)){
            CheckForUrls($url[$key],$key ,$LocationUrl);
        }
    }  
    RunForStatic();
    
}
function GetKeyName($pathar, $index){
    $var = count($pathar) - 1 - $index;
    $newKeyname = "";
    for($i = 0; $i < count($pathar) - $var; $i++){
        if($i == 0){
            $newKeyname = $pathar[$i];
        }else{
            $newKeyname = $newKeyname.'/'.$pathar[$i];   
        }
    }
    return $newKeyname;
}
function CheckForUrls($incomming,$keyname,$LocationUrl = null){
    if(preg_match('/urls.php/',$incomming)){
        if($LocationUrl == null){
            Run($incomming,$_GET['path']);            
        }else{
            $path1 = explode('/',$LocationUrl);
            if(count($path1) > 1){            
                $newpath = str_replace($keyname."/","",$LocationUrl);
            }else{
                $newpath = str_replace($keyname,"",$LocationUrl);
                
            }
            Run($incomming,$newpath,$LocationUrl);                        
        }
    }else{
        visit();                
        $incomming();
        die();
    }
}
function RunForStatic(){
    LoadStatic();
    global $static;
    $FullPath = $_GET['path'];
    $items = explode('/',$FullPath);
    $item = $items[0];
    if(array_key_exists($item,$static)){    
        returnFile($static[$items[0]].'/'.$items[1]);
    }
}
?>