<?php

class CalGrade
{
	private $DEFAULT_TOLERANCE = 1.0e-7;
	private $freq;
	private $cumFreq;
	private $percentile;
	private $Tscore;

	// recieve data
	private $data;
	private $type;
	private $point;

	private $Mean;
	private $SD;	// Standard Deviation
	private $Range;
	private $grade = array();
	private $numGrade;
	private $scoreFreq = array();

	private $numData;
	private $sum = 0;
	private $sumX2 = 0;
	private $min = 100;

	private $mx;
	private $mn;
	private $z = array();
	private $sGrade = 0;

	function __construct($data, $ctype, $type, $point)
	{
		$this->mx = array_fill(0, 8, 0);
		$this->mn = array_fill(0, 8, 101);
		$this->numGrade = array_fill(0, 15, 0);

		$this->data = $data;
		$this->type = $type;
		$this->point = $point;

		$this->numData = count($this->data);
		$this->Mean = $this->calMean();
		$this->SD = $this->calSD();
		$this->freq = $this->findFrequency();

		if ($ctype == 'tscore')
			$this->calGradeTscore();
		else
			$this->calGradeSelf();
	}

	private function calMean()
	{
		for ($i = 0; $i < $this->numData; $i++)
		{
			if(!ctype_alpha($this->data[$i][2])){
				$this->sum += $this->data[$i][2];
				$this->sumX2 += $this->data[$i][2] * $this->data[$i][2];
				$this->min = $this->min < $this->data[$i][2] ? $this->min : $this->data[$i][2];
			}	
		}
		return $this->sum / $this->numData;
	}

	private function calSD()
	{
		return sqrt((($this->numData*$this->sumX2) - ($this->sum*$this->sum)) / ($this->numData*($this->numData-1)));
	}

	private function sgrade($s, $ix)
	{
		$s = strtoupper($s);
		if($s == 'I'){
			$g = 'I';
			$this->numGrade[$ix]++;
		}elseif($s == 'M'){
			$g = 'M';
			$this->numGrade[$ix+1]++;
		}elseif($s == 'P'){
			$g = 'P';
			$this->numGrade[$ix+2]++;
		}elseif($s == 'S'){
			$g = 'S';
			$this->numGrade[$ix+3]++;
		}elseif($s == 'U'){
			$g = 'U';
			$this->numGrade[$ix+4]++;
		}elseif($s == 'W'){
			$g = 'W';
			$this->numGrade[$ix+5]++;
		}elseif($s == 'X'){
			$g = 'X';
			$this->numGrade[$ix+6]++;
		}elseif($s == 'F'){
			$g = 'F';
			$this->setNumMxMn($ix-1, 0);
		}
		return $g;
	}

	private function gradeEng8($s, $p, $d)
	{
		if(ctype_alpha($s)){
			return $this->sgrade($s, 8);
		}

		if ($s >= $p[0]) {
			$g = 'A';
			$this->setNumMxMn(0, $d);
		}elseif ($s >= $p[1]) {
			$g = 'B+';
			$this->setNumMxMn(1, $d);
		}elseif ($s >= $p[2]) {
			$g = 'B';
			$this->setNumMxMn(2, $d);
		}elseif ($s >= $p[3]) {
			$g = 'C+';
			$this->setNumMxMn(3, $d);
		}elseif ($s >= $p[4]) {
			$g = 'C';
			$this->setNumMxMn(4, $d);
		}elseif ($s >= $p[5]) {
			$g = 'D+';
			$this->setNumMxMn(5, $d);
		}elseif ($s >= $p[6]) {
			$g = 'D';
			$this->setNumMxMn(6, $d);
		}else{
			$g = 'F';
			$this->setNumMxMn(7, $d);
		}

		return $g;
	}

	private function gradeEng6($s, $p, $d)
	{
		if(ctype_alpha($s)){
			return $this->sgrade($s, 6);
		}
		
		if ($s >= $p[0]) {
			$g = 'A';
			$this->setNumMxMn(0, $d);
		}elseif ($s >= $p[1]) {
			$g = 'B+';
			$this->setNumMxMn(1, $d);
		}elseif ($s >= $p[2]) {
			$g = 'B';
			$this->setNumMxMn(2, $d);
		}elseif ($s >= $p[3]) {
			$g = 'C+';
			$this->setNumMxMn(3, $d);
		}elseif ($s >= $p[4]) {
			$g = 'C';
			$this->setNumMxMn(4, $d);
		}else {
			$g = 'F';
			$this->setNumMxMn(5, $d);
		}

		return $g;
	}

