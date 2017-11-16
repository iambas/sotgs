<?php
	
$dir = "upload/";

if(isset($_FILES['filepoint'])){
	// if file ok
	if($_FILES['filepoint']['error'] == UPLOAD_ERR_OK){
		// move file upload to directory 'upload'
		move_uploaded_file($_FILES["filepoint"]["tmp_name"], $dir.$_FILES["filepoint"]["name"]);
		$filename = $dir.$_FILES['filepoint']['name'];

		// get data
		$place = $_REQUEST['place'];
		
		$school = '';
		$institutes = '';
		$credit = 1;
		$attendance = 0;
		$midterm = 0;
		$assignment = 0;
		$report = 0;
		$final = 0;
		$total = 0;
		$level = '';

		// มทส.
		if ($place == 'sut') {
			$term = $_REQUEST['term'];
			$year = $_REQUEST['year'];
			$institutes = $_REQUEST['institutes'];
			$school = $_REQUEST['school'];
			$credit = $_REQUEST['credit'];
			$subId = $_REQUEST['subId'];
			$subjects = $_REQUEST['subjects'];
			$instructor = $_REQUEST['instructor'];
			$attendance = $_REQUEST['attendance'];
			$midterm = $_REQUEST['midterm'];
			$assignment = $_REQUEST['assignment'];
			$report = $_REQUEST['report'];
			$final = $_REQUEST['final'];
			$total = $_REQUEST['total'];

			switch($institutes) {
				case '1': $institutes = "Science"; break;
				case '2': $institutes = "Social Technology"; break;
				case '3': $institutes = "Agricultural Technology"; break;
				case '4': $institutes = "Engineering"; break;
				case '5': $institutes = "Medicine"; break;
				case '6': $institutes = "Nursing"; break;
				case '7': $institutes = "Dentistry"; break;
				default : $institutes = "Other"; break;
			}

			if ($school == "Other"){
				$school = $_REQUEST['oschool'];
			}
		}else{
			$term = $_REQUEST['term2'];
			$year = $_REQUEST['year2'];
			$level = $_REQUEST['level'];
			$subId = $_REQUEST['subId2'];
			$subjects = $_REQUEST['subjects2'];
			$instructor = $_REQUEST['instructor2'];
		}

		// Cal grade type
		$ctype = $_REQUEST['ctype'];
		// Grade Type
		$gtype = $_REQUEST['gtype'];
		$arrGtype = explode(", ", $gtype);

		$gl = sizeof($arrGtype);
		if ($place == 'sut'){
			$len = sizeof($arrGtype) + 7;
			array_push($arrGtype, "i:I");
			array_push($arrGtype, "m:M");
			array_push($arrGtype, "p:P");
			array_push($arrGtype, "s:S");
			array_push($arrGtype, "u:U");
			array_push($arrGtype, "w:W");
			array_push($arrGtype, "x:X");
		}else{
			$len = sizeof($arrGtype);
		}
		
		$type = $arrGtype[0];
		$point = array();
		getPoint();
		if(!getReadData()){
			echo "<center><h1>ข้อมูลในไฟล์ excel ไม่ถูกต้อง<br>กรุณาตรวจสอบแล้วลองอีกครั้ง</h1></center>";
			unlink($filename);
			exit();
		}
		findClassGpaAndColor();
		setDataJson();
		getStringRandom();
		writeJsonFile();
		writeExcelFile();

		header("location: result.html");
		unlink($filename);
	}
	exit();
}else{
	exit();
}

function getPoint()
{
	// check is not T-Score then get point from each grade (A, B, C, ...)
	global $ctype, $type, $point;
	if ($ctype == 'myscore'){
		if ($type == "eng8" or $type == "digit8") {
			$point[0] = $_REQUEST['one'];
			$point[1] = $_REQUEST['two'];
			$point[2] = $_REQUEST['three'];
			$point[3] = $_REQUEST['four'];
			$point[4] = $_REQUEST['five'];
			$point[5] = $_REQUEST['six'];
			$point[6] = $_REQUEST['seven'];
			$point[7] = 0;
		}elseif ($type == "eng5" or $type == "digit5") {
			$point[0] = $_REQUEST['one'];
			$point[1] = $_REQUEST['two'];
			$point[2] = $_REQUEST['three'];
			$point[3] = $_REQUEST['four'];
			$point[4] = 0;
		}elseif ($type == "eng6") {
			$point[0] = $_REQUEST['one'];
			$point[1] = $_REQUEST['two'];
			$point[2] = $_REQUEST['three'];
			$point[3] = $_REQUEST['four'];
			$point[4] = $_REQUEST['five'];
			$point[5] = 0;
		}
	}
}

function test(){
	global $dataSort;
	echo $dataSort;
}

