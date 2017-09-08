<?php
class model{
    function __construct(){
        $this->dbh = new PDO('mysql:host='.DB_IP.';dbname='.DB_Database, DB_USER, DB_PASS);
    }

    public function prepare($query){
        $this->stmt = $this->dbh->prepare($query);
    }

    public function bind($param, $value, $type = null){
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(){
        return $this->stmt->execute();
    }

    public function fetch(){
       return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetAll(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function debug(){
        $this->stmt->debugDumpParams();
    }
    /**
        Get all tables
    **/
    public function getTables(){
        $this->prepare("SHOW TABLES");
        $tables = $this->GetAll();
        if(count($tables) <= 0){
            return [];            
        }
        $key = array_keys($tables);
        $key = $key[0];
        print_r($key);
        print_r($tables);
        $tables = $tables[$key]; 
        return $tables;
    }

    public function getAllTables(){
        $this->prepare("SHOW TABLES");
        $tables = $this->GetAll();
        $newTables = [];
        foreach ($tables as $table){
            $key = array_keys($table);            
            array_push($newTables,$table[$key[0]]);
        }
        return $newTables;
    }
    /**
        Get All column info from given table
    **/
    public function getColumns($table){
        $this->prepare("SHOW COLUMNS FROM $table");
        return $this->GetAll(); 
    }
    /**
        Insert row in given table
        $table is table to insert in
        $items is array with as keyname the columnname like this $array = ['username'=>'Thisisaexample','password'=>'qwerty']
        return an array with as first item the id of the row and second item is if the query is succeed
    **/
    public function insert($table, $items){
        $tables = $this->getAllTables();
        if(!in_array($table,$tables)){
            if(strtolower( DEBUG) == "true"){
                trigger_error("$table not exist", E_USER_ERROR);                
            }
            return False;
        }
        $column = $this->getColumns($table);
        $columnnames = [];
        foreach($column as $columnname){
            array_push($columnnames,$columnname['Field']);
        }
        $arraykeys = array_keys($items);
        $values = "";
        $bind = "";
        for($i = 0; $i < count($items); $i++){
            $key = $arraykeys[$i];
            if(!in_array($key, $columnnames)){
                if(strtolower( DEBUG) == "true"){
                    trigger_error("$key not exist", E_USER_ERROR);
                    
                }
                return False;
            }
            $item = $items[$key];
            if($i == 0 ){
                $values = $key;
                $bind = ":".$key;
            }else{
                $values = $values.",".$key;
                $bind = $bind.", :".$key;
            }
        }
        $query = "INSERT INTO $table($values) VALUES($bind)";
        $this->prepare($query);
        for($i =0; $i < count($items); $i++){
            $key = $arraykeys[$i];
            $item = $items[$key];
            $this->bind(":".$key, $item);
        }
        $exe = $this->execute();
        $id = $this->dbh->lastInsertId();
        $ar = ["id"=>$id,"succeed"=>$exe];
        return $ar;
    }
    /**
        Updates given table 
        $table is table to update
        $pk is primary key value of the item you want to update
        $newValue is array with as keyname the columnname like this $array = ['username'=>'Thisisaexample','password'=>'qwerty']
    **/
    public function update($table,$pk,$newValue){
        $tables = $this->getAllTables();
        if(!in_array($table,$tables)){
            if(strtolower( DEBUG) == "true"){
                trigger_error("$table not exist", E_USER_ERROR);
            }
            return False;
        }
        $columns = $this->getColumns($table);
        $keynames = array_keys($newValue);
        $columnnames = [];
        $pkfieldname = "";
        foreach($columns as $column){
            if($column['Key'] == "PRI"){
                $pkfieldname = $column['Field'];
            }
            array_push($columnnames,$column['Field']);
        }
        $values = "";
        for($i = 0; $i < count($newValue); $i++){
            $key = $keynames[$i];
            if(!in_array($key,$columnnames)){
                if(strtolower( DEBUG) == "true"){
                    trigger_error("$key not exist", E_USER_ERROR);
                }
                return False;
            }
            if($i == 0 ){
                $values = "$key=:$key";
            }else{
                $values = $values.", $key=:$key";
            }
        }
        $query = "UPDATE $table SET $values WHERE $pkfieldname=:id";
        $this->prepare($query);
        $this->bind(":id",$pk);
        for($i = 0; $i < count($newValue); $i++){
            $key = $keynames[$i];
            $this->bind(":".$key, $newValue[$key]);
        }
        return $this->execute();
    }

    public function Filter($table,$filter){
        $tables = $this->getAllTables();
        if(!in_array($table,$tables)){
            if(strtolower( DEBUG) == "true"){
                trigger_error("$table not exist", E_USER_ERROR);                
            }
            return False;
        }
        $column = $this->getColumns($table);
        $keynames = array_keys($filter);
        $columnnames = [];
        foreach($column as $columnname){
            array_push($columnnames,$columnname['Field']);
        }
        $values = "";
        for($i =0; $i < count($filter); $i++){
            $key=$keynames[$i];
            if(!in_array($key,$columnnames)){
                if(strtolower( DEBUG) == "true"){
                    trigger_error("$key not exist", E_USER_ERROR);
                }
                return False;
            }
            if($i == 0 ){
                $values = "$key=:$key";
            }else{
                $values = $values.", $key=:$key";
            }
        }
        $query = "SELECT * FROM $table WHERE $values";
        $this->prepare($query);        
        for($i = 0; $i < count($filter); $i++){
            $key = $keynames[$i];
            $this->bind(":".$key, $filter[$key]);
        }
        return $this->GetAll();
    }
    
    public function Get($table,$filter){
        $tables = $this->getAllTables();
        if(!in_array($table,$tables)){
            if(strtolower( DEBUG) == "true"){
                trigger_error("$table not exist", E_USER_ERROR);                
            }
            return False;
        }
        $column = $this->getColumns($table);
        $keynames = array_keys($filter);
        $columnnames = [];
        foreach($column as $columnname){
            array_push($columnnames,$columnname['Field']);
        }
        $values = "";
        for($i =0; $i < count($filter); $i++){
            $key=$keynames[$i];
            if(!in_array($key,$columnnames)){
                if(strtolower( DEBUG) == "true"){
                    trigger_error("$key not exist", E_USER_ERROR);
                }
                return False;
            }
            if($i == 0 ){
                $values = "$key=:$key";
            }else{
                $values = $values.", $key=:$key";
            }
        }
        $query = "SELECT * FROM $table WHERE $values";
        $this->prepare($query);
        for($i = 0; $i < count($filter); $i++){
            $key = $keynames[$i];
            $this->bind(":".$key, $filter[$key]);
        }
        $this->execute();
        $f = $this->fetch();
        return $f;
    }
}

?>