	private function gradeEng5($s, $p, $d)
	{
		if(ctype_alpha($s)){
			return $this->sgrade($s, 5);
		}
		
		if ($s >= $p[0]) {
			$g = 'A';
			$this->setNumMxMn(0, $d);
		}elseif ($s >= $p[1]) {
			$g = 'B';
			$this->setNumMxMn(1, $d);
		}elseif ($s >= $p[2]) {
			$g = 'C';
			$this->setNumMxMn(2, $d);
		}elseif ($s >= $p[3]) {
			$g = 'D';
			$this->setNumMxMn(3, $d);
		}else {
			$g = 'F';
			$this->setNumMxMn(4, $d);
		}

		return $g;
	}

	private function gradeDigit8($s, $p, $d)
	{
		if(ctype_alpha($s)){
			return $this->sgrade($s, 8);
		}
		
		if ($s >= $p[0]) {
			$g = '4';
			$this->setNumMxMn(0, $d);
		}elseif ($s >= $p[1]) {
			$g = '3.5';
			$this->setNumMxMn(1, $d);
		}elseif ($s >= $p[2]) {
			$g = '3';
			$this->setNumMxMn(2, $d);
		}elseif ($s >= $p[3]) {
			$g = '2.5';
			$this->setNumMxMn(3, $d);
		}elseif ($s >= $p[4]) {
			$g = '2';
			$this->setNumMxMn(4, $d);
		}elseif ($s >= $p[5]) {
			$g = '1.5';
			$this->setNumMxMn(5, $d);
		}elseif ($s >= $p[6]) {
			$g = '1';
			$this->setNumMxMn(6, $d);
		}else{
			$g = '0';
			$this->setNumMxMn(7, $d);
		}

		return $g;
	}

	private function gradeDigit5($s, $p, $d)
	{
		if(ctype_alpha($s)){
			return $this->sgrade($s, 5);
		}
		
		if ($s >= $p[0]) {
			$g = '4';
			$this->setNumMxMn(0, $d);
		}elseif ($s >= $p[1]) {
			$g = '3';
			$this->setNumMxMn(1, $d);
		}elseif ($s >= $p[2]) {
			$g = '2';
			$this->setNumMxMn(2, $d);
		}elseif ($s >= $p[3]) {
			$g = '1';
			$this->setNumMxMn(3, $d);
		}else {
			$g = '0';
			$this->setNumMxMn(4, $d);
		}

		return $g;
	}

	private function setNumMxMn($ix, $d)
	{
		$this->numGrade[$ix]++;
		$this->mx[$ix] = $d > $this->mx[$ix] ? $d : $this->mx[$ix];
		$this->mn[$ix] = $d < $this->mn[$ix] ? $d : $this->mn[$ix];
	}

	private function calGradeSelf()
	{
		$type = $this->type;
		if ($type == "eng8") {
			for($i = 0; $i < $this->numData; $i++)
				$this->grade[$i] = $this->gradeEng8($this->data[$i][2], $this->point, $this->data[$i][2]);
		}elseif ($type == "eng6") {
			for($i = 0; $i < $this->numData; $i++)
				$this->grade[$i] = $this->gradeEng6($this->data[$i][2], $this->point, $this->data[$i][2]);
		}elseif ($type == "eng5") {
			for($i = 0; $i < $this->numData; $i++)
				$this->grade[$i] = $this->gradeEng5($this->data[$i][2], $this->point, $this->data[$i][2]);
		}elseif ($type == "digit8") {
			for($i = 0; $i < $this->numData; $i++)
				$this->grade[$i] = $this->gradeDigit8($this->data[$i][2], $this->point, $this->data[$i][2]);
		}elseif ($type == "digit5") {
			for($i = 0; $i < $this->numData; $i++)
				$this->grade[$i] = $this->gradeDigit5($this->data[$i][2], $this->point, $this->data[$i][2]);
		}
	}

	private function calGradeTscore()
	{
		$this->cumFreq = $this->findCumulativeFrequency($this->freq);
		$this->percentile = $this->findPercentile($this->freq, $this->cumFreq);
		$this->Tscore = $this->findTscore($this->percentile, count($this->freq));
		$this->findGradeTscore($this->Tscore, $this->freq);
	}

