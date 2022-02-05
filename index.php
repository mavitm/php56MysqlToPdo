<?php
$autoload = require "vendor/autoload.php";
$existMysqlQuery = function_exists("my_mysql_query");
$classExist = class_exists("\MyPdo\Connector");

$connector = \MyPdo\Connector::getInstance();
$connector->setConfig([
    "host" => "localhost",
    "port" => 3306,
    "name" => "themes",
    "user" => "root",
    "pass" => ""
])->connection();

################################################## object query ######################################################
//
$query = $connector->query()->setSql("select * from migrations");
$result = $query->runSql()->all();

//ex 2
$query2 = \MyPdo\MyQuery::getInstance()->setSql("select * from migrations");
$result2 = $query->runSql()->all();

//ex 3
$result3 = $connector->query()->setSql("select * from migrations")->runSql()->all();


############################################## function base #########################################################
//
$query4 = my_mysql_query("select * from migrations"); //return DataWrapper Object
//$result4 = my_mysql_fetch_assoc($query4);  //first row
while ($row = my_mysql_fetch_assoc($query4)){
    $result4[] = $row;
}

my_mysql_data_seek($query4, 0); //reset loop

while ($row = my_mysql_fetch_array($query4)){
    $result42[] = $row;
}

my_mysql_data_seek($query4, 0); //reset loop

while ($row = my_mysql_fetch_object($query4)){
    $result43[] = $row;
}

################################################## object binding ######################################################

$result5 = $connector->query()->setSql("select * from migrations where batch=:batch")
    ->setArg([":batch"=>2])
    ->runSql()
    ->all();


echo '<pre>';
print_r($result43);
echo '</pre>';