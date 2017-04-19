<?php
require("admin/model.php");
require("classes/user.php");
function index(){
     if(isset($_SESSION['username']) && User::RoleExist($_SESSION['username'],"adminpanel")){
        header("location: /admin/dashboard");
        die();
    }
    LoadTemplates();
    GetTemplate('main','header.php');
    GetTemplate('sign_in','index.php');
    GetTemplate('main','footer.php');
}
function login(){
    $user = strtolower( $_POST['user']);
    $pass = $_POST['pass'];
    if(User::Login($user,$pass)){
        echo json_encode("True");
    }else{
        echo json_encode("False");

    }
}
function logout(){
    session_destroy();
    $_SESSION['username'] = "";
    session_unset();
    header("location: ../admin");
}
function dashboard(){
    dashboardPerm();
    $items = explode('/',$_GET['path']);
    LoadTemplates();
    GetTemplate('main','header.php');
    GetTemplate("main","menu.php");
    if($items[count($items) -1 ] != "dashboard"){
        GetTemplate('dashboard','user.php');
    }else{
        GetTemplate('dashboard','index.php');
    }
    GetTemplate('main','footer.php'); 

}
function dashboardPerm(){
    if(!isset($_SESSION['username'])){
        header("location: /admin");
    }
    $username = $_SESSION['username'];
    // $bool = User::RoleExist($username,"adminpanel");
    // print_r($bool);
    if(!User::RoleExist($username,"adminpanel")){
        header("location: /admin");
        die();
    }
}
function Checkuser(){
    if(!isset($_SESSION['username'])){
        header("location: /admin");
    }
    // $bool = User::RoleExist($username,"adminpanel");
    // print_r($bool);
    if(!User::RoleExist($username,"checkuser")){
        header("location: /admin");
        die();
    }
}
function ChangeUser(){
    $username = $_SESSION['username'];
    if(!User::RoleExist($username,"updateuser")){
        die();
    }
    $db = new model();
    $id = $_SESSION['ChangeID'];
    $i = 0;
    $db->prepare("SELECT * FROM `Users` JOIN Personal ON Users.idPersonal = Personal.idPersonal WHERE `idUsers`=:id");
    $db->bind(":id",$id);
    $items = $db->GetAll();
    $pid = $items[0]['idPersonal'];
    $whitelist = array_keys($items[0]);
    $keynames = array_keys($_POST);
    foreach($_POST as $table){
        $key = $keynames[$i];
        if(!in_array($key,$whitelist)){
            die();
        }
        if($keynames[$i] == "idUsers"){

        }else if($keynames[$i] == "username" || $keynames[$i] == "email"){
            $db->prepare("UPDATE Users SET $key=:value WHERE idUsers=:id");
            $db->bind(":id",$id);
            $db->bind(":value",$table);

        }else{
            $db->prepare("UPDATE Personal SET $key=:value WHERE idPersonal=:pid");
            $db->bind(":pid",$pid);
            $db->bind(":value",$table);
        }
        if(!$db->execute()){
            echo "Failed";
        }
        $i++;
    }
    echo "Done";
}
function CreateUser(){
    $username = $_SESSION['username'];
    if(!User::RoleExist($username,"createuser")){
        die();
    } 
    User::createUser($_POST);
}
function Role(){
    LoadTemplates();
    GetTemplate('main','header.php');
    GetTemplate("main","menu.php");
    GetTemplate('dashboard','role.php');
    GetTemplate('main','footer.php'); 
}
?>