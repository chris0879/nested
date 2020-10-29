<?php
require_once '../core/bootstrap.php';
require_once '../db/DbPdo.php';
$dbconfig = require_once '../db/config.php';
//connessione SINGLETON PDO
$pdoConn = App\DB\DbPdo::getInstance($dbconfig);
$conn = $pdoConn->getConn();
// match current request url
$router = new AltoRouter();
//parametri opzionali
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
$page_num = filter_input(INPUT_GET, 'page_num', FILTER_VALIDATE_INT);   // ritorna null se non presente e false se non è un intero 
$page_size = filter_input(INPUT_GET, 'page_size', FILTER_VALIDATE_INT); // ritorna null se non presente e false se non è un intero 
/* ROTTE */
$router->map( 'GET', '/api/v1/node_id/[i:idnode]/language/[a:lang]', function($idnode, $lang ) use ($conn, $search, $page_num, $page_size) {

  $options = [
    'idnode' => $idnode,
    'lang' => $lang,
    'search' => $search,
    'page_num' => $page_num,
    'page_size' => $page_size
  ];

  try{
    $controller = new App\Controllers\ApiController($conn, $options);
    $nested = $controller->getNested();
    echo $nested;
    die();
  }catch(PDOException $e){
    die($e->getMessage());
  }
 
});

$match = $router->match();

// call closure or throw 404 status
if( $match && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] );
} else {
  
    // no route was matched
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    die();
}

