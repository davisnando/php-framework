<?php
class User{
    public function __construct(){

    }
    /**
    
    Login Function

    **/
    public static function Login($user,$pass){
        $pass = hash("sha512",$pass.$user);
        $db = new model(DB_Database);
        $db->prepare("SELECT * FROM Users WHERE username=:user AND password=:pass");
        $db->bind(":user",$user);
        $db->bind(":pass", $pass);
        $result = $db->GetAll();
        if(count($result) == 1){
            $_SESSION['username'] = $user;
            return True;
        }else{
            return False;   
        }
    }
    /** 
    
    Parameter is an array. The keyname is the table name and value is value for column
    
    **/
    public static function createUser($array){
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
        $keynames = array_keys($array);
        $i = 0;
        $userkomma = false;
        $personalkomma = false;
        $userquery1 = "";
        $binduser = "";
        $personalquery1 = "";
        $bindpersonal = "";
        $personal = [];
        $uservalue = [];
        $array['username'] = strtolower( $array['username']);
        $array['password'] = hash('sha512',$array['password'].$array['username']);
        foreach($array as $item){
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
        $db->bind(":user",$array['username']);
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
    /** 

    Checks if user has permissions

    **/
    public static function RoleExist($username,$perm){
        $db = new model(DB_Database);
        $db->prepare("SELECT Role.idRole,Role.name FROM `Users` JOIN userRole ON Users.idUsers=userRole.idUser JOIN Role ON userRole.idRole=Role.idRole WHERE Users.username=:user");
        $db->bind(":user",$username);
        $result = $db->GetAll();
        foreach($result as $item){
            if($item['name'] == $perm){
                return True;
            }
            $db->prepare("SELECT Perm.description FROM `permRole` JOIN Perm ON Perm.idPerm=permRole.idPerm WHERE `idRole`=:id");
            $db->bind(":id",$item['idRole']);
            $result1 = $db->GetAll();
            foreach($result1 as $item1){
                if(in_array($perm,$item1) )
                {
                    return True;
                }
            }


        }
        return False;
    }

}


?>