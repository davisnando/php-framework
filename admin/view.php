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
    $username = $_SESSION['username'];
    // $bool = RoleExist($username,"adminpanel");
    // print_r($bool);
    if(!RoleExist($username,"checkuser")){
        header("location: /admin");
        die();
    }
}

?>