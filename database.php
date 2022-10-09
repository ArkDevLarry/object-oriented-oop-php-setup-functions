<?php

class DB
{

    /*
    * This is the database class
    */

    public static $con;

    public function __construct()
    {
        try {

            $string = DB_TYPE . ":host=" . DB_HOST . ";dbname=" . DB_NAME;
            self::$con = new PDO($string, DB_USER, DB_PASS);

        } catch (PDOException $e) {

            die($e->getMessage());

        }
    }
    public static function getInstance() {

        if (self::$con) {
            return self::$con;
        }

        return $instance = new self();
    }

    public static function newInstance(){
        return $instance = new self();
    }
    /*
    * Read from database, Select.
    */
    public function read($query, $params = array()) {
        $stmt = self::$con->prepare($query);
        $result = $stmt->execute($params);

        if ($result) {            
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (is_array($data)) {
                return $data;
            }
            return false;
        }
        return false;

    }

    /*
    * Write to database i.e Insert, Delete.
    */
    public function write($query, $params = array()) {
        $stmt = self::$con->prepare($query);
        $result = $stmt->execute($params);

        if ($result) {
            return true;
        }
        return false;
        
    }

}
// $db = DB::getInstance();
