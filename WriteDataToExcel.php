<?php

class WriteDataToExcel
{
	private $objPHPExcel;
	private $data;
	private $name;
	private $grade;
	private $instructor, $institutes, $school, $subId, $subject, $term, $level;
	private $obj;

	function __construct($name, $data)
	{
		$this->objPHPExcel = new PHPExcel();
		$this->setProperties();
		$this->data = $data;
		$this->name = $name;
	}

	public function setGrade($grade){ $this->grade = $grade; }

	private function setProperties()
	{
		$prop = $this->objPHPExcel->getProperties();
		$prop->setCreator("");
		$prop->setLastModifiedBy("");
		$prop->setTitle("");
		$prop->setSubject("");
		$prop->setDescription("");
	}

	public function readJson($json){
		$content = file_get_contents($json);
    	$this->obj = json_decode($content);
	}

	public function writeData($type)
	{
		$obj = $this->obj;
		$this->sheetInfo();
		$this->sheetGrade($type);

		if($obj->ctype == "tscore")
			$this->sheetCal();
		
		// Write data to excel file
		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
		$fileName = "files/".$this->name.".xlsx";
		$objWriter->save($fileName);
	}

	public function sheetInfo()
	{
		$obj = $this->obj;

		// sheet 1
		$this->objPHPExcel->setActiveSheetIndex(0);
		$act = $this->objPHPExcel->getActiveSheet();
		$act->getDefaultRowDimension()->setRowHeight(20);
		$act->setTitle('Info');
		$act->getStyle('A:J')->getFont()->setName('TH SarabunPSK');
		$act->getStyle('A:J')->getFont()->setSize(16);

		$act->getColumnDimension('J')->setWidth("8");
		// write data
		if($obj->place == "sut"){
			$this->sut($act);
		}else{
			$this->other($act);
		}
	}

	public function sut($act)
	{
		$obj = $this->obj;
		$osx = 0;
		if($obj->institutes == "Engineering"){
			$path = 'assets/logo.png';
		}else{
			$path = 'assets/sut_logo.jpg';
			$osx = 13;
		}
		// Add a drawing to the worksheet
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('logo');
		$objDrawing->setDescription('logo');
		$objDrawing->setPath($path);
		$objDrawing->setHeight(135);
		$objDrawing->setCoordinates('E1');
		$objDrawing->setOffsetX($osx);
		$objDrawing->setWorksheet($this->objPHPExcel->getActiveSheet());

		// normal
		$act->mergeCells('A7:J7');
		$act->mergeCells('A8:J8');
		$act->mergeCells('A9:J9');
		$act->mergeCells('A10:J10');
		$act->mergeCells('A11:J11');

		// statistics
		$act->mergeCells('B13:C13');
		$act->mergeCells('B14:C14');
		$act->mergeCells('H13:I13');
		$act->mergeCells('H14:I14');

		// grade
		$act->mergeCells('D16:E16');
		$act->mergeCells('F16:G16');

		// set value
		
		$act->SetCellValue('A7', 'Institutes of '.$obj->institutes.'  School of '.$obj->school);
		$act->SetCellValue('A8', 'Course Title : '.$obj->subId." ".$obj->subjects);
		$act->SetCellValue('A9', 'Number of Credit : '.$obj->credit);
		$act->SetCellValue('A10', 'Instructor '.$obj->instructor);
		$act->SetCellValue('A11', 'Term / Academic Year : '.$obj->term);

		$act->SetCellValue('B13', 'Number of Student');
		$act->SetCellValue('B14', $obj->student);
		$act->SetCellValue('D13', 'Max. Score');
		$act->SetCellValue('D14', $obj->max);
		$act->SetCellValue('E13', 'Min. Score');
		$act->SetCellValue('E14', $obj->min);
		$act->SetCellValue('F13', 'Mean');
		$act->SetCellValue('F14', $obj->mean);
		$act->SetCellValue('G13', 'SD');
		$act->SetCellValue('G14', $obj->sd);
		$act->SetCellValue('H13', 'Class GPA');
		$act->SetCellValue('H14', $obj->classGPA);

		$act->SetCellValue('C16', "Grade");
		$act->SetCellValue('D16', "Range");
		$act->SetCellValue('F16', "No. of Student");
		$act->SetCellValue('H16', "%");

		$len = count($obj->grade);
		for($i = 0; $i < $len; $i++){
			$ix = intval(17+$i);
			$act->mergeCells('D'.$ix.':E'.$ix);
			$act->mergeCells('F'.$ix.':G'.$ix);

			$act->SetCellValue('C'.$ix, $obj->grade[$i]);
			$act->SetCellValue('D'.$ix, $obj->range[$i]);
			$act->SetCellValue('F'.$ix, $obj->numGrade[$i]);
			$act->SetCellValue('H'.$ix, $obj->gPercen[$i]);

			$col = array('C'.$ix, 'D'.$ix, 'F'.$ix, 'H'.$ix);
			$this->setCenter($col, $act);
		}
		$ix++;
		$act->mergeCells('C'.$ix.':E'.$ix);
		$act->mergeCells('F'.$ix.':G'.$ix);

		$act->SetCellValue('C'.$ix, "Total");
		$act->SetCellValue('F'.$ix, $obj->student);
		$act->SetCellValue('H'.$ix, 100);

		$col = array('C'.$ix, 'F'.$ix, 'H'.$ix);
		$this->setCenter($col, $act);

		// set align center
		$col = array('A6','A7','A8','A9','A10','A11','B13','D13','E13','F13','G13','H13','B14','D14','E14','F14','G14','H14',
					'C16', 'D16', 'F16', 'H16');
		$this->setCenter($col, $act);
	}

