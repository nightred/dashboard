<?php

// Base Functions
require 'config.php';

$prov = $_GET["prov"];
$mod = $_GET["mod"];

if (!$prov) die("No data provider requested");
if (!$mod) die("No data provider module requested");

$uri = "content/providers/{$prov}/{$prov}.php";

// Load the data provider
require "{$uri}";


?>
