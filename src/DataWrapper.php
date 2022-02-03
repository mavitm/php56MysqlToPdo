<?php namespace MyPdo;

class DataWrapper
{
    public $rowIndex = 0;
    public $data;

    public function __construct($rows)
    {
        $this->data = $rows;
    }

    public function getRowByKey($key){
        if(empty($this->data[$key])){
            return false;
        }
        return $this->data[$key];
    }

    public function getRow(){
        if(empty($this->data[$this->rowIndex])){
            return false;
        }
        $row = $this->rowIndex;
        $this->rowIndex += 1;
        return $row;
    }

    public function dataSeek($row_number){
        if($row_number < 0){
            $this->rowIndex = 0;
        }else{
            $this->rowIndex = $row_number;
        }
    }

    public function __toString()
    {
        return json_encode($this->data);
    }

    public function serialize() {
        return serialize($this->data);
    }

    public function unserialize($data) {
        $this->data = unserialize($data);
    }

    public function getData() {
        return $this->data;
    }

}