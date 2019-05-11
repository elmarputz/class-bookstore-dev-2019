<?php
require_once('inc/bootstrap.php');

$default_view = 'welcome';
$view = $default_view;




require_once('views/' . $view . '.php');