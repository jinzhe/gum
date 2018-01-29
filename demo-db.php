<?php
require "./core/gum.php";

gum::init();

$db = new db("ai");
echo $db->export();

$rows = $db->rows("SELECT * FROM db_person");

print_r($rows);