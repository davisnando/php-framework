<?php
require('classes/modelobj.php');
    
$model_files = [];
function LogVal($value){
    print_r($value);
    print(PHP_EOL);
}

function searchFile($path){
    if(is_dir($path)){
        global $model_files;
        $items = scandir($path);
        //remove .. and . items from directory list
        array_splice($items, 0, 1);
        array_splice($items, 0, 1);
        foreach($items as $item){
            array_push($model_files,$path.'/'.$item);
        }
    }
}

function LoadClasses(){
    $dirs = glob('*', GLOB_ONLYDIR);
    $static = [];
    for($i = 0; $i < count($dirs); $i++){
        $dir = $dirs[$i];
        searchFile($dir."/model");
    }
}

function GetAllClasses(){
    global $model_files;
    LoadClasses();
    $classes = [];
    foreach($model_files as $file){
        require($file);
        $classe = file_get_php_classes($file);
        foreach($classe as $class){
            array_push($classes, $class);
        }
    }
    return $classes;
}

function file_get_php_classes($filepath) {
    $php_code = file_get_contents($filepath);
    $classes = get_php_classes($php_code);
    return $classes;
}
  

function get_php_classes($php_code) {
    $classes = array();
    $tokens = token_get_all($php_code);
    $count = count($tokens);
    for ($i = 2; $i < $count; $i++) {
      if (   $tokens[$i - 2][0] == T_CLASS
          && $tokens[$i - 1][0] == T_WHITESPACE
          && $tokens[$i][0] == T_STRING) {

          $class_name = $tokens[$i][1];
          $classes[] = $class_name;
      }
    }
    return $classes;
}

function RunMigrates(){
    require('classes/model.php');
    $classes = GetAllClasses();
    $db = new Model();
    $tables = $db->getAllTables(); 
    $obj = [];
    $created = False;
    foreach($classes as $class){
        $c = new $class();        
        array_push($obj, $c);
        if(!in_array($class, $tables)){
            LogVal("Table created with name: ".$class);
            $db->prepare($c->CreateTable());
            $db->execute();
            $created = true;
        }else{
            checkColumns($c, $class);
        }
    }
    CheckDatabase($classes,$db, $tables, $obj);
}

function checkColumns($class, $name){
    $db = new Model();    
    $columns = $db->getColumns($name); 
    array_shift($columns);
    $vars = get_object_vars($class);
    foreach($vars as $var){
        $option = CheckVar($var, $columns, $class);
        if($option == 1){
            $db->prepare($var->GetQuery($name));
            $db->execute();
            LogVal("Added column: ".$var->name." to table: ".$name);
        }else if($option == 2){
            $db->prepare($var->ChangeQuery($name));
            $db->execute();
            LogVal("Changed Field: ".$var->name." From table ".$name);
        }
    }
}

function CheckVar($var, $columns, $class){
    foreach($columns as $column){
        if($column['Field'] == $var->name){
            if($column['Key'] != null){
                $db = new Model();
                $db->prepare("SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = '".DB_Database."' and COLUMN_NAME='".$column['Field']."' and TABLE_NAME='".$class::name()."'");
                $db->execute();
                $fk = $db->fetch();
                if($var->indexName != $fk['CONSTRAINT_NAME']){
                    echo "test";
                    removeKey($column['Field'], $class::name());
                    return 2;
                }else if($var->object::name() != $fk["REFERENCED_TABLE_NAME"]){
                    removeKey($column['Field'], $class::name());                    
                    return 2;
                }
            }
            if($column['Type'] != $var->type){
                return 2;
            }else if($column['Null'] == "YES" && $var->can_be_null != True || $column['Null'] == "NO" && $var->can_be_null != False){
                return 2;
            }else if($column['Default'] == null && $var->defaultValue != null || $column['Default'] != null && $var->defaultValue == null){
                return 2;
            }
            else{
                return 0;
            }
        }
    }
    return 1;
}

function removeKey($field, $table){
    $db = new Model();
    $db->prepare("SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = '".DB_Database."' and COLUMN_NAME='".$field."'");
    $db->execute();
    $fk = $db->fetch();
    $db->prepare("ALTER TABLE `$table` DROP FOREIGN KEY `".$fk['CONSTRAINT_NAME']."`;");
    $db->execute();
}

function CheckDatabase($classes, $db, $tables,$obj){
    foreach($tables as $table){
        if(!in_array($table,$classes)){
            $db->prepare("DROP TABLE ".$table);
            $db->execute();
            LogVal("Dropped table: ".$table);           
        }else{
            $columns = $db->getColumns($table); 
            array_shift($columns);
            foreach($columns as $column){
                $vars = get_object_vars($obj[array_search($table,$classes)]);
                checkVar_Columns($vars, $column,$db, $table);
                
            }            
        }
    }
}

function checkVar_Columns($vars, $column,$db,$table){
    foreach($vars as $var){
        if($column['Field'] == $var->name){

            return True;
        }
    }   
    if($column['Key'] != null){
        $db->prepare("SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = '".DB_Database."' and COLUMN_NAME='".$column['Field']."'");
        $db->execute();
        $fk = $db->fetch();
        $db->prepare("ALTER TABLE `$table` DROP FOREIGN KEY `".$fk['CONSTRAINT_NAME']."`;");
        $db->execute();
        LogVal("Removed foreign key from column:".$column['Field']." at table: ".$table); 
        
    }        
    $db->prepare("ALTER TABLE $table DROP COLUMN ".$column['Field']); 
    $db->execute();
    LogVal("Dropped column:".$column['Field']." from table: ".$table);     
    return False;
}


function inserts(){
    require('classes/model.php');
    $classes = GetAllClasses();
    foreach($classes as $class){
        $class::insert();
    }
}