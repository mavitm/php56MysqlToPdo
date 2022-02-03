<?php namespace MyPdo;

use MyPdo\MyQuery;
use MyPdo\DataWrapper;

function my_mysql_query($sql){
    MyQuery::getInstance()->setSql($sql)->runSql();
    return new DataWrapper(MyQuery::getInstance()->all());
}

function my_mysql_error($conn){

}

function my_mysql_real_escape_string($unescaped_string){

}

function my_mysql_fetch_row(DataWrapper $result){
    $data = $result->getRow();
    if($data){
        return MyQuery::getInstance()->getFetch() == "num" ? $data : array_values((array)$data);
    }
    return false;
}

function my_mysql_fetch_array(DataWrapper $result){
    $data = $result->getRow();
    if($data){
        return (array)$data;
    }
    return false;
}

function my_mysql_fetch_assoc(DataWrapper $result){
    $data = $result->getRow();
    if($data){
        return array_merge((array)$data, array_values((array)$data));
    }
    return false;
}

function my_mysql_fetch_object(DataWrapper $result){
    $data = $result->getRow();
    if($data){
        if(MyQuery::getInstance()->getFetch() == "obj"){
            return $data;
        }
        return (object) $data;
    }
    return false;
}

function my_mysql_data_seek(DataWrapper $result, $row_number){
    $result->dataSeek($row_number);
}