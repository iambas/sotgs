<?php

// if not found request "id"
if(!isset($_REQUEST['id'])){
    exit();
}else{
    if($_REQUEST['id'] == '') exit();

	// set json file
    $json = "files/" . $_REQUEST['id'] . ".json";
    if(!file_exists($json)) exit();
     
    // get contents
    $content = file_get_contents($json);
    $data = json_decode($content);

    if($data->place == 'other')	exit();

    $cnt = 0;
    $txtGrade = '';

	// set logo
    if ($data->institutes == "Engineering")
        $img = '<img src="assets/logo.png" width="95" height="95">';
    else
        $img = '<img src="assets/sut_logo.jpg" width="73" height="95">';

	
	// show Grade, Range, No. of Student, %
    foreach($data->range as $k => $v){
        if ($v == "0-101"){
            $v = "-";
        }
        $txtGrade .= "<tr>
                        <td></td>
                        <td height=\"25\" style=\"border: 1px solid #000; text-align: center;\">".$data->grade[$k]."</td>
                        <td style=\"border: 1px solid #000; text-align: center;\">$v</td>
                        <td style=\"border: 1px solid #000; text-align: center;\">".$data->numGrade[$k]."</td>
                        <td style=\"border: 1px solid #000; text-align: center;\">".$data->gPercen[$k]."</td>
                    </tr>" ;
        $cnt++;
    }
    
    $br = "";
    if($cnt == 5)   $br = "<br><br><br><br><br>";
    elseif($cnt == 6) $br = "<br><br><br>";

	// count freq score
    $cn = 0;
    $txtNum = '';
    $arrNum = array_fill(0, 101, 0);
    foreach($data->showCal as $k => $v){
        $arrNum[$v->scoreFreq] = $v->freq;
    }
    for($i = 91; $i <= 100; $i++){
        $cn += $arrNum[$i];
    }
    $txtNum .= "<tr>
                    <td align=\"right\">91-100</td>
                    <td width=\"10\"></td>
                    <td style=\"border-bottom: 1px solid #000; text-align: center;\">$cn</td>
                </tr>";
    for($i = 90; $i > 20; $i--){
        $txtNum .= "<tr>
                    <td align=\"right\">$i</td>
                    <td width=\"10\"></td>
                    <td style=\"border-bottom: 1px solid #000; text-align: center;\">".$arrNum[$i]."</td>
                </tr>";
    }    
    $cn = 0;
    for($i = 11; $i <= 20; $i++){
        $cn += $arrNum[$i];
    }
    $txtNum .= "<tr>
                    <td align=\"right\">11-20</td>
                    <td width=\"10\"></td>
                    <td style=\"border-bottom: 1px solid #000; text-align: center;\">$cn</td>
                </tr>";
    $cn = 0;
    for($i = 0; $i <= 10; $i++){
        $cn += $arrNum[$i];
    }
    $txtNum .= "<tr>
                    <td align=\"right\">1-10</td>
                    <td width=\"10\"></td>
                    <td style=\"border-bottom: 1px solid #000; text-align: center;\">$cn</td>
                </tr>";
}

// ---------------------------------------------------------
    

// Include the main TCPDF library (search for installation path).
require_once('TCPDF/tcpdf.php');

class MYPDF extends TCPDF {
	public function Header() {}
    public function Footer(){}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('');
$pdf->SetTitle('Score Distribution');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set margins
$marginTop = 10;
$marginBottom = 5;
$pdf->SetMargins(PDF_MARGIN_LEFT, $marginTop, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, $marginBottom);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'TCPDF/examples/lang/eng.php')) {
	require_once(dirname(__FILE__).'TCPDF/examples/lang/eng.php');
	$pdf->setLanguageArray($l);
}


// ---------------------------------------------------------

// add a page
$pdf->AddPage();

// set font
$pdf->SetFont('freeserif', '', 10);

// -----------------------------------------------------------------------------

