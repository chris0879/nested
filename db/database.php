<?php
// PDO 
return[
    'user' => 'root',
    'password' => 'furiaceca',
    'dsn' =>'mysql:host=localhost;dbname=nested_set;charset=utf8',
    'options' => [
        [ PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    ]
];
