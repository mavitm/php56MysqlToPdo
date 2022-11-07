<?php

use MyPdo\MyQuery;
use MyPdo\Connector;
use MyPdo\DataWrapper;
use MyPdo\ErrorHandler;

/*
 MYSQL_ASSOC
 */


/******************************************************************************/
/**
 * @throws Exception
 */
function my_mysql_connect($host, $userName, $pass){
    $connector = Connector::getInstance();
    $connector->setConfig([
        "host" => $host,
        "user" => $userName,
        "pass" => $pass
    ])->connection();
    return $connector;
}
if(!function_exists("mysql_connect")){
    /**
     * @throws Exception
     */
    function mysql_connect($host, $userName, $pass){ my_mysql_connect($host, $userName, $pass); }
}
/******************************************************************************/

/******************************************************************************/
/**
 * @throws Exception
 */
function my_mysql_select_db($dbName, $conn){
    return $conn->selectDb($dbName);
}
if(!function_exists("mysql_select_db")){
    /**
     * @throws Exception
     */
    function mysql_select_db($dbName, $conn){ my_mysql_select_db($dbName, $conn); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_errno($conn=null){
    if(!$conn){
        $conn = Connector::getInstance()->query();
    }
    return $conn->getErrorNo();
}
if(!function_exists("mysql_errno")){
    function mysql_errno($conn=null){ my_mysql_errno($conn); }
}
/******************************************************************************/

/******************************************************************************/
/**
 * @Deprecated
 * @param $dbName
 * @param $conn
 * @return DataWrapper
 */
function my_mysql_list_tables($dbName, $conn){
    if($conn == null){
        $conn = Connector::getInstance()->query();
    }
    $result = $conn->setSql("SHOW TABLES ".$dbName)->runSql();
    return new DataWrapper($result->all());
}
if(!function_exists("mysql_list_tables")){
    function mysql_list_tables($dbName, $conn){ return my_mysql_list_tables($dbName, $conn);}
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_set_charset($charset, $conn){
    /* not set */
}
if(!function_exists("mysql_set_charset")){
    function mysql_set_charset($charset, $conn){ my_mysql_select_db($charset, $conn); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_close($conn){
    $conn->close();
}
if(!function_exists("mysql_close")){
    function mysql_close($conn){ my_mysql_close($conn); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_query($sql, $conn=null){
    if($conn == null){
        $conn = Connector::getInstance()->query(); //MyQuery::getInstance()
    }
    $result = $conn->setSql($sql)->runSql();
    return new DataWrapper($result->all());
}
if(!function_exists("mysql_query")){
    function mysql_query($sql){ return my_mysql_query($sql); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_error(){
    $error = ErrorHandler::getInstance()->getLastErrorByClass(Connector::getInstance());
    if(empty($error)){
        return ErrorHandler::getInstance()->getLastError();
    }
    return json_encode($error);
}
if(!function_exists("mysql_error")){
    function mysql_error(){ return my_mysql_error(); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_real_escape_string($unescaped_string){
    if(is_array($unescaped_string)){
        return array_map(__METHOD__, $unescaped_string);
    }
    if(!empty($inp) && is_string($unescaped_string)) {
        try {
            return Connector::getInstance()->getConnection()->quote($unescaped_string);
        } catch (\Exception $e) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $unescaped_string);
        }
    }
    return $unescaped_string;
}
if(!function_exists("mysql_real_escape_string")){
    function mysql_real_escape_string($str){
        return my_mysql_real_escape_string($str);
    }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_fetch_row(DataWrapper $result, $const="num"){
    $data = $result->getRow();
    if($data){
        return MyQuery::getInstance()->getFetch() == "num" ? $data : array_values((array)$data);
    }
    return false;
}
if(!function_exists("mysql_fetch_row")){
    function mysql_fetch_row($result, $const="num"){ return my_mysql_fetch_row($result, $const); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_fetch_array(DataWrapper $result, $const="arr"){
    $data = $result->getRow();
    if($data){
        return array_merge((array)$data, array_values((array)$data));
    }
    return false;
}
if(!function_exists("mysql_fetch_array")){
    function mysql_fetch_array($result, $const="arr"){ return my_mysql_fetch_array($result, $const); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_fetch_assoc(DataWrapper $result, $const="arr"){
    $data = $result->getRow();
    if($data){
        return (array)$data;
    }
    return false;
}
if(!function_exists("mysql_fetch_assoc")){
    function mysql_fetch_assoc($result, $const="arr"){ return my_mysql_fetch_assoc($result, $const); }
}
/******************************************************************************/

/******************************************************************************/
function my_mysql_fetch_object(DataWrapper $result, $const="obj"){
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
    function mysql_fetch_object($result, $const="obj"){ return my_mysql_fetch_object($result, $const); }
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