	public function other($act)
	{
		$obj = $this->obj;

		// normal
		$act->mergeCells('A1:J1');
		$act->mergeCells('A2:J2');
		$act->mergeCells('A3:J3');
		$act->mergeCells('A4:J4');
		$act->mergeCells('A5:J5');

		// statistics
		$act->mergeCells('B7:C7');
		$act->mergeCells('B8:C8');
		$act->mergeCells('H7:I7');
		$act->mergeCells('H8:I8');

		// grade
		$act->mergeCells('D10:E10');
		$act->mergeCells('F10:G10');
		
		$act->SetCellValue('A1', 'รหัสวิชา '.$obj->subId);
		$act->SetCellValue('A2', 'ชื่อวิชา '.$obj->subjects);
		if($obj->level != ''){
			$act->SetCellValue('A3', 'ระดับชั้น'.$obj->level);
			$act->SetCellValue('A4', 'อาจารย์ผู้สอน '.$obj->instructor);
			$act->SetCellValue('A5', 'ภาคการศึกษา '.$obj->term);
		}else{
			$act->SetCellValue('A3', 'อาจารย์ผู้สอน '.$obj->instructor);
			$act->SetCellValue('A4', 'ภาคการศึกษา '.$obj->term);
		}
		
		$act->SetCellValue('B7', 'จำนวนนักศึกษา');
		$act->SetCellValue('B8', $obj->student);
		$act->SetCellValue('D7', 'คะแนนสูงสุด');
		$act->SetCellValue('D8', $obj->max);
		$act->SetCellValue('E7', 'คะแนนต่ำสุดสุด');
		$act->SetCellValue('E8', $obj->min);
		$act->SetCellValue('F7', 'ค่าเฉลี่ย');
		$act->SetCellValue('F8', $obj->mean);
		$act->SetCellValue('G7', 'ค่าเบี่ยงเบนมาตรฐาน');
		$act->SetCellValue('G8', $obj->sd);
		$act->SetCellValue('H7', 'เกรดเฉลี่ยของวิชานี้');
		$act->SetCellValue('H8', $obj->classGPA);

		$act->SetCellValue('C10', "เกรด");
		$act->SetCellValue('D10', "ช่วงคะแนน");
		$act->SetCellValue('F10', "จำนวนนักศึกษา");
		$act->SetCellValue('H10', "%");

		$col = array('C10', 'D10', 'F10', 'H10');
		$this->setCenter($col, $act);

		$len = count($obj->grade);
		for($i = 0; $i < $len; $i++){
			$ix = intval(11+$i);
			$act->mergeCells('D'.$ix.':E'.$ix);
			$act->mergeCells('F'.$ix.':G'.$ix);

			$act->SetCellValue('C'.$ix, $obj->grade[$i]);
			$act->SetCellValue('D'.$ix, $obj->range[$i]);
			$act->SetCellValue('F'.$ix, $obj->numGrade[$i]);
			$act->SetCellValue('H'.$ix, $obj->gPercen[$i]);

			$col = array('C'.$ix, 'D'.$ix, 'F'.$ix, 'H'.$ix);
			$this->setCenter($col, $act);
		}
		$ix++;
		$act->mergeCells('C'.$ix.':E'.$ix);
		$act->mergeCells('F'.$ix.':G'.$ix);

		$act->SetCellValue('C'.$ix, "รวม");
		$act->SetCellValue('F'.$ix, $obj->student);
		$act->SetCellValue('H'.$ix, 100);

		$col = array('C'.$ix, 'F'.$ix, 'H'.$ix);
		$this->setCenter($col, $act);

		$col = array('A1','A2','A3','A4','A5','B7','D7','E7','F7','G7','H7','B8','D8','E8','F8','G8','H8',
					'C16', 'D16', 'F16', 'H16');
		$this->setCenter($col, $act);
	}

