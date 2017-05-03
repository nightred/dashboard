<?php

$BASE_DIR = __DIR__;
$DOC_ROOT = ($_SERVER["DOCUMENT_ROOT"]);
$BASE_URL = str_replace('\\', '/', substr($BASE_DIR, strlen($DOC_ROOT)));

// Base Functions
require "/includes/functions.php";

?>