function getReadData()
{
	global $data, $n, $dataSort, $cg, $grade, $numGrade, $filename;
	global $freq, $cumFreq, $percentile, $Tscore, $scoreFreq, $ctype, $type, $point;

	// include class
	include 'ReadDataFromExcel.php';
	include 'QuickSort.php';
	include 'CalGrade.php';

	// read data from file
  	$excel = new ReadDataFromExcel($filename);
	if(!$excel->getOk()) return false;
	$data = $excel->getData();
	$n = count($data);

	// sort data with score max to min
	$qsort = new QuickSort($data);
	$qsort->sort();
	$dataSort = $qsort->getDataSort();

	// calculate mean, sd, find grade (A, B, C, ...), ...
	$cg = new CalGrade($dataSort, $ctype, $type, $point);
	$grade = $cg->getGrade();
	$numGrade = $cg->getNumGrade();
	$freq = $cg->getFreq();
	$scoreFreq = $cg->getScoreFreq();

	// if T-Score
	if ($ctype == 'tscore'){
		$cumFreq = $cg->getCumFreq();
		$percentile = $cg->getPercentile();
		$Tscore = $cg->getTscore();
	}

	return true;
}

function findClassGpaAndColor()
{
	global $type, $classGPA, $color, $numGrade, $n;
	if ($type == "eng8" or $type == "digit8") {
		$classGPA = $numGrade[0]*4 + $numGrade[1]*3.5 + $numGrade[2]*3 +
								$numGrade[3]*2.5 + $numGrade[4]*2 + $numGrade[5]*1.5 + $numGrade[6];
		$color = array("#2ecc71","#3498db","#95a5a6","#9b59b6","#34495e","#333333","#f1c40f","#e74c3c");
	}elseif ($type == "eng5" or $type == "digit5") {
		$classGPA = $numGrade[0]*4 + $numGrade[1]*3 + $numGrade[2]*2 + $numGrade[3];
		$color = array("#2ecc71","#3498db","#95a5a6","#9b59b6","#e74c3c");
	}elseif ($type == "eng6") {
		$classGPA = $numGrade[0]*4 + $numGrade[1]*3.5 + $numGrade[2]*3 + $numGrade[3]*2.5 + $numGrade[4]*2;
		$color = array("#2ecc71","#3498db","#95a5a6","#9b59b6","#34495e","#e74c3c");
	}
	array_push($color, "#E91E63");
	array_push($color, "#3F51B5");
	array_push($color, "#009688");
	array_push($color, "#CDDC39");
	array_push($color, "#FF9800");
	array_push($color, "#795548");
	array_push($color, "#000000");
	$classGPA = sprintf("%.3f", $classGPA / floatval($n));
}

