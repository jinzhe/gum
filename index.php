<?php
require "config.php";
require "core/gum.php";
gum::init();
$db = new db();
 
$uri=$_SERVER['REQUEST_URI'];

if ($uri=="/"||$uri=="/index.php"||$uri=="/index.html") {
    require "theme/".THEME."/index.php";
}elseif (substr_count($uri,'/')>1) {
    preg_match('{\/(.*)/(.*)\.html}i', $uri, $matches);
    // print_r($matches);
    if (count($matches)>0) {
        $router = $matches[1];
        if(strpos($matches[2],"-")!=false){
            $params=explode("-",$matches[2]);
            $id = $params[0];
            $page = isset($params[1])?$params[1]:1;
        }else{
            $id     = $matches[2];
            $page = 1;
        }
        
        $file="theme/" . THEME . "/" . $router . ".php";
        if(file::has($file)){
            require $file;
        }else{
            gum::not_found();
        }
    }else{
        gum::not_found();
    }
}else{
    gum::not_found();
}


// if(!file_exists("index.html") || time()-filemtime("index.html")>300){
//     gum::init();
//     $db = new db();
//     if(ini_get('output_buffering')!==false){
//         ob_start();
//     }
//     require "theme/light/index.php";
//     $content=ob_get_clean();
//     file_put_contents("index.html",$content);
//     echo $content;
// }else{
//     gum::redirect("index.html");
// }