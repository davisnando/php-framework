<?php
class Visitors extends ModelObj{
    function __construct(){
        $this->uniek = new ModelBool('Uniek',NULL, False,False);
        $this->page = new ModelVarchar('Page',255,NULL,False);
        $this->IP = new ModelVarchar('IP',255,NULL,False);
        $this->VisitDate = new ModelDateTime('VisitDate',True,True);
        $this->onlinesince = new ModelDateTime('onlinesince',False,True);
    }
}
class Visitors_online extends ModelObj{
    function __construct(){
        $this->IP = new ModelVarchar('IP',255,NULL,False);
        $this->Last_seen = new ModelDateTime('Last_seen',True,True);
    }
}
class Personal extends ModelObj{
    function __construct(){
        $this->firstname = new ModelVarchar('firstname',255,NULL,False);
        $this->lastname = new ModelVarchar('lastname',255,NULL,False);
    }
    static function insert(){
        Personal::Create(['firstname'=>'test', 'lastname'=>'test']);
    }

}
class Users extends ModelObj{
    function __construct(){
        $this->username = new ModelVarchar('username',255,NULL,False);
        $this->password = new ModelVarchar('password',255,NULL,False);
        $this->email = new ModelVarchar('email',255,NULL,False);
        $this->personal = new ModelFK('Personal','fk_personal',Personal::Class,False);
    }
    static function insert(){        
        #password = test
        #it uses password.username as salt
        Users::Create(['username'=>'test', 'password'=>hash('sha512','testtest'), 'email'=>'test@test.nl', 'Personal'=>'1']);
    }
}
class Perm extends ModelObj{
    function __construct(){
        $this->description = new ModelVarchar('description',45,NULL,False);
    }  
    static function insert(){
        Perm::Create(['description'=>'adminpanel']);
        Perm::Create(['description'=>'checkuser']);
        Perm::Create(['description'=>'updateuser']);
        Perm::Create(['description'=>'createuser']);
        Perm::Create(['description'=>'changerole']);
        Perm::Create(['description'=>'changeUserRole']);
        Perm::Create(['description'=>'createRole']);
        Perm::Create(['description'=>'Role']);
        Perm::Create(['description'=>'Tables']);
        Perm::Create(['description'=>'ChangeTable']);
        Perm::Create(['description'=>'Log']);
        Perm::Create(['description'=>'statistics']);
    }
}
class Role extends ModelObj{
    function __construct(){
        $this->name = new ModelVarchar('Name',45,NULL,False);
    }  
    static function insert(){
        Role::Create(['Name'=>'admin']);
        Role::Create(['Name'=>'default']);
        Role::Create(['Name'=>'moderator']);
    }
}
class permRole extends ModelObj{
    function __construct(){
        $this->perm = new ModelFK('perm','fk_perm',Perm::Class,False);
        $this->role = new ModelFK('role','fk_role',Role::Class,False);
    }  
    static function insert(){
        permRole::Create(['role'=>'1', 'perm'=>'1']);
        permRole::Create(['role'=>'1', 'perm'=>'2']);
        permRole::Create(['role'=>'1', 'perm'=>'3']);
        permRole::Create(['role'=>'1', 'perm'=>'4']);
        permRole::Create(['role'=>'1', 'perm'=>'5']);
        permRole::Create(['role'=>'1', 'perm'=>'6']);
        permRole::Create(['role'=>'1', 'perm'=>'7']);
        permRole::Create(['role'=>'1', 'perm'=>'8']);
        permRole::Create(['role'=>'1', 'perm'=>'9']);
        permRole::Create(['role'=>'1', 'perm'=>'10']);
        permRole::Create(['role'=>'1', 'perm'=>'11']);        
        permRole::Create(['role'=>'1', 'perm'=>'12']);        
        permRole::Create(['role'=>'3', 'perm'=>'1']);
        permRole::Create(['role'=>'3', 'perm'=>'2']);
        permRole::Create(['role'=>'3', 'perm'=>'3']);
        permRole::Create(['role'=>'3', 'perm'=>'4']);
    }
}
class userRole extends ModelObj{
    function __construct(){
        $this->user = new ModelFK('user','fk_user',Users::Class,False);
        $this->role = new ModelFK('role','fk_roles',Role::Class,False);
    }  
    static function insert(){
        userRole::Create(['user'=>'1', 'role'=>'1']);
    }
}
class Log extends ModelObj{
    function __construct(){
        $this->user = new ModelFK('user','fk_user',Users::Class,False);
        $this->logtext = new ModelVarchar('Logtext',255,NULL,False);
        $this->Logdate = new ModelDateTime('Logdate',True,True);
    }
}

