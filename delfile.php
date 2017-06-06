<?php

if (isset($_REQUEST['name'])) {
    $name = $_REQUEST['name'];
    $excel = "files/".$name.".xlsx";
    $json = "files/".$name.".json";
    if (file_exists($excel)) unlink($excel);
    if (file_exists($json)) unlink($json);
}

?>