<?php
require "./core/gum.php";

gum::init([
	"headers" => [
		"Access-Control-Allow-Origin: *",
		// "content-type: application/json;charset=utf-8",
	],
]);
var_dump(server::isMobile()) . "<br>";

echo gum::randomCode() . "<br>";

var_dump(check::url("http://110.com"));