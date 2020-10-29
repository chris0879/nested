
<?php


$servername = "localhost";
$username = "root";
$password = "furiaceca";
$dbname = 'nested_set';


try {

    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   // echo "Connected successfully<br>"; 
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}




$disableForeignKey = $conn->exec('SET FOREIGN_KEY_CHECKS=0;');
$dropTable = $conn->exec("DROP TABLE IF EXISTS node_tree;");
$dropTable = $conn->exec("DROP TABLE IF EXISTS node_tree_names;");
$activeForeignKey = $conn->exec('SET FOREIGN_KEY_CHECKS=1;');


/*CREAZIONE TABELLA node_tree */
try{
    $createTable = $conn->exec("CREATE TABLE IF NOT EXISTS  node_tree (
        `idNode` int(6) NOT NULL AUTO_INCREMENT,
        `level` varchar(30) NOT NULL,
        `iLeft` smallint(5) NOT NULL,
        `iRight` smallint(5) NOT NULL,
        PRIMARY KEY (`idNode`)
    )");
}catch(PDOException $e){
    echo $e->getMessage();
}


/* POPOLAMENTO TABELLA node_tree */
try{

    /*
    $insert = $conn->exec("
    INSERT INTO node_tree (level, iLeft, iRight)
    VALUES ('Abbigliamento','1','18'), ('Uomo','2','7'),('Camicia','3','4'),('Pantalone Uomo','5','6'),('Donna','8','17'),('Gonna','9','10'),('Pantaloni donna','11','16'),('Sportivi','12','13'),('Eleganti','14','15');
    ");
    */


    
    $insert = $conn->exec("
INSERT INTO node_tree (level, iLeft, iRight)
VALUES ('2','2','3'), ('2','4','5'),('2','6','7'),('2','8','9'),('1','1','24'),('2','10','11'),('2','12','19'),('3','15','16'),('3','17','18'),('2','20','21'),('3','13','14'),('2','22','23');
");


}catch(PDOException $e){
    echo $e->getMessage();
}




/*CREAZIONE TABELLA node_tree_names */
try{
    $createTable = $conn->exec("CREATE TABLE IF NOT EXISTS  node_tree_names (
        `idNode` INT(6),
        `language` enum ('english','italian') NOT NULL,
        `NodeName` varchar(30) NOT NULL,
        FOREIGN KEY (idNode) REFERENCES node_tree(idNode)
    )");
  
}catch(PDOException $e){

   echo $e->getMessage();

}



/* POPOLAMENTO TABELLA node_tree_names */
try{

    /*
    $insert = $conn->exec("
    INSERT INTO node_tree_names (idNode, language, NodeName)
    VALUES ('1','english','Marketing'), ('1','italian','Marketing'),('2','english','Helpdesk'),('2','italian','Supporto Tecnico'),('3','english','Managers'),('3','italian','Manager'),('4','english','Customer Account'),('4','italian','Assistenza Cliente'),('5','english','0brand Srl'),('5','italian','0brand Srl'),('6','english','Accounting'),('6','italian','Amministrazione'), ('7','english','Sales'),('7','italian','Supporto Vendite'),('8','english','Italy'),('8','italian','Italia'),('9','english','Europe'),('9','italian','Europa');
    ");
    */

    
    $insert = $conn->exec("
    INSERT INTO node_tree_names (idNode, language, NodeName)
    VALUES ('1','english','Marketing'), ('1','italian','Marketing'),('2','english','Helpdesk'),('2','italian','Supporto Tecnico'),('3','english','Managers'),('3','italian','Manager'),('4','english','Customer Account'),('4','italian','Assistenza Cliente'),('5','english','0brand Srl'),('5','italian','0brand Srl'),('6','english','Accounting'),('6','italian','Amministrazione'), ('7','english','Sales'),('7','italian','Supporto Vendite'),('8','english','Italy'),('8','italian','Italia'),('9','english','Europe'),('9','italian','Europa'),('10','english','Developers'),('10','italian','Sviluppatori'), ('11','english','North America'),('11','italian','Nord America'),('12','english','Quality Assurance'),('12','italian','Controllo Qualità') ;
    ");

}catch(PDOException $e){
    echo $e->getMessage();
}






echo "create table and insert data... ok <br>";

/*
`idNode` int(6) NOT NULL AUTO_INCREMENT,
`level` varchar(30) NOT NULL,
`iLeft` smallint(5) NOT NULL,
`iRight` smallint(5) NOT NULL,

`idNode` INT(6),
`language` enum ('english','italian') NOT NULL,
`NodeName` varchar(30) NOT NULL,
*/


$stmt = $conn->query("SELECT * FROM node_tree");
$rows = $stmt->fetchAll();

/*
array (size=12)
  0 => 
    array (size=8)
      'idNode' => string '1' (length=1)
      0 => string '1' (length=1)
      'level' => string '2' (length=1)
      1 => string '2' (length=1)
      'iLeft' => string '2' (length=1)
      2 => string '2' (length=1)
      'iRight' => string '3' (length=1)
      3 => string '3' (length=1)

*/


getLeftRightByIdNode($rows);


function getLeftRightByIdNode ($rows, $id=5) {

    $result = [];
    $n = count($rows);
    
    for($x=0; $x<$n; $x++){

       $idNode = $rows[$x]['idNode'];
       $iLeft = $rows[$x]['iLeft'];
       $iRight = $rows[$x]['iRight'];

        if($idNode == $id){
            //var_dump($idNode);
            $result = [
                'idNode' => $idNode,
                'iLeft' => $iLeft,
                'iRight' => $iRight,
                'rows' => $rows
            ];

            return  $result;
        }
    }

    return $result;
}

