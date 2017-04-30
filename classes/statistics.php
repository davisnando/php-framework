<?php

class Statistics{
    public static function getAll(){
        $db = new Model();
        $db->prepare("SELECT * FROM Visitors ");
        $result = $db->GetAll();
        $olddate = Null;
        $dayarr = [];
        $all = [];
        foreach($result as $row){
            $date = new DateTime($row['VisitDate']);
            $strip = new DateTime($date->format('Y-m-d'));
            if($olddate == Null){
                $olddate = $strip;
                array_push($dayarr,$row);
            }else{
                $diffrence = $strip->diff($olddate);
                $days = $diffrence->format("%a");
                $olddate = $strip;
                if($days == 0){
                    array_push($dayarr,$row);
                }else{
                    array_push($all,$dayarr);
                    $dayarr = [$row];
                }
            }
        }
        return json_encode($all);
    }
    public static function getAllFromPage($page){
        $db = new Model();
        $db->prepare("SELECT * FROM Visitors WHERE Page=:p ");
        $db->bind(":p",$page);
        $result = $db->GetAll();
        $olddate = Null;
        $dayarr = [];
        $all = [];
        foreach($result as $row){
            $date = new DateTime($row['VisitDate']);
            $strip = new DateTime($date->format('Y-m-d'));
            if($olddate == Null){
                $olddate = $strip;
                array_push($dayarr,$row);
            }else{
                $diffrence = $strip->diff($olddate);
                $days = $diffrence->format("%a");
                $olddate = $strip;
                if($days == 0){
                    array_push($dayarr,$row);
                }else{
                    array_push($all,$dayarr);
                    $dayarr = [$row];
                }
            }
        }
        return json_encode($all);
    }
    public static function countOnlineVisitors(){
        $db = new Model();
        $db->prepare("DELETE FROM Vistors_online WHERE Last_seen < (NOW() - INTERVAL 1 MINUTE)");
        $db->execute();
        $db->prepare("SELECT * FROM Vistors_online");
        $result = $db->GetAll();
        return count($result);
    }
    public static function countAllVisitorsTodayOnly(){
        $db = new Model();
        $db->prepare("SELECT * FROM Visitors WHERE VisitDate > (NOW() - INTERVAL 1 DAY)");
        $result = $db->GetAll();
        return count($result);
    }
    public static function countAllUniqueVisitors(){
        $db = new Model();
        $db->prepare("SELECT * FROM Visitors WHERE Uniek=1");
        $result = $db->GetAll();
        return count($result);
    }
}