<?php
require('admin/model/model.php');
function index(){
    echo "Hello, ";
    // $personal = Personal::Create(['firstname'=>'test', 'lastname'=>'test123']);
    // $user = Users::Create(['username'=>'test','password'=>sha1('test'),'email'=>"email@gmail.com"  , 'Personal'=>$personal->id]);
    $u = userRole::get(['id'=>$_GET['id']]);
    if($u != null)
        // SELECT Personal.firstname, Personal.lastname FROM userRole JOIN Users on userRole.user=Users.id JOIN Personal on Users.Personal=Personal.id WHERE userRole=:id
        echo $u->user->object->personal->object->firstname->value." ".$u->user->object->personal->object->lastname->value;
    else
        echo "user not exists";
}   
?>