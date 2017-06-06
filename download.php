<?php

if(isset($_REQUEST['col']) and isset($_REQUEST['cnSort']) and isset($_REQUEST['id'])){
    $col = $_REQUEST['col'];
    $cnSort = $_REQUEST['cnSort'];
    $id = $_REQUEST['id'];
    if($id == '' or $col == '' or $cnSort == '')	exit();
    
    $filename = "files/" . $id . ".xlsx";
    $json = "files/".$id.".json";
    if(!file_exists($json))	exit();

    $content = file_get_contents($json);
    $obj = json_decode($content);
    $data = array();
    foreach($obj->records as $k => $v){
        $data[$k][0] = $v->id;
        $data[$k][1] = $v->name;
        $data[$k][2] = $v->score;
        $data[$k][3] = $v->grade;
    }

    require_once 'PHPExcel/Classes/PHPExcel.php';
    include 'PHPExcel/Classes/PHPExcel/IOFactory.php';
    include 'WriteDataToExcel.php';

    if ($cnSort == 0){
        //$write = new WriteDataToExcel($id, $data);
        header("location: $filename");
    }else{
        include 'QuickSort.php';
        $qsort = new QuickSort($data);
        $qsort->sortTable($col, $cnSort);
        $dataSort = $qsort->getDataSort();
        $write = new WriteDataToExcel($id, $dataSort);
    }

    $write->readJson($json);
    $write->writeData("");
    header("location: $filename");
}

exit();

?>
