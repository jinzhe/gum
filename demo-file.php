<?php
require "./core/gum.php";

gum::init();

function tree($directory) {
	$mydir = dir($directory);
	$list = [];
	while ($file = $mydir->read()) {
		if ((is_dir("$directory/$file")) AND ($file != ".") AND ($file != "..")) {

		} else {
			$list[] = $file;
		}

	}
	$mydir->close();
	echo json_encode($list);
}

tree("/Users/zee/Movies/comopop/song/");