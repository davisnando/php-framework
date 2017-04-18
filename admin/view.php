<?php
function index(){
    LoadTemplates();
    GetTemplate('main','header.php');
    GetTemplate('sign_in','index.php');
    GetTemplate('main','footer.php');
}
function login(){
    require("admin/model.php");
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $pass = hash("sha512",$pass.$user);
    $db = new model(DB_Database);
    $db->prepare("SELECT * FROM Users WHERE username=:user AND password=:pass");
    $db->bind(":user",$user);
    $db->bind(":pass", $pass);
    $result = $db->GetAll();
    if(count($result) == 1){
        $_SESSION['username'] = $user;
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
    require("admin/model.php");
    if(!isset($_SESSION['username'])){
        header("location: /admin");
    }
    $username = $_SESSION['username'];
    // $bool = RoleExist($username,"adminpanel");
    // print_r($bool);
    if(!RoleExist($username,"adminpanel")){
        header("location: /admin");
        die();
    }
}
function Checkuser(){
    require("admin/model.php");
    if(!isset($_SESSION['username'])){
        header("location: /admin");
    }
    // $bool = RoleExist($username,"adminpanel");
    // print_r($bool);
    if(!RoleExist($username,"checkuser")){
        header("location: /admin");
        die();
    }
}
function ChangeUser(){
    require("admin/model.php");
    $username = $_SESSION['username'];
    if(!RoleExist($username,"updateuser")){
        die();
    }
    $db = new model();
    $id = $_POST['idUsers'];
    $i = 0;
    $db->prepare("SELECT * FROM `Users` WHERE `idUsers`=:id");
    $db->bind(":id",$id);
    $items = $db->GetAll();
    $pid = $items[0]['idPersonal'];
    $keynames = array_keys($_POST);
    foreach($_POST as $table){
        $key = $keynames[$i];
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
?>