<?php
class ModelObj{
    function CreateTable(){
        $tablename = get_called_class();
        $query = "CREATE TABLE ".$tablename."(id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY);";
        $vars = get_object_vars($this);
        $varnames = array_keys($vars);
        foreach($varnames as $varname){
            $query .= $this->$varname->GetQuery($tablename);
        }
        return $query;
    } 
       
    static function Name(){
        return get_called_class();
    }

    static function Create($options){
        $classname = get_called_class();
        $c = new $classname();
        $vars = get_object_vars($c);
        $names = array_keys($options);
        foreach($vars as $var){
            if(!$var->can_be_null){
                if(!in_array($var->name, $names) ){
                    trigger_error("$var->name can't be null", E_USER_ERROR);  
                }
            }
        }
        $db = new Model();
        $result = $db->insert($classname, $options);
        if($result['succeed'] != True){
            trigger_error("Something went wrong by create row in table: $table", E_USER_ERROR);  
        }
        return $classname::Get(['id'=>$result['id']]);
    }

    static function getVarName($dbname, $class){
        $vars = get_object_vars($class);
        $vars = array_keys($vars);
        for($i = 0; $i < count($vars); $i++){
            $var = $vars[$i];
            if(isset($class->$var->name)){
                if($class->$var->name == $dbname)
                    return $vars[$i];
            }
        }      
        return "";  
    }
    static function get_or_create($options){
        $classname = get_called_class(); 
        $c = $classname::Get($options);  
        if($c != null)
            return $c;
        else
            return $classname::Create($options);      
    }
    static function Get($options){
        $db = new Model();
        $classname = get_called_class(); 
        $result = $db->Get($classname, $options);
        if(empty($result)){
            return null;
        }
        $c= new $classname();  
        $c->id = $result['id'];
        unset($result['id']);
        $keys = array_keys($result);
        for($i = 0; $i < count($keys); $i++){
            $key = $keys[$i];
            $var = $c->getVarName($key, $c);
            $object = $c->$var;
            $object->value = $result[$key];   
            if(isset($object->object)){
                $object->getObject();
            }
        }
        return $c;
    }

    static function filter($options){
        $db = new Model();
        $classname = get_called_class();         
        $result = $db->filter($classname, $options);
        if(empty($result)){
            return null;
        }
        $rows = [];
        foreach($result as $row){
            $c= new $classname();  
            $c->id = $row['id'];
            unset($row['id']);
            $keys = array_keys($row);
            for($i = 0; $i < count($keys); $i++){
                $key = $keys[$i];
                $var = $c->getVarName($key, $c);
                $object = $c->$var;
                $object->value = $row[$key];   
                if(isset($object->object)){
                    $object->getObject();
                }
            }
            array_push($rows, $c);
        }
        return $rows;
    }

    function Set($options){
        $db = new Model();
        $id = $this->id;
        $classname = get_called_class();
        $succeed = $db->update($classname, $id, $options);
        if(!$succeed){
            trigger_error("Something went wrong when updating: $classname", E_USER_ERROR);            
        }
        $this->__updateValues();
        return $this;
    }

    function __updateValues(){
        $id = $this->id;
        $db = new Model();
        $classname = get_called_class();
        $result = $db->Get($classname,['id'=>$id]);
        unset($result['id']);
        $keys = array_keys($result);
        for($i = 0; $i < count($keys); $i++){
            $key = $keys[$i];
            $var = $this->getVarName($key, $this);
            $object = $this->$var;
            $object->value = $result[$key]; 
            if(isset($object->object)){
                $object->getObject();
            }  
        }
    }

    function log(){
        $vars = get_object_vars($this);
        foreach($vars as $var){
            print($var->value);
        }        
    }

    static function insert(){
    }
}

class ModelObjBase{
    function GetQuery($table){
        return "";
    }

    function getValue($id, $table){
        $db = new Model();
        $db->prepare("SELECT $this->name FROM $table WHERE id=$id");
        $db->execute();
        $result = $db->fetch();
        $this->value = $result[$this->name];
    }
}

