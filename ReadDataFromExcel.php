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

			if ($dataRow[$row]['A'] == '') {
				break;
			}
			$this->data[$row-1][0] = $dataRow[$row]['A'];
			$this->data[$row-1][1] = $dataRow[$row]['B'];
			$this->data[$row-1][2] = $dataRow[$row]['C'];
		}
	}

	public function getData()
	{
		return $this->data;
	}
}

?>
