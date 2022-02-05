# Connection
```php
$connector = \MyPdo\Connector::getInstance();
$connector->setConfig([
    "host" => "localhost",
    "port" => 3306,
    "name" => "themes",
    "user" => "root",
    "pass" => ""
])->connection();
```

# use with functions
```php 
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
```

# directly with their own methods
```php 
$query = $connector->query()->setSql("select * from migrations");
$result = $query->runSql()->all();

//ex 2
$query2 = \MyPdo\MyQuery::getInstance()->setSql("select * from migrations");
$result2 = $query->runSql()->all();


$secondRow = $query2->getRow(1);
$thirdRow = $query2->getRow(2, ['custom'=>'if there is no data give this']);
$fourthRow = $query2->getRow(3, false);

//ex 2 loop
if($query2->result()){
    foreach($result2 as $row){
        //...
    }
}

//ex 3
$result3 = $connector->query()->setSql("select * from migrations")->runSql()->all();
```

**add parameters**
```php 
$result5 = $connector->query()->setSql("select * from migrations where batch=:batch")
    ->setArg([":batch"=>2])
    ->runSql()
    ->all();
    
$count      = $result5->numRows();
```

**other methods**
```php 
\MyPdo\MyQuery::getInstance()->queryCount();  //how many times queries were made during the flow
```

```php
$insert = $connector->query()->setSql("insert into migrations (batch) values (:batch)")
    ->setArg([":batch"=>2])
    ->runSql();

$insertId   = $insert->insertID();
$queryTime  = $insert->queryTime();
```

```php
$update = $connector->query()->setSql("update migrations set batch=:batch")
    ->setArg([":batch"=>2])
    ->runSql();

$affRows    = $update->affRows();
$queryTime  = $update->queryTime();
```



