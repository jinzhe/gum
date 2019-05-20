<?php
require "config.php";
require "core/gum.php";
gum::init();
$db     = new db();
$uri    = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$router = $uri[1];
if ($router == "" || $router == "/index.php" || $router == "/index.html") {
    require "theme/" . THEME . "/index.php";
} else {
    $action = $uri[2] ?? "";
    $action = str_replace(".html", "", $action); //清理无用.html
    $file   = "theme/" . THEME . "/" . $router . ".php";
    if (file::has($file)) {
        if (strpos($action, "-") != false) {
            $params = explode("-", $action);
            $id     = $params[0];
            $page   = $params[1] ?? 1;
        } else {
            $id   = $action;
            $page = 1;
        }
        require $file;
    } else {
        gum::not_found();
    }
}