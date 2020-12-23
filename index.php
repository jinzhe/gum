<?php
require "config.php";
require "core/gum.php";
gum::init();
$db = new db();

$theme_dir    = file_get_contents("theme/default.txt");
$theme_config = include_once "theme/" . $theme_dir . "/config.php";
$uri          = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (check::safe($uri)) {
    gum::not_found("Access denied!");
}

foreach ($theme_config["router"] as $k => $v) {
    if (preg_match("|" . $v["regexp"] . "|U", $uri)) {
        preg_match_all("|" . $v["regexp"] . "|U", $uri, $matches);
        if (isset($v["params"])) {
            foreach ($v["params"] as $kk => $vv) {
                $$vv = $matches[$kk + 1][0];
            }
        }
        $theme_file = "theme/" . $theme_dir . "/" . $v["file"] . ".php";
        $file       = $v["file"];
        if (file::has($theme_file)) {
            require "service/config.php";
            $config = [];
            foreach (config::get($db) as $v) {
                $config[$v["key"]] = $v["value"];
            }
            include_once $theme_file;
        } else {
            gum::not_found();
        }
        break;
    }
}
