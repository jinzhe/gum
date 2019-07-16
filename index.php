<?php
require "config.php";
require "core/gum.php";
gum::init();
$db=new db();

$rules =include_once "theme/" . THEME . "/router.php";
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// 检测非法注入
if (check::safe($uri)) {
    gum::not_found("Access denied!");
}

foreach ($rules as $k => $v) {
    if (preg_match("|" . $v["regexp"] . "|U", $uri)) { //匹配当前访问url
        preg_match_all("|" . $v["regexp"] . "|U", $uri, $matches);
        if (isset($v["params"])) {
            foreach ($v["params"] as $kk => $vv) {
                $$vv = $matches[$kk + 1][0];
            }
        }
        $theme_file   = "theme/" . THEME . "/" . $v["file"] . ".php";
        $file=$v["file"];
        if (file::has($theme_file)) {
           include_once $theme_file; 
        }else{
            gum::not_found();
        }
        break;
    }
}
