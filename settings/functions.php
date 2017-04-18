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
/** 

Checks if user has permissions

**/
function RoleExist($username,$perm){
    $db = new model("Framework");
    $db->prepare("SELECT Role.idRole,Role.name FROM `Users` JOIN userRole ON Users.idUsers=userRole.idUser JOIN Role ON userRole.idRole=Role.idRole WHERE Users.username=:user");
    $db->bind(":user",$username);
    $result = $db->GetAll();
    foreach($result as $item){
        if($item['name'] == $perm){
            return True;
        }
        $db->prepare("SELECT Perm.description FROM `permRole` JOIN Perm ON Perm.idPerm=permRole.idPerm WHERE `idRole`=:id");
        $db->bind(":id",$item['idRole']);
        $result1 = $db->GetAll();
        foreach($result1 as $item1){
            if(in_array($perm,$item1) )
            {
                return True;
            }
        }


    }
    return False;
}