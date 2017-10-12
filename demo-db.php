<?php
require "./core/gum.php";

gum::init();

$db = new db("mysql", "qiki");

echo $db->export();