<?php

Abstract Class View{
    public function __construct(){
        if(method_exists($this, 'get_context_data')){
            $this->get_context_data();
        }
        if($_SERVER['REQUEST_METHOD'] === 'GET')
            if(method_exists($this, 'get')){
                $this->get();
            }
        if($_SERVER['REQUEST_METHOD'] === 'POST')
            if(method_exists($this, 'post')){
                $this->post();
            }
    }
    static function as_view(){
        $view = View();
    }
}