	private function findFrequency()
	{
		$nFreq = 0;
		$freq = array();
		$temp = $this->data;
		$dummy = array();

		for($i = 0; $i < $this->numData; $i++){
			if(ctype_alpha($this->data[$i][2])) continue;
			if ($temp[$i][2] != -1) {
				$this->scoreFreq[$nFreq] = $temp[$i][2];
				$freq[$nFreq] = 1;
				$dummy[$nFreq] = $temp[$i][2];
				$j = $i;
				$n = $this->numData - 1;
				while (($j < $n) && ($temp[$j+1][2] == $temp[$i][2])) {
					$freq[$nFreq]++;
					$temp[$j+1][2] = -1;
					$j++;
				}
				$nFreq++;
			}
		}

		return $freq;
	}

	private function findCumulativeFrequency($freq)
	{
		$cumFreq = array();
		$n = count($freq) - 1;
		$cumFreq[$n] = $freq[$n];
		for($i = $n-1; $i >= 0; $i--){
			$cumFreq[$i] = $freq[$i] + $cumFreq[$i+1];
		}

		return $cumFreq;
	}

	private function findPercentile($freq, $cumFreq)
	{
		$percentile = array();
		$n = count($freq) - 1;
		$percentile[$n] = 0.5 * $freq[$n] / $this->numData;
		for($i = $n-1; $i >= 0; $i--){
			$percentile[$i] = ($cumFreq[$i+1] + 0.5 * $freq[$i]) / $this->numData;
		}

		return $percentile;
	}

	private function findTscore($percentile, $n)
	{
		$Tscore = array();
		try {
			for($i = 0; $i < $n; $i++){
				//$z = $this->findZAtKnownArea($percentile[$i]); // formular
				//$z = $this->findZ($percentile[$i]); // z table
				$z = $this->NormSInv($percentile[$i]); // function in excel
				$Tscore[$i] = $z * 10 + 50;
				$this->z[$i] = $z;
			}
		} catch (Exception $e) {
			echo "Failed : ".$e->getMessage();
		}

		return $Tscore;
	}

	private function findZ($area)
	{
		$isAreaGreaterThanHalf = false;
		if ($area > 0.5){
			$newArea = $area - 0.5;
			$isAreaGreaterThanHalf = true;
		} else {
			$newArea = 0.5 - $area;
		}
		$newArea = sprintf("%.4f", $newArea);
		$z = 0;
		include 'ztable.php';
		for($i = 0; $i < 35; $i++){
			for($j = 0; $j < 10; $j++){
				$ztable[$i][$j] = sprintf("%.4f", $ztable[$i][$j]);
				if (abs($newArea - $ztable[$i][$j]) == 0) {
					$z = ($i/10.0) + ($j/100.0);
					if (abs($newArea - $ztable[$i][$j+1]) == 0) {
						$z += 0.005;
					}

					return $isAreaGreaterThanHalf? $z : -$z;
				}elseif ($newArea < $ztable[$i][$j]) {
					if ($j-1 == -1) {
						$z = ($ztable[$i][$j] - $newArea) < ($ztable[$i-1][9] - $newArea) ? ($i/10.0) + ($j/100.0) : (($i-1)/10.0) + 0.09;
					}else{
						$z = ($ztable[$i][$j] - $newArea) < ($ztable[$i][$j-1] - $newArea) ? ($i/10.0) + ($j/100.0) : ($i/10.0) + ($j-1)/100.0;
					}
					return $isAreaGreaterThanHalf? $z : -$z;
				}
			}
		}

		return $isAreaGreaterThanHalf? $z : -$z;
	}