class ModelVarchar extends ModelObjBase{
    function __construct($name, $length=255,$default=null, $can_be_null=False){
        $this->name = $name;
        $this->length = $length;
        $this->defaultValue = $default;
        $this->can_be_null = $can_be_null;
        $this->type = "varchar(".$length.")";
        $this->value = NULL;
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD `";
        $query .= $this->name."` VARCHAR(".$this->length.")";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= " DEFAULT '".$this->defaultValue."'";
        }
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` VARCHAR(".$this->length.")";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= " DEFAULT '".$this->defaultValue."'";
        }
        return $query."; ";
    }
}

class ModelInt extends ModelObjBase{
    function __construct($name, $length=11,$default=null, $can_be_null=False, $autoincrement=False){
        $this->name = $name;
        $this->length = $length;
        $this->defaultValue = $default;
        $this->can_be_null = $can_be_null;
        $this->autoincrement = $autoincrement;
        $this->type = "int(".$length.")";
        $this->value = NULL;
        
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD `";
        $query .= $this->name."` INT(".$this->length.")";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        if($this->autoincrement){
            $query .= "AUTO_INCREMENT";
        }
        echo $query;
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` INT(".$this->length.")";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        if($this->autoincrement){
            $query .= "AUTO_INCREMENT";
        }
        return $query."; ";
    }
}

class ModelFloat extends ModelObjBase{
    function __construct($name, $default=null, $can_be_null=False){
        $this->name = $name;
        $this->defaultValue = $default;
        $this->can_be_null = $can_be_null;
        $this->type = "float";
        $this->value = NULL;
        
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD `";
        $query .= $this->name."` float";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` float";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        return $query."; ";
    }
}

class ModelBool extends ModelObjBase{
    function __construct($name, $default=null, $can_be_null=False){
        $this->name = $name;
        $this->length = 1;
        $this->defaultValue = $default;
        $this->can_be_null = $can_be_null;
        $this->type = "tinyint(".$this->length.")";
        $this->value = NULL;
        
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD `";
        $query .= $this->name."` tinyint(".$this->length.")";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` tinyint(".$this->length.")";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        return $query."; ";
    }
}

class ModelText extends ModelObjBase{
    function __construct($name, $default=null, $can_be_null=False){
        $this->name = $name;
        $this->defaultValue = $default;
        $this->can_be_null = $can_be_null;
        $this->type = "text";
        $this->value = NULL;
        
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD `";
        $query .= $this->name."` TEXT";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` TEXT";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        return $query."; ";
    }
}

class ModelFK extends ModelObjBase{

    function __construct($name,$indexName, $to, $can_be_null=False){
        $this->name = $name;
        $this->length = 6;
        $this->object = $to;
        $this->indexName = $indexName;
        $this->can_be_null = $can_be_null;
        $this->defaultValue = null;
        $this->type = "int(".$this->length.") unsigned";
        $this->value = NULL;
        
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD `";
        $query .= $this->name."` INT(".$this->length.") UNSIGNED";
        if($this->can_be_null){
            $query .= " NULL";
        }else{
            $query .= " NOT NULL";
        }
        $query .= "; ALTER TABLE `$table` ADD CONSTRAINT $this->indexName FOREIGN KEY (`$this->name`) REFERENCES `".$this->object::name()."` (id)";
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` INT(".$this->length.") UNSIGNED";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        $query .= "; ALTER TABLE `$table` ADD CONSTRAINT $this->indexName FOREIGN KEY (`$this->name`) REFERENCES `".$this->object::name()."` (id)";        
        return $query."; ";
    }
    function getObject(){
        $classname = $this->object;
        $c = $classname::Get(['id'=>$this->value]);
        $this->object = $c;
    }
}

class ModelDateTime extends ModelObjBase{
    function __construct($name, $defaultNow=False, $can_be_null=False){
        $this->name = $name;
        if($defaultNow){
            $this->defaultValue = "NOW()";            
        }else{
            $this->defaultValue = null;            
        }
        $this->can_be_null = $can_be_null;
        $this->type = "datetime";
        $this->value = NULL;
        
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD `";
        $query .= $this->name."` datetime";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= " DEFAULT ".$this->defaultValue."";
        }
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` datetime";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= " DEFAULT ".$this->defaultValue."";
        }
        return $query."; ";
    }
}

class ModelTime extends ModelObjBase{
    function __construct($name,  $can_be_null=False){
        $this->name = $name;
        $this->defaultValue = null;            
        $this->can_be_null = $can_be_null;
        $this->type = "time";
        $this->value = NULL;
        
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD ";
        $query .= $this->name." time";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= " DEFAULT ".$this->defaultValue."";
        }
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` time";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= " DEFAULT ".$this->defaultValue."";
        }
        return $query."; ";
    }
}

class ModelDate extends ModelObjBase{
    function __construct($name, $can_be_null=False){
        $this->name = $name;
        $this->defaultValue = null;            
        $this->can_be_null = $can_be_null;
        $this->type = "date";
        $this->value = NULL;
        
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD `";
        $query .= $this->name."` date";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` date";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        return $query."; ";
    }
}

class ModelDouble extends ModelObjBase{
    function __construct($name, $default=null, $can_be_null=False){
        $this->name = $name;
        $this->defaultValue = $default;
        $this->can_be_null = $can_be_null;
        $this->type = "double";
        $this->value = NULL;
        
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD `";
        $query .= $this->name."` double";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` double";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        return $query."; ";
    }
}

class ModelDecimal extends ModelObjBase{
    function __construct($name, $length=11,$default=null, $can_be_null=False){
        $this->name = $name;
        $this->length = $length;
        $this->length = str_replace('.',',',$length);
        if(preg_match('/./',$this->length)){
            $this->length .= ",0";
        } 
        $this->defaultValue = $default;
        $this->can_be_null = $can_be_null;
        $this->type = "decimal(".$this->length.")";
        $this->value = NULL;        
    }
    function GetQuery($table){
        $query = "ALTER TABLE `".$table."` ADD `";
        $query .= $this->name."` decimal(".$this->length.")";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        return $query."; ";
    }
    function ChangeQuery($table){
        $query = "ALTER TABLE `".$table."` CHANGE COLUMN `".$this->name."` `";
        $query .= $this->name."` decimal(".$this->length.")";
        if($this->can_be_null){
            $query .= " NULL ";
        }else{
            $query .= " NOT NULL ";
        }
        if(isset($this->defaultValue)){
            $query .= "DEFAULT ".$this->defaultValue." ";
        }
        return $query."; ";
    }
}