$html = 
'<table border="0">
    <tr>
        <td width="70%">
            <br>
            <table width="630" border="0">
                <tr>
                    <td width="150"></td>
                    <td align="center" style="line-height: 90%;">
                        '.$img.'
                        <br>
                        <div style="font-size: 22px; font-weight: bold;">Score Distribution</div>
                        <div style="font-size: 20px;">Institute of '.$data->institutes.'</div>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td width="150" align="right">Term / Academic Year &nbsp;</td>
                    <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->term .'</td>
                </tr>
                <tr>
                    <td width="150" align="right"></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="right">School of &nbsp;</td>
                    <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->school .'</td>
                </tr>
                <tr>
                    <td align="right">Course Title &nbsp;</td>
                    <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->subId . " " . $data->subjects .'</td>
                </tr>
                <tr>
                    <td align="right">Number of Credit &nbsp;</td>
                    <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->credit .'</td>
                </tr>
                <tr>
                    <td align="right">Instructor &nbsp;</td>
                    <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->instructor .'</td>
                </tr>
                <tr>
                    <td align="right">Number of Student &nbsp;</td>
                    <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->student .'</td>
                </tr>
            </table>
            <br>
            <div style="text-align: center;">
                <table border="0" >
                    <tr>
                        <td></td>
                        <td align="right" width="150"><b>Making Scheme &nbsp;</b></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="right">Attendance &nbsp;</td>
                        <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->attendance .'</td>
                        <td align="left">&nbsp;%</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="right">Midterm Exam &nbsp;</td>
                        <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->midterm .'</td>
                        <td align="left">&nbsp;%</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="right">Assignment / Wrok &nbsp;</td>
                        <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->assignment .'</td>
                        <td align="left">&nbsp;%</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="right">Report &nbsp;</td>
                        <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->report .'</td>
                        <td align="left">&nbsp;%</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="right">Final Exam &nbsp;</td>
                        <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->final .'</td>
                        <td align="left">&nbsp;%</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="right">Total &nbsp;</td>
                        <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->total .'</td>
                        <td align="left">&nbsp;%</td>
                    </tr>
                </table>
                <br><br>
                <table border="0" align="center" cellpadding="5">
                    <tr>
                        <td></td>
                        <td width="50" style="border: 1px solid #000; text-align: center;"><b>Grade</b></td>
                        <td width="120" style="border: 1px solid #000; text-align: center;"><b>Range</b></td>
                        <td width="120" style="border: 1px solid #000; text-align: center;"><b>No. of Student</b></td>
                        <td width="60" style="border: 1px solid #000; text-align: center;"><b>%</b></td>
                    </tr>
                    '. $txtGrade .'
                    <tr>
                        <td colspan="3" align="right">Total &nbsp;</td>
                        <td style="border: 1px solid #000; text-align: center;">'. $data->student .'</td>
                        <td style="border: 1px solid #000; text-align: center;">100</td>
                    </tr>
                </table>
                <br>
                <table>
                    <tr>
                        <td align="right">Max. Score &nbsp;</td>
                        <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->max .'</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td align="right">Min. Score &nbsp;</td>
                        <td style="border-bottom: 1px solid #000; text-align: center;">'. $data->min .'</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td align="right">Mean &nbsp;</td>
                        <td style="border-bottom: 1px solid #000; text-align: center;">'. sprintf("%.2f", $data->mean) .'</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td align="right">SD &nbsp;</td>
                        <td style="border-bottom: 1px solid #000; text-align: center;">'. sprintf("%.2f", $data->sd) .'</td>
                        <td colspan="2"></td>
                    </tr>
                </table>
                <br><br>
                <table cellpadding="5">
                    <tr>
                        <td align="right"><b>Class GPA</b></td>
                        <td style="border: 1px solid #000; text-align: center;"><b>'. sprintf("%.2f", $data->classGPA) .'</b></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                '. $br .'
                <table>
                    <tr><td></td><td></td><td></td><td></td></tr>
                    <tr>
                        <td align="right">Signature &nbsp;</td>
                        <td colspan="2" style="border-bottom: 1px solid #000; text-align: center;"></td>
                        <td align="left">&nbsp;Instructor</td>
                    </tr>
                </table>
            </div>
        </td>
        <td width="30%">
            <br>
            <table border="0" style="font-size: 10.5px;" align="center">
                <tr>
                    <td width="25%" align="right">Score</td>
                    <td width="10"></td>
                    <td width="75%" align="center">No. of Student</td>
                </tr>
                '. $txtNum .'
            </table>
        </td>
    </tr>
</table>';

$pdf->writeHTML($html, true, false, true, false, '');

//Close and output PDF document
$name = "score_distribution-".$data->subId."-".$data->ty.".pdf";
$pdf->Output($name, 'I');

?>
