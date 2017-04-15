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
}

?>