<?php
session_start();

require_once '../core/bootstrap.php';


$data_pdo = require_once '../db/DbPdo.php';
$data = require_once '../db/database.php';


//connessione SINGLETON PDO
$pdoConn = App\DB\DbPdo::getInstance($data);
$conn = $pdoConn->getConn();


$router = new AltoRouter();
// match current request url



$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
$page_num = filter_input(INPUT_GET, 'page_num', FILTER_VALIDATE_INT);   // ritorna null se non presente e false se non è un intero 
$page_size = filter_input(INPUT_GET, 'page_size', FILTER_VALIDATE_INT); // ritorna null se non presente e false se non è un intero 



/* ROTTE */
$router->map( 'GET', '/api/v1/node_id/[i:idnode]/language/[a:lang]', function($idnode, $lang ) use ($conn, $search, $page_num, $page_size) {

  //$controller = new App\Controllers\PageController($fb, $callbackUrl,$get, $post);
 

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





  //https://graph.facebook.com/v2.8/me/friends?access_token=EAAI02UZAMxhoBAKZB9PMJPhiFSTX6FK4WPzyWlp8BOWnEI2zzOFWpuQzPwOUmyMTIMUIAvORkmiyPe2ujRwioz5uQg26LzaTLi40ZCBvTr1hkK8DfLSSZCwvg1mLl12t7uINiD5WgOAqoSoefLVJCmx7R5jCFIZC3mqWpZAAjesTQPDVn6IGpMibVWnLXvMPhdqLYOQZCCSxX7FB9BEPZAp5W3uy0PX0lTkZCOvB8DOkrxwZDZD