	private function findZAtKnownArea($area)
	{
		$isAreaGreaterThanHalf = false;
		$lowerLimit = 0;
		$newArea = 0;
		$deltaX;
		$z = 0;
		$sumArea = 0;
		$dA;

		if ($area < 0 || $area > 1) {
			throw new Exception("AREA UNDER NORMAL CURVE MUST BE BETWEEN 0 to 1.", 1);
		}

		if ($area > 0.5){
			$newArea = $area - 0.5;
			$isAreaGreaterThanHalf = true;
		} else {
			$newArea = 0.5 - $area;
		}

		if ($newArea >= 0.49951657585762) {
			$lowerLimit = 3.3;
			$sumArea = 0.49951657585762;
		}elseif ($newArea >= 0.49931286206208) {
			$lowerLimit = 3.2;
			$sumArea = 0.49931286206208;
		}elseif ($newArea >=0.49903239678678 ) {
			$lowerLimit = 3.1;
			$sumArea = 0.49903239678678;
		}elseif ($newArea >= 0.49865010196837) {
			$lowerLimit = 3;
			$sumArea = 0.49865010196837;
		}elseif ($newArea >=0.49813418669962) {
			$lowerLimit = 2.9;
			$sumArea =0.49813418669962;
		}elseif ($newArea >=0.49744486966957) {
			$lowerLimit = 2.8;
			$sumArea =0.49744486966957;
		}elseif ($newArea >=0.49653302619696) {
			$lowerLimit = 2.7;
			$sumArea = 0.49653302619696;
		}elseif ($newArea >= 0.49379033467422) {
			$lowerLimit = 2.5;
			$sumArea = 0.49379033467422;
		}elseif ($newArea >= 0.49180246407540) {
			$lowerLimit = 2.4;
			$sumArea = 0.49180246407540;
		}elseif ($newArea >= 0.48927588997832) {
			$lowerLimit = 2.3;
			$sumArea = 0.48927588997832;
		}elseif ($newArea >= 0.48609655248650) {
			$lowerLimit = 2.2;
			$sumArea = 0.48609655248650;
		}elseif ($newArea >= 0.47724986805182) {
			$lowerLimit = 2;
			$sumArea = 0.47724986805182;
		}elseif ($newArea >= 0.43319279873114) {
			$lowerLimit = 1.5;
			$sumArea = 0.43319279873114;
		}elseif ($newArea >= 0.34134474606854 ) {
			$lowerLimit = 1.0;
			$sumArea = 0.34134474606854;
		}elseif ($newArea >= 0.19146246127401) {
			$lowerLimit = 0.5;
			$sumArea = 0.19146246127401;
		}elseif ($newArea >= 0.09870632568292) {
			$lowerLimit = 0.25;
			$sumArea = 0.09870632568292;
		}elseif ($newArea >= 0.01993880583837) {
			$lowerLimit = 0.05;
			$sumArea = 0.01993880583837;
		}

		$z = $lowerLimit;
		$deltaX = 0.00001;
		while($newArea - $sumArea > $this->DEFAULT_TOLERANCE) {
			$dA = (1/sqrt(2*pi()))*0.5*$deltaX*(exp(-0.5*$z*$z) + exp(-0.5*($z+$deltaX)*($z+$deltaX)));
			$sumArea += $dA;
			$z += $deltaX;
		}

		return $isAreaGreaterThanHalf? $z+$deltaX : -($z+$deltaX);
	}

	private	function NormSInv($probability) {
  		$a1 = -39.6968302866538;
		$a2 = 220.946098424521;
		$a3 = -275.928510446969;
		$a4 = 138.357751867269;
		$a5 = -30.6647980661472;
		$a6 = 2.50662827745924;

		$b1 = -54.4760987982241;
		$b2 = 161.585836858041;
		$b3 = -155.698979859887;
		$b4 = 66.8013118877197;
		$b5 = -13.2806815528857;

		$c1 = -7.78489400243029E-03;
		$c2 = -0.322396458041136;
		$c3 = -2.40075827716184;
		$c4 = -2.54973253934373;
		$c5 = 4.37466414146497;
		$c6 = 2.93816398269878;

		$d1 = 7.78469570904146E-03;
		$d2 = 0.32246712907004;
		$d3 = 2.445134137143;
		$d4 =  3.75440866190742;
		
		$p_low = 0.02425;
		$p_high = 1 - $p_low;
		$q = 0;
		$r = 0;
		$normSInv = 0;
		if ($probability < 0 || $probability > 1) {
			throw new \Exception("normSInv: Argument out of range.");
		} elseif ($probability < $p_low) {
			$q = sqrt(-2 * log($probability));
			$normSInv = ((((($c1 * $q + $c2) * $q + $c3) * $q + $c4) * $q + $c5) * $q + $c6) / (((($d1 * $q + $d2) * $q + $d3) * $q + $d4) * $q + 1);
		} elseif ($probability <= $p_high) {
			$q = $probability - 0.5;
			$r = $q * $q;
			$normSInv = ((((($a1 * $r + $a2) * $r + $a3) * $r + $a4) * $r + $a5) * $r + $a6) * $q / ((((($b1 * $r + $b2) * $r + $b3) * $r + $b4) * $r + $b5) * $r + 1);
		} else {
			$q = sqrt(-2 * log(1 - $probability));
			$normSInv = -((((($c1 * $q + $c2) * $q + $c3) * $q + $c4) * $q + $c5) * $q + $c6) /(((($d1 * $q + $d2) * $q + $d3) * $q + $d4) * $q + 1);
  		}
		return $normSInv;
	}

