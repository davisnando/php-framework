<?php
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
    AddLog("Changed user with id: $id");

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
    if(!User::RoleExist($_SESSION['username'],"Role")){
        header("location: /admin/dashboard");
        die();
    }
    LoadTemplates();
    GetTemplate('main','header.php');
    GetTemplate("main","menu.php");
    GetTemplate('dashboard','role.php');
    GetTemplate('main','footer.php'); 
}
function getPerm(){
    $id = $_POST['id'];
    $db = new Model();
    $db->prepare("SELECT idPerm FROM permRole WHERE idRole=:id");
    $db->bind(":id",$id);
    $result = $db->GetAll();
    echo json_encode($result);
}
function setPerm(){
    if(!User::RoleExist($_SESSION['username'],"changerole")){
        die();
    }
    $id = $_POST['idRole'];
    $idPerm = $_POST['idPerm'];
    $toggle = $_POST['toggle'];
    $db = new Model();

    if($toggle == "1"){
        $db->prepare("INSERT INTO permRole(idRole,idPerm) VALUES(:role,:perm)");
        $db->bind(":role", $id);
        $db->bind(":perm",$idPerm);
        $db->execute();
        echo "Added";
        AddLog("Added permission: $idPerm to Role: $id");


    }else{
        $db->prepare("DELETE FROM permRole WHERE idRole=:role and idPerm=:perm");
        $db->bind(":role", $id);
        $db->bind(":perm",$idPerm);
        $db->execute();
        AddLog("Deleted permission: $idPerm from Role: $id");
        echo "Deleted";
    }
}
function changeRole(){
    if(!User::RoleExist($_SESSION['username'],"changeUserRole")){
        die();
    }
    $idRole = $_POST['idRole'];
    $idUser = $_POST['idUser'];
    $db = new Model();
    $db->prepare("SELECT * FROM userRole WHERE idUser=:id and idRole=1");
    $db->bind(":id",$idUser);
    $result = $db->GetAll();
    if(count($result) > 0){
        die();
    }
    $db->prepare("DELETE FROM userRole WHERE idUser=:id");
    $db->bind(":id",$idUser);
    $db->execute();
    $db->prepare("INSERT INTO userRole(idRole, idUser) VALUES(:role,:id)");
    $db->bind(":id",$idUser);
    $db->bind(":role",$idRole);
    $db->execute();  
    AddLog("Changed role from user: $idUser to $idRole ");

    echo "Done";  
}
function createRole(){
    if(!User::RoleExist($_SESSION['username'],"createRole")){
        die();
    }
    $name = $_POST['name'];
    $db = new Model();
    $db->prepare("SELECT * FROM Role WHERE name=:name");
    $db->bind(":name",$name);
    $result = $db->GetAll();
    if(count($result) > 0){
        echo "Exist";
        die();
    }
    $db->prepare("INSERT INTO Role(name) VALUES(:name)");
    $db->bind(":name",$name);
    $db->execute();
    AddLog("Created a new Role with name: ".$name);

    echo "Done";

}
function createPerm(){
    if(!User::RoleExist($_SESSION['username'],"createRole")){
        die();
    }
    $name = $_POST['name'];
    $db = new Model();
    $db->prepare("SELECT * FROM Perm WHERE description=:name");
    $db->bind(":name",$name);
    $result = $db->GetAll();
    if(count($result) > 0){
        echo "Exist";
        die();
    }
    $db->prepare("INSERT INTO Perm(description) VALUES(:name)");
    $db->bind(":name",$name);
    $db->execute();
    AddLog("Created a new permission with name: ".$name);
    echo "Done";

}
function Table(){
    if(!User::RoleExist($_SESSION['username'],"Tables")){
        header("location: /admin/dashboard");
        die();
    }
    $items = explode('/',$_GET['path']);
    LoadTemplates();
    GetTemplate('main','header.php');
    GetTemplate("main","menu.php");
    if($items[count($items) -1 ] != "table"){
        $_SESSION['table'] = $items[count($items) -1 ];
        GetTemplate('dashboard','inTable.php');
    }else{
        GetTemplate('dashboard','table.php');
    }
    GetTemplate("main","footer.php");

}
function changeTable(){
    if(!isset($_SESSION['table'])){
        header("location: /admin/dashboard");
        die();
    }
    $items = explode('/',$_GET['path']);
    LoadTemplates();
    GetTemplate('main','header.php');
    GetTemplate("main","menu.php");
    $id = end($items);
    $_SESSION['ChangeID'] = $id;
    if(!is_numeric($id)){
        header("location: /admin/dashboard");
        die();
    }
            GetTemplate('dashboard','item.php');

    GetTemplate("main","footer.php");

}
function saveItem(){
    if(!isset($_SESSION['ChangeID']) || !User::RoleExist($_SESSION['username'],"ChangeTable") || !isset($_SESSION['table'])){
        die();
    }
   $id = $_SESSION['ChangeID'];
   $table = $_SESSION['table'];
   $db = new Model();
   $db->prepare("SHOW COLUMNS FROM $table");
   $result = $db->GetAll();
   $fieldnames = [];
   $primarykey = "";
   $first = True;
   foreach($result as $item){
        if($item['Key'] == "PRI"){
            $primarykey = $item['Field'];
        }
        array_push($fieldnames,$item['Field']);
   }
   $keynames = array_keys($_POST);
   $allItems = $_POST;
   $i = 0;
   $query = "";
   foreach($allItems as $item){
       if(!in_array($keynames[$i],$fieldnames)){
           die();
       }
        if($i == 0){
            $query = $keynames[$i]."='".$item."'";
        }
        else{
            $query = $query.", ".$keynames[$i]."='".$item."'";

        }
        $i++;
   }
   $query = "UPDATE $table SET ".$query." WHERE $primarykey=:id";
   $db->prepare($query);
   $db->bind(":id",$id);
   if(   $db->execute()){
       echo "Done";
   }else{
       echo "Failed";
   }
   AddLog("Changed column from table: $table with columnId: $id");

}
function ShowLogPage(){
    if(!User::RoleExist($_SESSION['username'],"Log")){
        die();
    }
    LoadTemplates();
    GetTemplate('main','header.php');
    GetTemplate('main','menu.php');
    GetTemplate('dashboard','log.php');
    GetTemplate('main','footer.php');
}
function statistics(){
    require("classes/statistics.php");
    if(!User::RoleExist($_SESSION['username'],"statistics")){
        die();
    }
    LoadTemplates();
    GetTemplate('main','header.php');
    GetTemplate('main','menu.php');
    GetTemplate('dashboard','statistics.php');
    GetTemplate('main','footer.php');
}

?>