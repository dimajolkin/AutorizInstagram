<?php
include "lib/simple_html_dom.php";

require_once "SplClassLoader.php";

$loader = new SplClassLoader( 'AutorizInst', '/' );
$loader->register();