	private function findGradeTscore($Tscore, $freq)
	{
		$t = $this->type;
		$n = count($Tscore);
		if ($t == 'eng8') {
			$range = ($Tscore[0] - $Tscore[$n-1]) / 8.0;
			if ($range < 0)   $range = -$range;
			$p = array();
			for($i = 0; $i < 7; $i++){
				$p[$i] = $Tscore[0] - ($i+1)*$range;
			}

			$i = 0; $k = 0;
			while($i < $this->numData){
				if (ctype_alpha($this->data[$i][2])){
					$this->grade[$i] = $this->gradeEng8($this->data[$i][2], $p, $this->data[$i][2]);
					$i++;
				}else{
					for($j = 0; $j < $freq[$k]; $j++){
						$this->grade[$i] = $this->gradeEng8($Tscore[$k], $p, $this->data[$i][2]);
						$i++;
					}
					$k++;
				}
			}
		}elseif ($t == 'eng6') {
			$range = ($Tscore[0] - $Tscore[$n-1]) / 6.0;
			if ($range < 0)   $range = -$range;
			$p = array();
			for($i = 0; $i < 7; $i++){
				$p[$i] = $Tscore[0] - ($i+1)*$range;
			}

			$i = 0; $k = 0;
			while($i < $this->numData){
				if (ctype_alpha($this->data[$i][2])){
					$this->grade[$i] = $this->gradeEng6($this->data[$i][2], $p, $this->data[$i][2]);
					$i++;
				}else{
					for($j = 0; $j < $freq[$k]; $j++){
						$this->grade[$i] = $this->gradeEng6($Tscore[$k], $p, $this->data[$i][2]);
						$i++;
					}
					$k++;
				}
			}
		}elseif ($t == 'eng5') {
			$range = ($Tscore[0] - $Tscore[$n-1]) / 5.0;
			if ($range < 0)   $range = -$range;
			$p = array();
			for($i = 0; $i < 7; $i++){
				$p[$i] = $Tscore[0] - ($i+1)*$range;
			}

			$i = 0; $k = 0;
			while($i < $this->numData){
				if (ctype_alpha($this->data[$i][2])){
					$this->grade[$i] = $this->gradeEng5($this->data[$i][2], $p, $this->data[$i][2]);
					$i++;
				}else{
					for($j = 0; $j < $freq[$k]; $j++){
						$this->grade[$i] = $this->gradeEng5($Tscore[$k], $p, $this->data[$i][2]);
						$i++;
					}
					$k++;
				}
			}
		}elseif ($t == 'digit8') {
			$range = ($Tscore[0] - $Tscore[$n-1]) / 8.0;
			if ($range < 0)   $range = -$range;
			$p = array();
			for($i = 0; $i < 7; $i++){
				$p[$i] = $Tscore[0] - ($i+1)*$range;
			}

			$i = 0; $k = 0;
			while($i < $this->numData){
				if (ctype_alpha($this->data[$i][2])){
					$this->grade[$i] = $this->gradeDigit8($this->data[$i][2], $p, $this->data[$i][2]);
					$i++;
				}else{
					for($j = 0; $j < $freq[$k]; $j++){
						$this->grade[$i] = $this->gradeDigit8($Tscore[$k], $p, $this->data[$i][2]);
						$i++;
					}
					$k++;
				}
			}
		}elseif ($t == 'digit5') {
			$range = ($Tscore[0] - $Tscore[$n-1]) / 5.0;
			if ($range < 0)   $range = -$range;
			$p = array();
			for($i = 0; $i < 7; $i++){
				$p[$i] = $Tscore[0] - ($i+1)*$range;
			}

			$i = 0; $k = 0;
			while($i < $this->numData){
				if (ctype_alpha($this->data[$i][2])){
					$this->grade[$i] = $this->gradeDigit5($this->data[$i][2], $p, $this->data[$i][2]);
					$i++;
				}else{
					for($j = 0; $j < $freq[$k]; $j++){
						$this->grade[$i] = $this->gradeDigit5($Tscore[$k], $p, $this->data[$i][2]);
						$i++;
					}
					$k++;
				}
			}
		}
	}

	// set get function
	public function setData($data){ $this->data = $data; }
	public function getData(){ return $this->data; }
	public function getMean(){ return $this->Mean; }
	public function getMin(){ return $this->min; }
	public function getSD(){ return $this->SD; }
	public function getGrade(){ return $this->grade; }
	public function getNumGrade(){ return $this->numGrade; }
	public function getFreq(){ return $this->freq; }
	public function getCumFreq(){ return $this->cumFreq; }
	public function getPercentile(){ return $this->percentile; }
	public function getTscore(){ return $this->Tscore; }
	public function getScoreFreq(){ return $this->scoreFreq; }
	public function getMx(){ return $this->mx; }
	public function getMn(){ return $this->mn; }
	public function getZ(){ return $this->z; }
}

?>
