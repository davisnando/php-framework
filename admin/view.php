<?php
function index(){
    LoadTemplates();
    GetTemplate('main','header.php');
    GetTemplate('sign_in','index.php');
    GetTemplate('main','footer.php');
}
function login(){
    echo "Hallo";
    echo "<br>";
}

?>