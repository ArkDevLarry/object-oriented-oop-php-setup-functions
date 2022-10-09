<?php

class Controller
{
    public function view($path, $dir, $data = [])
    {

        extract($data);
        if (file_exists("../app/views/". $dir . $path . ".php"))
        {
            include "../app/views/". $dir . $path . ".php";
        }else
        {
            include "../app/views/" . $dir . "404.php";  
        }
    }
    public function load_model($model)
    {
        if (file_exists("../app/models/" . strtolower($model) . ".class.php"))
        {
            include "../app/models/" . strtolower($model) . ".class.php";
            return $a = new $model();
        }
        return false;
    }
}