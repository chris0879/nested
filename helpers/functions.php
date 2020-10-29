<?php

function view($view, array $data = []){
   
    extract($data);
    ob_start();
    require __DIR__ .'/../app/views/'.$view.'.tpl.php';
    $output =  ob_get_contents();
     ob_end_clean();
     return $output;
}


function redirect($url ='/'){
    header('Location:'.$url);
    exit;
}




function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}




function ArrayFromObject($objects){
    $arrayFromObject=[];
        foreach ($objects as $object) {
            $arrayFromObject[] = get_object_vars($object);
        }
    return $arrayFromObject;
}





function truncateString($string, $length)
{
    $string = strtolower($string);
    if (strlen($string) > $length) {
        $string = substr($string, 0, $length) . '..';
    }else{
        //$string = str_pad($string,  $length+1);
       // $string = str_pad($string, $length, "-", STR_PAD_BOTH); 
    }

    return ucwords($string);
}