function setDataJson()
{
	// create data json to display
	global $txtJson, $place, $term, $year, $school, $institutes, $subjects, $instructor;
	global $n, $dataSort, $cg, $classGPA, $len, $arrGtype, $numGrade, $grade;
	global $freq, $cumFreq, $percentile, $Tscore, $scoreFreq;
	global $color, $ctype, $gl, $point;

	global $credit, $attendance, $midterm, $assignment, $report, $final, $total, $level, $subId;

	$txtJson = "{";
	$txtJson .= '"place":"'.$place.'", ';
	$txtJson .= '"term":"'.$term.'/'.$year.'", ';
	$txtJson .= '"ty":"'.$term.'-'.$year.'", ';
	$txtJson .= '"institutes":"'.$institutes.'", ';
	$txtJson .= '"school":"'.$school.'", ';
	$txtJson .= '"credit":'.$credit.', ';
	$txtJson .= '"attendance":'.$attendance.', ';
	$txtJson .= '"midterm":'.$midterm.', ';
	$txtJson .= '"assignment":'.$assignment.', ';
	$txtJson .= '"report":'.$report.', ';
	$txtJson .= '"final":'.$final.', ';
	$txtJson .= '"total":'.$total.', ';
	$txtJson .= '"level":"'.$level.'", ';
	$txtJson .= '"subId":"'.$subId.'", ';
	$txtJson .= '"subjects":"'.$subjects.'", ';
	$txtJson .= '"instructor":"'.$instructor.'", ';

	$txtJson .= '"student": '.$n.', "max":'.$dataSort[0][2].', "min":'.$cg->getMin().', ';
	$txtJson .= '"mean":'.sprintf("%.2f", $cg->getMean()).', "sd":'.sprintf("%.2f", $cg->getSD()).', ';
	$txtJson .= '"classGPA":'.sprintf("%.2f", $classGPA).', ';

	$txtJson .= '"numGrade":[';
	for ($i = 0; $i < $len-1; $i++) {
		$txtJson .= '"'.$numGrade[$i].'"';
		$txtJson .= ($i != $len-2) ? ', ' : '], ';
	}

	$txtJson .= '"grade":[';
	for ($i = 1; $i < $len; $i++) {
		$arr = explode(":", $arrGtype[$i]);
		$txtJson .= '"'.$arr[1].'"';
		$txtJson .= ($i != $len-1) ? ', ' : '], ';
	}

	$txtJson .= '"gPercen":[';
	for ($i = 0; $i < $len-1; $i++) {
		$txtJson .= '"'.sprintf("%.2f", ($numGrade[$i] * 100 / $n)).'"';
		$txtJson .= ($i != $len-2) ? ', ' : '], ';
	}

	$txtJson .= '"color":[';
	for ($i = 0; $i < count($color); $i++) {
		$txtJson .= '"'.$color[$i].'"';
		$txtJson .= ($i != count($color)-1) ? ', ' : '], ';
	}

	$z = $cg->getZ();
	$out = '';
	if ($ctype == 'tscore') {
		$mx = $cg->getMx();
		$mn = $cg->getMn();
		$mx[0] = 100;
		$mn[$gl-2] = 0;
		$txtJson .= '"range":[';
		for ($i = 0; $i < $len-1; $i++) {
			if(isset($mx[$i])){
				$txtJson .= '"'.$mn[$i].'-'.$mx[$i].'"';
				$txtJson .= ($i == $len-2)? '], ' : ', ';	
			}else{	
				$txtJson .= ($i == $len-2)? '"-"], ' : '"-", ';
			}
		}

		$nf = count($freq);
		$out = '';
		for($i = 0; $i < $nf; $i++){
			if ($out != "") { $out .= ","; }
			$out .=  '{"scoreFreq":"'.$scoreFreq[$i].'", ';
			$out .=  '"freq":"'.$freq[$i].'", ';
			$out .=  '"cumFreq":"'.$cumFreq[$i].'", ';
			$out .= '"cf5f":"'.sprintf("%.2f", $cumFreq[$i] - 0.5 * $freq[$i]).'", ';
			$out .=  '"percentile":"'.sprintf("%.2f", $percentile[$i] * 100).'", ';
			$out .=  '"tscore":"'.sprintf("%.0f", $Tscore[$i]).'"}';
		}
		$txtJson .= '"ctype": "tscore", "showCal":['.$out.'], ';
	}else{
		$txtJson .= '"range":[';
		for ($i = 0; $i < $len-1; $i++) {
			if(isset($point[$i])){
				if($i == 0)
					$txtJson .= '"'.$point[$i].'-100",';
				elseif($i == $len-2)
					$txtJson .= '"0-'.($point[$i-1] - 1).'"],';
				else
					$txtJson .= '"'.$point[$i].'-'.($point[$i-1] - 1).'",';
			}else{
				$txtJson .= ($i == $len-2)? '"-"], ' : '"-", ';
			}
		}

		$nf = count($freq);
		$out = '';
		for($i = 0; $i < $nf; $i++){
			if ($out != "") { $out .= ","; }
			$out .=  '{"scoreFreq":"'.$scoreFreq[$i].'", ';
			$out .=  '"freq":"'.$freq[$i].'"}';
		}
		$txtJson .= '"ctype": "myscore", "showCal":['.$out.'], ';
	}
	$out = '';
	for ($i = 0; $i < $n; $i++) {
		$sc = $dataSort[$i][2];
		if(ctype_alpha($dataSort[$i][2]))
			$sc = "-";
		if ($out != "") { $out .= ","; }
		$out .= '{"id":"'.$dataSort[$i][0].'", ';
		$out .= '"name":"'.$dataSort[$i][1].'", ';
		$out .=  '"score":"'.$sc.'", ';
		$out .= '"grade":"'.$grade[$i].'"}';
	}
	$txtJson .= '"records":['.$out.']';
	$txtJson .= "}";
}

function random($n)
{
	$ran = '';
	while($n-- > 0){
		$alpha = str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz");
		$ran .= $alpha[0];
	}
	return $ran;
}

function getStringRandom()
{
	global $str, $subId, $txtJson, $json, $term, $year;
	$ran = random(32);
	$str = $subId."-".$term."-".$year."-".$ran;
	$json = $str.".json";
	setcookie("id", $str);
}

function writeJsonFile()
{
	global $txtJson, $json;
	$myfile = fopen("files/".$json, "w") or die("Unable to open file!");
	fwrite($myfile, $txtJson);
}

function writeExcelFile()
{
	// set data to write
	global $dataSort, $str, $grade;
	$json = "files/".$str.".json";
	include 'WriteDataToExcel.php';
	$write = new WriteDataToExcel($str, $dataSort);
	$write->setGrade($grade);
	$write->readJson($json);
    $write->writeData("first");
}

?>
