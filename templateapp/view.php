<?php
require('admin/model/model.php');
require('classes/views.php');

Class indexView extends View{
    public function get(){
        echo "Hello, ";
        // $personal = Personal::Create(['firstname'=>'test', 'lastname'=>'test123']);
        // $user = Users::Create(['username'=>'test','password'=>sha1('test'),'email'=>"email@gmail.com"  , 'Personal'=>$personal->id]);
        $u = null;
        if(array_key_exists('id', $_GET)){
            $u = userRole::get(['id'=>$_GET['id']]);
        }
        if($u != null)
            // SELECT Personal.firstname, Personal.lastname FROM userRole JOIN Users on userRole.user=Users.id JOIN Personal on Users.Personal=Personal.id WHERE userRole=:id
            echo $u->user->object->personal->object->firstname->value." ".$u->user->object->personal->object->lastname->value;
        else
            echo "user not exists";
    }
} 
?>