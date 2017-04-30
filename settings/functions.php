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
        AddLog("Uploaded file with path: ".$fullName);
        return $fullName;
    }else{
         echo "Failed to upload";
    }
}
function AddLog($logtext){
    $db = new Model();
    $db->prepare("INSERT INTO Log(idUser,Logtext) VALUES(:id,:text)");
    $db->bind(":id",User::getId($_SESSION['username']));
    $db->bind(":text",$logtext);
    $db->execute();
}
function stillAlive(){
    $db = new Model();
    $db->prepare("DELETE FROM Vistors_online WHERE Last_seen < (NOW() - INTERVAL 1 MINUTE)");
    $db->execute();
    $ip = get_client_ip();
    $db->prepare("SELECT * FROM Vistors_online WHERE IP=:ip");
    $db->bind(":ip", $ip);
    $result = $db->GetAll();
    $query = "";
    if(count($result) == 0){
        $query = "INSERT INTO Vistors_online(IP) VALUES(:ip)";
    }else{
        $query = "UPDATE `Vistors_online` SET `Last_seen`=CURRENT_TIMESTAMP WHERE `IP`=:ip";
    }
    $db->prepare($query);
    $db->bind(":ip",$ip);
    $db->execute();
}
function addAliveControl(){
    LoadStatic();
    echo "<script src=";
    GetStaticFile("js","stayalive.js");
    echo "></script>";
}
function visit(){
    $path = $_GET['path'];
    $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $db = new Model();
    $ip =  get_client_ip();
    $db->prepare("SELECT * FROM `Visitors` WHERE `VisitDate` > (NOW() - INTERVAL 5 MINUTE) AND IP=:ip AND Page=:p");
    $db->bind(":ip",$ip);
    $db->bind(":p",$actual_link);
    $result = $db->GetAll();
    $first = explode('/',$path)[0];
    $ext = explode('.',$path);
    if($first == "admin" ){
        return;
    }
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return;
    }
    if( count($ext) == 2){
        return;
    }
    addAliveControl();
    if(count($result) >= 1){
        return;
    }
    stillAlive();
    $unique = True;
    if(isset($_COOKIE['Unique'])){
        $unique = False;
    }else{
        setcookie("Unique","False",time() + (10 * 365 * 24 * 60 * 60));
    }
    $db->prepare("INSERT INTO Visitors(Uniek,Page,IP) VALUES(:u,:p,:i)");
    $db->bind(":u",$unique);
    $db->bind(":p",$actual_link);
    $db->bind(":i",$ip);
    $db->execute();

    
}
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}