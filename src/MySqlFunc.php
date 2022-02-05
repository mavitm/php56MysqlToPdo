<?php

use MyPdo\MyQuery;
use MyPdo\DataWrapper;

/******************************************************************************/
function my_mysql_query($sql){
    MyQuery::getInstance()->setSql($sql)->runSql();
    return new DataWrapper(MyQuery::getInstance()->all());
}
if(!function_exists("mysql_query")){
    function mysql_query($sql){ return my_mysql_query($sql); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_error($conn){

}
/******************************************************************************/

/******************************************************************************/
function my_mysql_real_escape_string($unescaped_string){

}
/******************************************************************************/

/******************************************************************************/
function my_mysql_fetch_row(DataWrapper $result){
    $data = $result->getRow();
    if($data){
        return MyQuery::getInstance()->getFetch() == "num" ? $data : array_values((array)$data);
    }
    return false;
}
if(!function_exists("mysql_fetch_row")){
    function mysql_fetch_row($result){ return my_mysql_fetch_row($result); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_fetch_array(DataWrapper $result){
    $data = $result->getRow();
    if($data){
        return array_merge((array)$data, array_values((array)$data));
    }
    return false;
}
if(!function_exists("mysql_fetch_array")){
    function mysql_fetch_array($result){ return my_mysql_fetch_array($result); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_fetch_assoc(DataWrapper $result){
    $data = $result->getRow();
    if($data){
        return (array)$data;
    }
    return false;
}
if(!function_exists("mysql_fetch_assoc")){
    function mysql_fetch_assoc($result){ return my_mysql_fetch_assoc($result); }
}
/******************************************************************************/

/******************************************************************************/
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
if(!function_exists("mysql_fetch_object")){
    function mysql_fetch_object($result){ return my_mysql_fetch_object($result); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_data_seek(DataWrapper $result, $row_number){
    return $result->dataSeek($row_number);
}
if(!function_exists("mysql_data_seek")){
    function mysql_data_seek($result, $row_number){ my_mysql_data_seek($result, $row_number); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_insert_id(){
    return MyQuery::getInstance()->insertID();
}
if(!function_exists("mysql_insert_id")){
    function mysql_insert_id(){ my_mysql_insert_id(); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_affected_rows(){
    return MyQuery::getInstance()->affRows();
}
if(!function_exists("mysql_affected_rows")){
    function mysql_affected_rows(){ my_mysql_affected_rows(); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_num_rows(){
    return MyQuery::getInstance()->numRows();
}
if(!function_exists("mysql_num_rows")){
    function mysql_num_rows(){ my_mysql_num_rows(); }
}
/******************************************************************************/