<?php
namespace App\Models;
use \PDO;

class Nested{

    protected $conn;
    protected $table_name = 'nested_set';
    protected $node = [];
    protected $language = [];
    protected $descendants = [];

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }


    public function getNestedSet($idNode, $language_select, $search, $page_num, $page_size)
    {
        $sql_mode = $this->conn->exec("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        // response
        $api = [
            'node' => [
                'node_id' => '',
                'name' => '',
                'children_count' => ''
            ],

            'next_page' => '', //(opzionale) il numero della prossima pagina se i record sono più di page_size
            'prev_page' =>  '',//(opzionale)il numero della pagina precedente se page_num è oltre la pagina 0
            'error' => '' //(opzionale): se è avvenuto un errore compilare con il messaggio dell’errore. 

        ];

       
        // ritorna le informazioni del nodo selezionato
        $node = $this->getNodeById($idNode);
        if(!$node){
           $api['error'] ='ID nodo non valido';
           return $api;
        }

         // ritorna un array che contiene il titolo del nodo nella rispettiva lingua  // $result[$idNode]['language']
         $language_all = $this->getAllLanguage();
         $this->setLanguage($language_all);

         if(!$language_all[$idNode][$language_select]){
            $api['error'] ='language error';
           return $api;
         };
       



         // ritorna i discendenti partendo dai limiti left e right del nodo specificato. Ritorna empty se non ha discendenti.
        $descendants = $this->createDescendants($node->iLeft,$node->iRight);
        $this->setDescendants($descendants);
        /*
        0 => 
        object(stdClass)[26]
        public 'idNode' => string '2' (length=1)
        public 'level' => string 'Uomo' (length=4)
        public 'iLeft' => string '2' (length=1)
        public 'iRight' => string '7' (length=1)
        */

         
        // inserisco le informaziono nel nodo 
        $this->setNode($node,$language_select);
        /*
        object(stdClass)[9]
        public 'idNode' => string '1' (length=1)
        public 'level' => string 'Abbigliamento' (length=13)
        public 'iLeft' => string '1' (length=1)
        public 'iRight' => string '18' (length=2)
        */


        if(!empty($descendants)){
            foreach($descendants as $row){
                $this->setNode($row,$language_select);
            }
        }


       


        $api['node'] = $this->getNode();

        return $api;

        
    }


   



    public function getAllLanguage()
    {
        $result = [];
        $stmt = $this->conn->query("SELECT * from node_tree_names");
        $rows =  $stmt->fetchAll();
        $n =  count($rows);
        for($x=0; $x<$n; $x++){
            $result[$rows[$x]->idNode][$rows[$x]->language] = $rows[$x]->NodeName;
        }
        // per prelevare il valore  $result[$idNode]['language']
        return $result;
    }



    // per trovare i limiti destro e sinistro del nodo
    public function getNodeById($idNode)
    {
        $query = $this->conn->prepare("SELECT * from node_tree  WHERE idNode = :id");

        $array = array(
            'id' => $idNode
        );

        $query->execute($array);
        $result = $query->fetch();
        return $result;

    }



    public function createDescendants($left, $right){

        $query = $this->conn->prepare("SELECT * from node_tree  WHERE iLeft > :iLeft and iRight < :iRight  ");

        $array = array(
            'iLeft' => $left,
            'iRight' => $right
        );

        $query->execute($array);
        $result = $query->fetchAll();
        return $result;

    }
    


    public function countChildren($iLeft, $iRight, $descendants)
    {
        $child_count = 0;
        foreach($descendants as $row){
            if($row->iLeft > $iLeft &&  $row->iRight < $iRight){
                $child_count++;
            }

        }

        return  $child_count;
    }


    public function setNode($obj,$language_select)
    {

        $array_node = array(
            'node_id' => $obj->idNode,
            'name' => $this->getLanguage()[$obj->idNode][$language_select],
            'children_count' => $this->countChildren( $obj->iLeft, $obj->iRight, $this->getDescendants()),
            'level' => $obj->level,
        );

       $this->node[] = $array_node;
    }


    public function getNode()
    {
        return $this->node;
    }

    public function setLanguage(array $language)
    {
        $this->language = $language;
    }


    public function getLanguage():array
    {
        return $this->language;
    }


    public function setDescendants(array $descendants)
    {
        $this->descendants = $descendants;
    }

    public function getDescendants():array
    {
        return $this->descendants;
    }

}
