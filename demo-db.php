<?php
require "./core/gum.php";

gum::init();

$db = new db("ai");

$rows = $db->rows("SELECT * FROM db_person");

print_r($rows);