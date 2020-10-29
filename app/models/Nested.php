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
        // response Api
        $api = [
            'nodes' => [
                'node_id' => '',
                'name' => '',
                'children_count' => ''
            ],
            //'next_page' => '', //(opzionale) il numero della prossima pagina se i record sono più di page_size
            //'prev_page' =>  '',//(opzionale)il numero della pagina precedente se page_num è oltre la pagina 0
            'error' => '' //(opzionale): se è avvenuto un errore compilare con il messaggio dell’errore. 
        ];

        // ritorna le informazioni del nodo selezionato
        $node = $this->getNodeById($idNode);
        if(!$node){
           $api['error'] ='ID nodo non valido';
           return $api;
        }
         // ritorna un array che contiene il nome dei nodi // $language_all['name'][$idNode][$language_select]
        $language_all = $this->getAllLanguage($search, $idNode);

        $this->setLanguage($language_all);

        /*  se non trovo il nome del nodo ritorno con errore  */
        if(!$language_all['name'][$idNode][$language_select]){
            $api['error'] ='language error';
            return $api;
        };
      
       
        // ritorna i discendenti partendo dai limiti left e right del nodo specificato. Ritorna empty se non ha discendenti.
        $descendants = $this->createDescendants($node->iLeft,$node->iRight);
       
        $this->setDescendants($descendants);
         
        // inserisco le informazioni nel nodo principale
        $this->setNode($node,$language_select);
        // inserisco le informaziono dei nodi figli
        if(!empty($descendants)){
            foreach($descendants as $row){
                $this->setNode($row,$language_select);
            }
        }

        $api['nodes'] = $this->getNode();

        return $api;

    }


    public function getAllLanguage($search, $idNode)
    {  
        $trova = "%$search%";
        $name = [];
        $id_search = [];
        $query = $this->conn->prepare("SELECT * from node_tree_names WHERE NodeName LIKE ? or idNode = ? ");
        $query->execute([$trova,$idNode]);
        $rows =  $query->fetchAll();
        //$query->debugDumpParams();
        $n =  count($rows);
        for($x=0; $x<$n; $x++){
            $name[$rows[$x]->idNode][$rows[$x]->language] = $rows[$x]->NodeName;
            $id_search[] = $rows[$x]->idNode;
        }
        // per prelevare il valore del nome:  $result['name'][$idNode]['language']
        // per prelevare tutti gli id $result['id_search'];
        $result = [
            'name' => $name,
            'id_search' => $id_search,
        ];
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

        // conterrà gli id da ricercare nella tabella node_tree filtrati con il parametro search
        $id_search = $this->getLanguage()['id_search'];
        //sanitize $id_search 
        $id_sanitized = array_filter(array_unique(array_map('intval', (array)$id_search)));
        $id = implode(',',$id_sanitized);
        $query = $this->conn->prepare("SELECT * from node_tree  WHERE iLeft > :iLeft and iRight < :iRight and idNode in ($id) ;");
        $array = array(
            'iLeft' => $left,
            'iRight' => $right
        );
        $query->execute($array);
        $result = $query->fetchAll();
        //$query->debugDumpParams();
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
            'name' => $this->getLanguage()['name'][$obj->idNode][$language_select],
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
