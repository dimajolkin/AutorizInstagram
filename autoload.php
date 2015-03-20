<?php
require_once "SplClassLoader.php";

$loader = new SplClassLoader( 'src', '/' );
$loader->register();