<?php namespace  MyPdo;

class MyQuery{

    const DF = 'd.m.Y H:i:s'; // date format

    private static $instance = null;

    private
        $connector      = null,
        $sql            = null,
        $sqlArgs        = [],
        $queryResult    = null,
        $queryCount     = 0,
        $queryTime      = 0,
        $queryDate      = 0;

    private 
        $insertID = 0,
        $numRows = 0,
        $affRows = 0,
        $errorNo = null,
        $errorMessage = null,
        $driverErrorCode = null;


    private $fetch = "arr";  //obj|num|arr

    public function __construct(){
        $this->connector = Connector::getInstance()->getConnection();
    }
    
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getFetch(){
        return $this->fetch;
    }

    public function setConnection($connector){
        $this->connector = $connector;
        return $this;
    }

    public function setSql($sql){
        $this->sqlArgs = null;
        $this->sql = preg_replace('/\s\s+|\t\t+/', ' ', trim($sql));
        return $this;
    }

    public function setArg() {
        $args = func_get_args();
        $this->sqlArgs = is_array($args[0]) ? $args[0] : $args;
        return $this;
    }

    public function run(){
        if($this->connector->getConnection() === null){
            $this->queryResult = false;
            ErrorHandler::getInstance()->errCompile(__LINE__, __FUNCTION__, 'Could not connect to database',__CLASS__, ["sql"=>$this->sql]);
            return $this;
        }

        $this->queryResult = $this->_readFromDbase();
        return $this;
    }

    public function runSql(){
        return $this->run();
    }

    public function execute(){
        return $this->run();
    }

    public function result() {
        return $this->queryResult != false;
    }

    public function all(){
        if (empty($this->queryResult) || is_scalar($this->queryResult)) {
            return array();
        }
        return $this->returnFetch();
    }

    public function getRow($key, $default=false){
        if (empty($this->queryResult) || is_scalar($this->queryResult)) {
            return $default;
        }

        //$key = ($key > 0 ? ($key - 1) : 0);

        if(empty($this->queryResult[$key])){
            return $default;
        }

        return $this->queryResult[$key];
    }

    public function affRows() {
        return $this->affRows;
    }

    public function insertID() {
        return $this->insertID;
    }

    public function numRows() {
        return $this->numRows;
    }

    public function queryCount() {
        return $this->queryCount;
    }

    public function queryTime() {
        return $this->queryTime;
    }

    public function getErrorNo(){
        return $this->errorNo;
    }

    public function getErrorMessage(){
        return $this->errorMessage;
    }

    public function getDriverErrorCode(){
        return $this->driverErrorCode;
    }


    public function giveInfo() {
        return array(
            'dbase' => array(
                'type' => Connector::getInstance()->getDriverType(),
            ),
            'query' => array(
                'time' => $this->queryTime,
                'date' => $this->queryDate,
                'count' => $this->queryCount,
                'numRows' => $this->numRows,
                'affRows' => $this->affRows,
                'insertID' => $this->insertID,
            ),
            'last_err' => ErrorHandler::getInstance()->getLastErrorByClass(__CLASS__),
            'last_sql' => $this->sql,
            'fetchMode' => $this->fetch,
            'result(0)' => isset($this->queryResult[0]) ? $this->queryResult[0] : null
        );
    }


    private function _readFromDbase() {

        $dbase = $this->connector->getConnection();

        // reset
        $this->numRows = $this->insertID = $this->affRows = 0;
        $this->errorNo = $this->errorMessage = $this->driverErrorCode = null;

        try{
            $prev = microtime(true);
                $query = $dbase->prepare($this->sql);

                if(is_array($this->sqlArgs) && count($this->sqlArgs) > 0){
                    $query->execute($this->sqlArgs);
                }else{
                    $query->execute();
                }
            $next = microtime(true);

            $a                      = $query->errorInfo();
            $this->errorNo          = $a[0];
            $this->driverErrorCode  = $a[1];
            $this->errorMessage     = $a[2];

        }catch (\PDOException $e){ //https://www.php.net/manual/en/class.pdoexception.php
            $a = array(
                $e->getLine(),
                $e->errorInfo,
                $e->getMessage()
            );
            if(is_array($e->errorInfo)){
                $this->errorNo          = $e->errorInfo[0];
                $this->driverErrorCode  = $e->errorInfo[1];
                $this->errorMessage     = $e->errorInfo[2];
            }
        }

        // query time
        $this->queryTime = number_format(($next - $prev), 20);
        $this->queryDate = date(self::DF);

        $this->queryCount++;


        // is fail
        if(!empty($a[1]) || $a[0] != '00000'){
            ErrorHandler::getInstance()->errCompile(__LINE__, __FUNCTION__, "Code: ".$a[0]." - Driver Code: ".$a[0]." - Message: ".$a[2], __CLASS__);
            return false;
        }

        if(preg_match('/^(insert)\s/i', $this->sql)){
            $this->insertID = $dbase->lastInsertId();
            $this->affRows = $query->rowCount();
            $this->numRows = $query->rowCount();
        }else{
            $this->insertID = 0;
            $this->affRows = $query->rowCount();
            $this->numRows = $query->rowCount();
        }

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function returnFetch(){

        if(empty($this->queryResult)){
            return $this->queryResult;
        }

        if($this->fetch == "num"){
            return array_map('array_values', $this->queryResult);
        }elseif($this->fetch == "obj"){
            return array_map(function(&$val){
                return (object) $val;
            }, $this->queryResult);
        }
        return $this->queryResult;
    }

}