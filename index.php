<?php
require_once('inc/bootstrap.php');

$default_view = 'welcome';
$view = $default_view;


// switch views based on querystring
if (isset($_REQUEST['view']) && 
    file_exists(__DIR__ . '/views/' . $_REQUEST['view'] . '.php')) {
        $view = $_REQUEST['view'];
    }    




require_once('views/' . $view . '.php');