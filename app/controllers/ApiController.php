<?php
namespace App\Controllers;
use \PDO;
use App\Models\Nested;



class ApiController
{
  
    protected $conn;
    public $content = '';
    protected $nested;
    protected $options;

    public function __construct(PDO $conn, $options)
    {
       
        $this->conn = $conn;
        $this->nested = new Nested($conn);
        $this->options = $options;
        //var_dump($this->options);
        //die();
    }

    public function display()
    {
        require $this->layout;
    }
    

    public function getNested()
    {
        /*
        effettuo la chiamata al model che mi restituirà i dati della tabella nested_set
        */
        $nested_set = $this->nested->getNestedSet($this->options['idnode'], $this->options['lang'], $this->options['search'], $this->options['page_num'], $this->options['page_size'], );
        return view('nested',['nested_set' => json_encode($nested_set)]);
      
        //return json_encode($nested_set);
    }


}