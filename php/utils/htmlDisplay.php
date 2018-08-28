<?php
require_once __DIR__ . "/../config.php";

function includeHead() {
    include DIR_HTML . "head_common.html";
}

function includeAjax() {
    include DIR_HTML . "head_ajax.html";
}

function includeNavigationMenu(){
    include DIR_LAYOUT . "navigation_menu.php";
}
/*
function importFooter() {
    require DIR_HTML . "footer_common.html";
}*/
?>
