<?php
namespace App\Controllers;




class PageController
{
    protected $layout = '../layout/index.tpl.php';
    public $content = '';
  
   
    protected $get;
    protected $post;
   


    public function __construct( $get, $post)
    {
     
        $this->get = $get;
        $this->post = $post;
    }


    public function display()
    {
        require $this->layout;
    }





    public function home()
    {   
        
        
        $this->content =  view('home',['get'=>$this->get, 'post' => $this->post ]);
       
    }




    

   






}