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
                echo '/'.$keyname.'/'.$filename;
            }else{
                echo "";
            }
        }else{
            echo "";
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
/** 
Get File type from extension
feel free to add more content type
**/
function getFileMimeType($file) {
    $images = ['gif','jpg','jpeg','png'];
    $icons = ['ico'];
    $stylesheets = ['css'];
    $javascript = ['js'];
    $array = explode('.',$file);
    $extension = end($array);
    if(in_array($extension,$images)){
        return "image/".$extension;
    }else if(in_array($extension,$stylesheets)){
        return "text/".$extension;
    }else if(in_array($extension,$javascript)){
        return "application/javascript";
    }else if(in_array($extension,$icons)){
        return "image/x-icon";
    }
    return "";
}
function createInput($type, $name,$value,$prop = []){
    $input = ["button", "checkbox", "file","hidden","image","password","radio","reset","submit","text"];
    $bootstrap = True;
    $classes = "";
    $propstring = "";

    if(array_key_exists("bootstrap",$prop)){
        if($prop['bootstrap'] == False){
            $bootstrap = False;
        }
    }
    if(array_key_exists("class",$prop)){
        $classes = $prop['class'];
    }
    if(array_key_exists("props",$prop)){
        $propstring .= $prop['props'];
    }
    if($bootstrap){
        $propstring .= " class='$classes form-control'";
    }else if($classes != ""){
        $propstring .= " class='$classes'";
    }
    if(in_array($type,$input)){
        $val = " value='$value'";
        if($type == "text" || $type == "password"){
            $val = " placeholder='$value'";
        }
        echo "<input type='$type' id='$name'  name='$name' $val $propstring>";
    }else if($type == "textarea"){
        echo "<textarea name='$name' id='$name' $propstring placeholder='$value' ></textarea>";
    }else{
        echo "<script>console.log('input not recognized');</script>";
    }

}
function RandomName($filename){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 10; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return hash('sha1',$randomString);
}
function upload($file,$whitelist_Ext,$whitelist_Type){
    if(empty($file)){
        echo "empty";
        die();
    }
    $path = "uploads/";
    $filename = $file['name'];
    $fileType = $file['type'];
    $file_parts = pathinfo($filename);
    $ext = $file_parts['extension'];
    if(!in_array($fileType,$whitelist_Type) ){
       echo "invalid file type";
       die();
    }
    if(!in_array($ext,$whitelist_Ext)){
       echo "invalid file extension";
       die();
    }
    //generate a new name
    $randomName = RandomName($filename);
    $fullName = $path.$randomName.".".$ext;
    while(file_exists($fullName)){
        $randomName = RandomName($filename);
        $fullName = $path.$randomName.".".$ext;
    }
    if(move_uploaded_file($_FILES['file']['tmp_name'],$fullName)){
        return $fullName;
    }else{
         echo "Failed to upload";
    }
}