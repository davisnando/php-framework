<?php
function index(){
     if(isset($_SESSION['username'])){
        header("location: /admin/dashboard");
        die();
    }
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
   require("admin/model.php");
   $username = $_SESSION['username'];
    if(!RoleExist($username,"createuser")){
        die();
    } 
    $db = new model();
    $db->prepare("SHOW COLUMNS FROM Users");
    $usertable = $db->GetAll();
    $usertablenames = [];
    foreach($usertable as $table){
        array_push($usertablenames,$table['Field']);
    }
    $db->prepare("SHOW COLUMNS FROM Personal");
    $personaltable = $db->GetAll();
    $personaltablenames = [];
    foreach($personaltable as $table){
        array_push($personaltablenames,$table['Field']);
    }
    $keynames = array_keys($_POST);
    $i = 0;
    $userkomma = false;
    $personalkomma = false;
    $userquery1 = "";
    $binduser = "";
    $personalquery1 = "";
    $bindpersonal = "";
    $personal = [];
    $uservalue = [];
    $_POST['password'] = hash('sha512',$_POST['password'].$_POST['username']);
    foreach($_POST as $item){
        if(in_array($keynames[$i],$personaltablenames)){
            if($personalkomma){
             $personalquery1 = $personalquery1.",";   
             $bindpersonal = $bindpersonal.",";
            }
            $personalkomma = True;
            $personalquery1 = $personalquery1.$keynames[$i];
            $bindpersonal = $bindpersonal.":".$keynames[$i];
            $tempar = [":".$keynames[$i],$item];
            array_push($personal,$tempar);
        }else if(in_array($keynames[$i],$usertablenames)){
            if($userkomma){
             $userquery1 = $userquery1.",";
             $binduser = $binduser.",";   
            }
            $userkomma = True;
            $userquery1 = $userquery1.$keynames[$i];
            $binduser = $binduser.":".$keynames[$i];
            $tempar = [":".$keynames[$i],$item];
            array_push($uservalue,$tempar);
        }else{
            echo "Not exist";
        }        
        $i++;
    }
    $db->prepare("SELECT * FROM Users WHERE username=:user");
    $db->bind(":user",$_POST['username']);
    $result = $db->GetAll();
    if(count($result) >=1){
        echo "username";
        die();
    }
    $query1 = "INSERT INTO Personal($personalquery1) VALUES($bindpersonal)";
    $db->prepare($query1);
    foreach($personal as $bindvalue){
        $db->bind($bindvalue[0],$bindvalue[1]);
    }
    $db->execute();
    $id = $db->dbh->lastInsertId();
    $query1 = "INSERT INTO Users($userquery1,idPersonal) VALUES($binduser,:id)";
    $db->prepare($query1);
    foreach($uservalue as $bindvalue){
        $db->bind($bindvalue[0],$bindvalue[1]);
    }
    $db->bind(":id",$id);
    $db->execute();
    $id = $db->dbh->lastInsertId();
    $db->prepare("INSERT INTO userRole(idUser,idRole) VALUES(:user,2)");
    $db->bind(":user",$id);
    $db->execute();
    echo "Done";
}
?>