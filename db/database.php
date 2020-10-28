<?php
// PDO 
return[
    'driver' => 'mysql', 
    'host' => 'localhost',
    'user' => 'root',
    'password' => 'furiaceca',
    'database' => 'nested_set',
    'dsn' =>'mysql:host=localhost;dbname=nested_set;charset=utf8',
    'options' => [
        [ PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    ]
];
