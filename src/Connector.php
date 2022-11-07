<?php namespace MyPdo;

class Connector{

    private static $instance = null;

    private $conn = null;

    private 
        $host,
        $port,
        $name,
        $user,
        $pass;

    private $defaultDriver = "mysql";    

    public function __construct(){
    }

    /**
     * @throws \Exception
     */
    public function setConfig($config){
        //or remove parametrs and read file
        $this->host = $config['host'];
        $this->port = empty($config['port']) ? null : $config['port'];
        $this->name = empty($config['name']) ? null : $config['name'];
        $this->user = $config['user'];
        $this->pass = $config['pass'];

        if(empty($config['host']) || empty($config['user']) || !isset($config['pass'])){
            throw new \Exception("invalid db config");
        }

        //$this->defaultDriver = $config['driver'];
        return $this;
    }

    private function __clone() {}
    private function __wakeup() {}

    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(){
        return $this->conn;
    }

    public function getDriverType(){
        return $this->defaultDriver;
    }

    public function getDbName(){
        return $this->name;
    }

    public function connection(){
        try{
            if($this->defaultDriver == "mysql"){
                $this->conn = $this->connectMysql();
            }else{
                die("no supported driver selected");
            }
        }catch(\PDOException $e){
            $this->conn = null;
            ErrorHandler::getInstance()->errCompile($e->getLine(), __METHOD__, $e->getMessage(), __CLASS__);
        }
        return $this->conn;
    }

    /**
     * @param $dbName
     * @return bool
     * @throws \Exception
     */
    public function selectDb($dbName){
        $this->name = $dbName;
        if(!$this->conn){
            throw new \Exception("database not connected");
        }
        $this->conn->exec('use '.$this->name);
        return true;
    }

    /**
     * @return \PDO
     * @throws \Exception
     */
    private function connectMysql(){
        $conStr = 'mysql:host='.$this->host;
        if($this->port){
            $conStr .= ':'.$this->port;
        }
        if($this->name){
            $conStr .= ';dbname='.$this->name;
        }
        try {
            //$driver = new \PDO('mysql:host='.$this->host.':'.$this->port.';dbname='.$this->name,$this->user,$this->pass,array(\PDO::MYSQL_ATTR_INIT_COMMAND =>"SET NAMES utf8"));
            $driver = new \PDO($conStr, $this->user, $this->pass, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $driver->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
            //$driver->setAttribute(PDO::ATTR_TIMEOUT, 5);
            //$driver->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
            //$driver->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            //$driver->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            return $driver;
        }catch (\PDOException $e){
            //WARNING https://www.php.net/manual/en/pdo.connections.php
            throw new \Exception("Connection failed");
        }
    }

    public function query($conn=null){
        if(!$conn){
            $conn = $this;
        }
        $query = MyQuery::getInstance();
        $query->setConnection($conn);
        return $query;
    }

    public function close(){
        if ($this->conn)
            $this->conn = null;
    }

    public function __destruct() {
        $this->close();
    }
}