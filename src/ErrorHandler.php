<?php namespace MyPdo;

class ErrorHandler{

    const DS = DIRECTORY_SEPARATOR;
    const DF = 'd.m.Y H:i:s'; // date format

    private static $instance = null;

    private
        $err=array(),
        $debugBack=false;     


    private $opt = [
        //log errors?
        "save" => false,

        //error files log directory
        "outPath" => "",

        //show rror
        "debug" => false,

        //stop the system in case of error?
        "exit" => "false"
    ];    

    public function __construct(){
        $this->debugBack = function_exists('debug_backtrace');
    }

    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setOpt($opt){
        $this->opt = $opt;
        return $this;
    }

    public function setOptItem($key, $value){
        $this->opt[$key] = $value;
        return $this;
    }

    public function getLastError(){
        //return end($this->err);
        return $this->err[count($this->err) - 1];
    }

    public function getLastErrorByClass($class){
        krsort($this->err);
        $className = $class;
        if(is_object($class)){
            $className = get_class($class);
        }
        $error = [];
        foreach($this->err as $err){
            if($err['class'] == $className){
                $error = $err;
                break;
            }
        }
        ksort($this->err);
        return $error;
    }

    public function errCompile($line, $func, $fail, $class, $extra=[]) {
        $errEnd = array();
        if ($this->debugBack) {
            $errPre = debug_backtrace();
            foreach ($errPre as $err) {
                if (!isset($err['class'])){
                    continue;
                }
                if ($err['class'] === $class) {
                    array_push($errEnd, $err);
                }
            }
            $errEnd = end($errEnd);
        }else{
            $errEnd['file'] = $this->_phpSelf();
            $errEnd['line'] = $line;
            $errEnd['function'] = $func;
            $errEnd['class'] = $class;
        }

        array_push($this->err, array(
            'file' => $errEnd['file'],
            'line' => $errEnd['line'],
            'func' => $class . '::' . $errEnd['function'],
            'fail' => $fail,
            'desc' => implode('\n', $extra)
        ));
    }

    private function _errControl() {

        $err = count($this->err) > 0 ? $this->err[0] : null;

        if (is_null($err)){
            return;
        }

        if ($this->opt["save"]) {
            $data = array(
                'name' => Connector::getInstance()->getDbName(),
                'fail' => $err['fail'],
                'func' => $err['func'],
                'line' => $err['line'],
                'file' => $err['file'],
                'time' => date(self::DF)
            );

            if(strlen($this->opt["outPath"]) > 0){
                $path = str_replace("/", self::DS, $this->opt["outPath"]);
                $path = rtrim($path, self::DS);
                $path = $path. self::DS . date('d-m-Y') . '.error';
                
                $this->writeFile($path, $data);
            }
        }

        if ($this->opt["debug"]) {
            printf('<pre class="dbo_error">' . PHP_EOL .
                '<strong>DATABASE ERROR</strong>' . PHP_EOL .
                'file : %s' . PHP_EOL .
                'line : %u' . PHP_EOL .
                'fail : %s' . PHP_EOL .
                '</pre>%s',
                $err['file'], $err['line'], $err['fail'], PHP_EOL
            );

            if ($this->opt["exit"]){
                exit();
            }
        }
    }

    private function writeFile($path, $data){
        $con = fopen($path, "a+");
        if($con){
            fwrite($con, $data);
        }
        fclose($con);
    }



}