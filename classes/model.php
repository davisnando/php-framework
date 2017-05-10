<?php
class model{
    function __construct(){
        $this->dbh = new PDO('mysql:host='.DB_IP.';dbname='.DB_Database, DB_USER, DB_PASS);
    }
    public function prepare($query){
        $this->stmt = $this->dbh->prepare($query);
    }
    public function bind($param, $value, $type = null)
    {
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
       return $this->stmt->fetch();
    }
    public function GetAll(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function debug(){
        $this->stmt->debugDumpParams();
    }
    public function getTables(){
        $this->prepare("SHOW TABLES");
        $tables = $this->GetAll();
        $key = array_keys($tables);
        $key = $key[0];
        $tables = $tables[$key]; 
        return $tables;
    }
    public function getColumns($table){
        $this->prepare("SHOW COLUMNS FROM $table");
        return $this->GetAll(); 
    }
    public function insert($table, $items){
        $tables = $this->getTables();
        if(!in_array($table,$tables)){
            if(strtolower( DEBUG) == "true"){
                echo $table;
                echo "Table not exist";
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
                    echo $key;
                    echo " Column not exist";
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
    public function update($table,$pk,$newValue){
        $tables = $this->getTables();
        if(!in_array($table,$tables)){
            if(strtolower( DEBUG) == "true"){
                echo $table;
                echo "Table not exist";
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
                    echo $key;
                    echo "Key not exist";
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
}

?>