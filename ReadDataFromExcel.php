<?php

require_once 'PHPExcel/Classes/PHPExcel.php';
include 'PHPExcel/Classes/PHPExcel/IOFactory.php';

class ReadDataFromExcel
{
	private $inputFileName;
	private $inputFileType;
	private $objReader;
	private $objPHPExcel;
	private $objWorksheet;
	private $highestRow;
	private $highestColumn;
	private $data = array();

	private $ok = true;

	function __construct($filename)
	{
		$this->inputFileName = $filename;
		$this->inputFileType = PHPExcel_IOFactory::identify($this->inputFileName);
		$this->objReader = PHPExcel_IOFactory::createReader($this->inputFileType);
		$this->objReader->setReadDataOnly(true);
		$this->objPHPExcel = $this->objReader->load($this->inputFileName);

		$this->objWorksheet = $this->objPHPExcel->setActiveSheetIndex(0);
		$this->highestRow = $this->objWorksheet->getHighestRow();
		$this->highestColumn = $this->objWorksheet->getHighestColumn();

		$this->readData();
	}

	private function readData()
	{
		for ($row = 1; $row <= $this->highestRow; ++$row) {
			$dataRow = $this->objWorksheet->rangeToArray('A'.$row.':'.$this->highestColumn.$row, null, true, true, true);

			error_reporting(0);
			
			$a = $dataRow[$row]['A'];
			$b = $dataRow[$row]['B'];
			$c = $dataRow[$row]['C'];

			if ($row == 1 && $a == '' && $b == '' && $c == '') {
				$this->ok = false;
				break;
			}

			if($a == '' && $b == '' && $c == '')
				break;

			if(strlen($c) > 3 || $b == ''){
				$this->ok = false;
				break;
			}
			
			$this->data[$row-1][0] = $a;
			$this->data[$row-1][1] = $b;
			$this->data[$row-1][2] = $c;
		}
	}

	public function getOk(){
		return $this->ok;
	}

	public function getData()
	{
		return $this->data;
	}
}

?>
