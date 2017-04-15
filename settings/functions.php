<?php
$templates = array();
$static = array();

/**
    load static files in
**/
function LoadStatic(){
    $dirs = glob('*', GLOB_ONLYDIR);
    $static = [];
    for($i = 0; $i < count($dirs); $i++){
        $dir = $dirs[$i];
        searchdir($dir."/static");
    }
}
/**
    Search every folder for a static folder
**/
function searchdir($path){
    if(is_dir($path)){
        global $static;
        $items = scandir($path);
        //remove .. and . items from directory list
        array_splice($items, 0, 1);
        array_splice($items, 0, 1);
        foreach($items as $item){
            $static[$item] = $path.'/'.$item;
        }
    }
}
/**
    $keyname = subdirectory name or filename in static folder
    $filename = filename in subdirectory
**/
function GetStaticFile($keyname,$filename = null){
    global $static;
    if(array_key_exists($keyname,$static)){
        if(is_dir($static[$keyname])){
            $path = $static[$keyname].'/'.$filename;
            if(file_exists($path)){
                echo $path;
            }else{
                echo "";
            }
        }else{
            echo $static[$keyname];
        }
    }else{
        echo "";
    }
}
/**
    search through folders for a template folder
**/
function searchtemplates($path){
    if(is_dir($path)){
        global $templates;
        $items = scandir($path);
        //remove .. and . items from directory list
        array_splice($items, 0, 1);
        array_splice($items, 0, 1);
        foreach($items as $item){
            $templates[$item] = $path.'/'.$item;
        }
    }
}
/**
    Load all templates folder
**/
function LoadTemplates(){
    $dirs = glob('*', GLOB_ONLYDIR);
    $static = [];
    for($i = 0; $i < count($dirs); $i++){
        $dir = $dirs[$i];
        searchtemplates($dir."/templates");
    }
}
/**
$keyname = subdirectory name or filename in templates folder
$filename = filename in subdirectory
**/
function GetTemplate($keyname,$filename = null){
    global $templates;
    if(array_key_exists($keyname,$templates)){
        if(is_dir($templates[$keyname])){
            $path = $templates[$keyname].'/'.$filename;
            if(file_exists($path)){
                require( $path);
            }else{
                return;
            }
        }else{
            require( $templates[$keyname]);
        }
    }else{
        return ;
    }
}