	public function sheetGrade($type)
	{
		$obj = $this->obj;
		// create new sheet
		$this->objPHPExcel->createSheet();

		// sheet 2
		$this->objPHPExcel->setActiveSheetIndex(1);
		$act = $this->objPHPExcel->getActiveSheet();
		$act->setTitle('Grade');

		$act->getStyle('A:E')->getFont()->setName('TH SarabunPSK');
		$act->getStyle('A:E')->getFont()->setSize(16);

		$act->getColumnDimension('B')->setWidth("15");
		$act->getColumnDimension('C')->setWidth("35");
		$act->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		if($obj->place == "sut"){
			$act->SetCellValue('A1', 'No.');
			$act->SetCellValue('B1', 'ID');
			$act->SetCellValue('C1', 'Full Name');
			$act->SetCellValue('D1', 'Score');
			$act->SetCellValue('E1', 'Grade');
		}else{
			$act->SetCellValue('A1', 'ลำดับ');
			$act->SetCellValue('B1', 'รหัส');
			$act->SetCellValue('C1', 'ชื่อ-สกุล');
			$act->SetCellValue('D1', 'คะแนน');
			$act->SetCellValue('E1', 'เกรด');
		}

		$act->getStyle('A:B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$act->getStyle('D:E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$dlen = count($this->data);
		if($type == "first")
		{
			for($i = 0; $i < $dlen; $i++)
			{
				$act->setCellValue('A'.($i+2), ($i+1));
				$act->setCellValue('B'.($i+2), $this->data[$i][0]);
				$act->setCellValue('C'.($i+2), $this->data[$i][1]);
				$act->setCellValue('D'.($i+2), $this->data[$i][2]);
				$act->setCellValue('E'.($i+2), $this->grade[$i]);
			}
		}else{
			for($i = 0; $i < $dlen; $i++)
			{
				$act->setCellValue('A'.($i+2), ($i+1));
				$act->setCellValue('B'.($i+2), $this->data[$i][0]);
				$act->setCellValue('C'.($i+2), $this->data[$i][1]);
				$act->setCellValue('D'.($i+2), $this->data[$i][2]);
				$act->setCellValue('E'.($i+2), $this->data[$i][3]);
			}
		}
	}

	public function sheetCal()
	{
		$obj = $this->obj;
		// create new sheet
		$this->objPHPExcel->createSheet();

		// sheet 2
		$this->objPHPExcel->setActiveSheetIndex(2);
		$act = $this->objPHPExcel->getActiveSheet();
		$act->setTitle('Calculate');

		$act->getStyle('A:G')->getFont()->setName('TH SarabunPSK');
		$act->getStyle('A:G')->getFont()->setSize(16);
		$act->getStyle('A:G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		if($obj->place == "sut"){
			$act->getColumnDimension('C')->setWidth("15");
			$act->getColumnDimension('D')->setWidth("28");
			$act->getColumnDimension('F')->setWidth("12");

			$act->SetCellValue('A1', 'No.');
			$act->SetCellValue('B1', 'Score');
			$act->SetCellValue('C1', 'Frequency');
			$act->SetCellValue('D1', 'Commulative Frequency');
			$act->SetCellValue('E1', 'cf-0.5f');
			$act->SetCellValue('F1', 'Percentile');
			$act->SetCellValue('G1', 'T-score');
		}else{
			$act->getColumnDimension('D')->setWidth("12");
			$act->getColumnDimension('F')->setWidth("18");

			$act->SetCellValue('A1', 'ลำดับ');
			$act->SetCellValue('B1', 'คะแนน');
			$act->SetCellValue('C1', 'ความถี่');
			$act->SetCellValue('D1', 'ความถี่สะสม');
			$act->SetCellValue('E1', 'cf-0.5f');
			$act->SetCellValue('F1', 'เปอร์เซ็นต์ไทล์');
			$act->SetCellValue('G1', 'T-score');
		}
		
		foreach($obj->showCal as $i => $v){
			$act->setCellValue('A'.($i+2), ($i+1));
			$act->setCellValue('B'.($i+2), $v->scoreFreq);
			$act->setCellValue('C'.($i+2), $v->freq);
			$act->setCellValue('D'.($i+2), $v->cumFreq);
			$act->setCellValue('E'.($i+2), $v->cf5f);
			$act->setCellValue('F'.($i+2), $v->percentile);
			$act->setCellValue('G'.($i+2), $v->tscore);
		}
	}

	public function setCenter($col, $act)
	{
		foreach($col as $k => $v){
			$act->getStyle($v)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
	}
}

?>
